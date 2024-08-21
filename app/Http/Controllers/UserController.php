<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\EmailTemplateLang;
use App\Models\Mdf;
use App\Models\Notification;
use App\Models\User;
use App\Models\UsersOperatorsServices;
use App\Models\Utility;
use App\Models\Operator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\common\Utility as UserActivity;

class UserController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('Manage Users'))
        {
            $user  = Auth::user();
            $users = User::where('created_by', '=', $user->ownerId())->where('type', '!=', 'Client')->get();

            $activeUsers = User::where('created_by', '=', $user->ownerId())->where('type', '!=', 'Client')->Active()->count();

            foreach ($users as $key => $value) {
                $roles = Role::with('permissions')->where('name', '=', $value['type'])->first();
                $operatorsServices = UsersOperatorsServices::GetOperaterServiceByUserId($value['id'])->get();
                $operators = array_unique($operatorsServices->pluck('id_operator')->toArray());
                $country = Operator::filteroperator($operators)->get();
                $countries = array_unique($country->pluck('country_name')->toArray());

                $today = Carbon::now()->toDateTimeString();

                $users[$key]['permissions'] = $roles->permissions;
                $users[$key]['countries'] = $countries;
                $users[$key]['password_age'] = (isset($users[$key]['change_password_time']) && $users[$key]['change_password_time'] != '') ? Carbon::parse($users[$key]['change_password_time'])->diffInDays($today).' days' : 'N/A';
            }

            return view('users.index', compact('users','activeUsers'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('Create User'))
        {
            $user = \Auth::user();

            if($user->type == 'Super Admin')
            {
                return view('users.create');
            }
            else
            {
                $roles = Role::where('created_by', '=', $user->ownerId())->get();

                $customFields = CustomField::where('module', '=', 'user')->get();

                return view('users.create_owner', compact('roles', 'customFields'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('Create User'))
        {
            UserActivity::user_activity('Create User');

            $objUser = \Auth::user();
            $resp = '';
            $default_lang = Utility::getValByName('default_language');

            if($objUser->type == 'Super Admin')
            {
                $validator = \Validator::make(
                    $request->all(), 
                    [
                       'name' => 'required',
                       'email' => 'required|email|unique:users',
                       'password' => 'required',
                       'user_name' => 'required|unique:users',
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $user = User::create(
                    [
                        'name' => $request->name,
                        'user_name' => $request->user_name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'type' => 'Owner',
                        'lang' => $default_lang,
                        'created_by' => \Auth::user()->id,
                    ]
                );

                $adminRole = Role::findByName('Owner');
                $user->assignRole($adminRole);
                $user->userDefaultData();
                $user->makeEmployeeRole();
            }
            else
            {
                $validator = \Validator::make(
                    $request->all(), 
                    [
                       'name' => 'required',
                       'email' => 'required|email|unique:users',
                       'confirm_password' => 'required',
                       'password' => 'required_with:confirm_password|same:confirm_password',
                       'role' => 'required',
                       'user_name' => 'required|unique:users',
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $role = Role::findById($request->role);
                $user = User::create(
                    [
                        'name' => $request->name,
                        'user_name' => $request->user_name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'type' => $role->name,
                        'created_by' => $objUser->ownerId(),
                        'lang' => $default_lang,
                    ]
                );

                $user->assignRole($role);

                CustomField::saveData($user, $request->customField);

                $uArr = [
                    'email' => $user->email,
                    'password' => $request->password,
                ];

                // Send Email
                //$resp = Utility::sendEmailTemplate('New User', [$user->id => $user->email], $uArr);
            }

            return redirect()->route('users')->with('success', __('User created Successfully!') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($user_id)
    {
        $usr  = Auth::user();
        $user = User::find($user_id);
        $OperaterServices = UsersOperatorsServices::GetOperaterServiceByUserId($user_id)->get()->groupBy('id_operator');
        
        return view('users.show', compact('user','OperaterServices'));
    }

    public function edit($id)
    {
        if(\Auth::user()->can('Edit User'))
        {
            $user = User::where('id', '=', $id)->where('created_by', '=', \Auth::user()->ownerId())->first();

            if($user)
            {
                $objUser = \Auth::user();

                if($objUser->type == 'Super Admin')
                {
                    return view('users.edit', compact('user'));
                }
                else
                {
                    $roles = Role::where('created_by', '=', $user->ownerId())->get();
                    $userRole = $user->roles->first();

                    if($userRole)
                    {
                        $userRole = $userRole->id;
                    }
                    else
                    {
                        $userRole = '';
                    }

                    $user->customField = CustomField::getData($user, 'user');
                    $customFields = CustomField::where('module', '=', 'user')->get();

                    return view('users.edit_owner', compact('roles', 'userRole', 'user', 'customFields'));
                }
            }
            else
            {
                return response()->json(['error' => __('Invalid User.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update($id, Request $request)
    {
        if(\Auth::user()->can('Edit User'))
        {
            $user = User::where('id', '=', $id)->where('created_by', '=', \Auth::user()->ownerId())->first();

            if($user)
            {
                $objUser = \Auth::user();
                
                if($objUser->type == 'Super Admin')
                {
                    $validator = \Validator::make(
                        $request->all(), 
                        [
                           'name' => 'required',
                           'email' => 'required|email|unique:users,email,' . $id,
                           'user_name' => 'required|unique:users,user_name,'. $id,
                        ]
                    );
                    
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('users')->with('error', $messages->first());
                    }

                    $post         = [];
                    $post['name'] = $request->name;

                    if(!empty($request->password))
                    {
                        $validation['password'] = 'required';
                        $post['password']       = Hash::make($request->password);
                    }

                    $post['email'] = $request->email;

                    $user->update($post);
                }
                else
                {
                    $validator = \Validator::make(
                        $request->all(), 
                        [
                           'name' => 'required',
                           'email' => 'required|email|unique:users,email,' . $id,
                           'role' => 'required',
                           'user_name' => 'required|unique:users,user_name,'. $id,
                        ]
                    );

                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('users')->with('error', $messages->first());
                    }
                    
                    $post = [];
                    $post['name'] = $request->name;
                    $post['user_name'] = $request->user_name;
                    $post['email'] = $request->email;
                    $post['type'] = Role::findById($request->role)->name;
                    $user->update($post);
                    $roles[] = $request->role;
                    $user->roles()->sync($roles);
                    CustomField::saveData($user, $request->customField);

                    UserActivity::user_activity('Edit User');
                }

                return redirect()->route('users')->with('success', __('User Updated Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid User.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->can('Delete User'))
        {
            UserActivity::user_activity('Delete User');

            $user = User::where('id', '=', $id)->where('created_by', '=', \Auth::user()->ownerId())->first();

            if($user)
            {
                Notification::where('user_id', '=', $user->id)->delete();

                $user->delete();

                return redirect()->route('users')->with('success', __('User Deleted Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid User.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function status($id)
    {
        if(\Auth::user()->can('Edit User'))
        {
            UserActivity::user_activity('Change User Status');

            $user = User::find($id);

            if($user)
            {
                $user->is_active = ($user->is_active == 1) ? 0 : 1;
                $user->save();

                return redirect()->route('users')->with('success', __('User Status change Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid User.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function profile()
    {
        $user = Auth::user();

        return view('users.profile', compact('user'));
    }

    public function deleteAvatar()
    {
        $objUser = Auth::user();
        \File::delete(storage_path('avatars/' . $objUser->avatar));
        $objUser->avatar = '';
        $objUser->save();

        return redirect()->route('profile')->with('success', __('Avatar deleted successfully'));
    }

    public function updateProfile(Request $request)
    {
        $objUser             = Auth::user();
        $validation          = [];
        $validation['name']  = 'required';
        $validation['email'] = 'required|email|unique:users,email,' . Auth::user()->id;

        if($request->avatar)
        {
            $validation['avatar'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $request->validate($validation);

        $post = $request->all();

        if($request->avatar)
        {
            \File::delete(storage_path('avatars/' . $objUser->avatar));
            $avatarName = $objUser->id . '_avatar' . time() . '.' . $request->avatar->getClientOriginalExtension();
            $request->avatar->storeAs('avatars', $avatarName);
            $post['avatar'] = $avatarName;
        }

        $objUser->update($post);

        UserActivity::user_activity('Update Profile');

        return redirect()->back()->with('success', __('Profile Updated Successfully!'));
    }

    public function userpassword(Request $request)
    {
        if(Auth::Check())
        {
            UserActivity::user_activity('Update Password');

            $request->validate(
                [
                    'old_password' => 'required',
                    'password' => 'required|same:password',
                    'password_confirmation' => 'required|same:password',
                ]
            );

            $objUser = User::find($request->id);
            $request_data = $request->All();
            $current_password = $objUser->password;

            if(Hash::check($request_data['old_password'], $current_password))
            {
                $user_id = Auth::User()->id;
                $obj_user = User::find($request->id);
                $obj_user->password = Hash::make($request_data['password']);;
                $obj_user->save();

                return redirect()->back()->with('success', __('Password Updated Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please Enter Correct Current Password!'));
            }
        }
        else
        {
            return redirect()->route('profile')->with('error', __('Something is wrong!'));
        }
    }

    public function updatePassword(Request $request)
    {
        if(Auth::Check())
        {
            UserActivity::user_activity('Update Password');

            $request->validate(
                [
                    'old_password' => 'required',
                    'password' => 'required|same:password',
                    'password_confirmation' => 'required|same:password',
                ]
            );

            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;

            if(Hash::check($request_data['old_password'], $current_password))
            {
                $user_id            = Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['password']);
                $obj_user->change_password_time = Carbon::now()->format('Y-m-d');
                $obj_user->save();

                return redirect()->route('profile')->with('success', __('Password Updated Successfully!'));
            }
            else
            {
                return redirect()->route('profile')->with('error', __('Please Enter Correct Current Password!'));
            }
        }
        else
        {
            return redirect()->route('profile')->with('error', __('Something is wrong!'));
        }
    }

    public function lang($currantLang)
    {
        if(\Auth::user()->can('Manage Languages'))
        {
            $user = \Auth::user();

            $dir = base_path() . '/resources/lang/' . $user->id . "/" . $currantLang;

            if(!is_dir($dir))
            {
                $dir = base_path() . '/resources/lang/' . $currantLang;

                if(!is_dir($dir))
                {
                    $dir = base_path() . '/resources/lang/en';
                }
            }

            $arrLabel = json_decode(file_get_contents($dir . '.json'));

            $arrFiles   = array_diff(
                scandir($dir),
                array(
                    '..',
                    '.',
                )
            );

            $arrMessage = [];

            foreach($arrFiles as $file)
            {
                $fileName = basename($file, ".php");
                $fileData = $myArray = include $dir . "/" . $file;

                if(is_array($fileData))
                {
                    $arrMessage[$fileName] = $fileData;
                }
            }

            return view('lang.index', compact('currantLang', 'arrLabel', 'arrMessage'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function createLang()
    {
        if(\Auth::user()->can('Create Language'))
        {
            return view('lang.create');
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function storeLang(Request $request)
    {
        if(\Auth::user()->can('Create Language'))
        {
            $Filesystem = new Filesystem();
            $langCode = strtolower($request->code);
            $langDir = base_path() . '/resources/lang/';
            $dir = $langDir;

            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }

            $dir = $dir . '/' . $langCode;
            $jsonFile = $dir . ".json";
            \File::copy($langDir . 'en.json', $jsonFile);

            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }

            $Filesystem->copyDirectory($langDir . "en", $dir . "/");

            // make entry in email_tempalte_lang table for email template content
            Utility::makeEmailLang($langCode);

            return redirect()->route('lang', $langCode)->with('success', __('Language Created Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function storeLangData($currantLang, Request $request)
    {
        if(\Auth::user()->can('Edit Language'))
        {
            $dir = base_path() . '/resources/lang';

            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }

            $jsonFile = $dir . "/" . $currantLang . ".json";

            file_put_contents($jsonFile, json_encode($request->label));

            $langFolder = $dir . "/" . $currantLang;

            if(!is_dir($langFolder))
            {
                mkdir($langFolder);
                chmod($langFolder, 0777);
            }

            if(!empty($request->message))
            {
                foreach($request->message as $fileName => $fileData)
                {
                    $content = "<?php return [";
                    $content .= $this->buildArray($fileData);
                    $content .= "];";
                    file_put_contents($langFolder . "/" . $fileName . '.php', $content);
                }
            }

            return redirect()->route('lang', $currantLang)->with('success', __('Language Save Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroyLang($lang)
    {
        $settings = Utility::settings();
        $default_lang = $settings['default_language'];

        // Remove Email Template Language
        EmailTemplateLang::where('lang', 'LIKE', $lang)->delete();

        $langDir = base_path() . '/resources/lang/';

        if(is_dir($langDir))
        {
            // remove directory and file
            Utility::delete_directory($langDir . $lang);
            unlink($langDir . $lang . '.json');

            // update user that has assign deleted language.
            User::where('lang', 'LIKE', $lang)->update(['lang' => $default_lang]);
        }

        return redirect()->route('lang', $default_lang)->with('success', __('Language Deleted Successfully!'));
    }

    public function buildArray($fileData)
    {
        $content = "";

        foreach($fileData as $lable => $data)
        {
            if(is_array($data))
            {
                $content .= "'$lable'=>[" . $this->buildArray($data) . "],";
            }
            else
            {
                $content .= "'$lable'=>'" . addslashes($data) . "',";
            }
        }

        return $content;
    }

    public function changeLang($lang)
    {
        $user = \Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }

    // public function search(Request $request)
    // {
    //     $html   = '';
    //     $usr    = Auth::user();
    //     $type   = $usr->type;
    //     $search = $request->keyword;

    //     if(!empty($search))
    //     {
    //         if($type == 'Client')
    //         {
    //             $objDeal = Deal::select(
    //                 [
    //                     'deals.id',
    //                     'deals.name',
    //                 ]
    //             )->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->whereRaw('FIND_IN_SET(' . $usr->id . ',client_deals.client_id)')->where('deals.name', 'LIKE', $search . "%")->get();

    //             $html .= '<li>
    //                         <span class="list-link">
    //                             <i class="fas fa-search"></i>' . __('Deals') . '
    //                         </span>
    //                     </li>';
    //             if($objDeal->count() > 0)
    //             {
    //                 foreach($objDeal as $deal)
    //                 {
    //                     $html .= '<li>
    //                         <a class="list-link pl-4" href="' . route('deals.show', $deal->id) . '">
    //                             <span>' . $deal->name . '</span>
    //                         </a>
    //                     </li>';
    //                 }
    //             }
    //             else
    //             {
    //                 $html .= '<li>
    //                             <a class="list-link pl-4" href="#">
    //                                 <span>' . __('No Deals Found.') . '</span>
    //                             </a>
    //                         </li>';
    //             }
    //         }
    //         else
    //         {
    //             // Deal Wise Searching
    //             $objDeal = Deal::select(
    //                 [
    //                     'deals.id',
    //                     'deals.name',
    //                 ]
    //             )->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', $usr->id)->where('deals.name', 'LIKE', $search . "%")->get();

    //             $html .= '<li>
    //                         <span class="list-link">
    //                             <i class="fas fa-search"></i>' . __('Deals') . '
    //                         </span>
    //                     </li>';
    //             if($objDeal->count() > 0)
    //             {
    //                 foreach($objDeal as $deal)
    //                 {
    //                     $html .= '<li>
    //                         <a class="list-link pl-4" href="' . route('deals.show', $deal->id) . '">
    //                             <span>' . $deal->name . '</span>
    //                         </a>
    //                     </li>';
    //                 }
    //             }
    //             else
    //             {
    //                 $html .= '<li>
    //                             <a class="list-link pl-4" href="#">
    //                                 <span>' . __('No Deals Found.') . '</span>
    //                             </a>
    //                         </li>';
    //             }
    //             // Deal Wise Searching end

    //             // Task Wise Searching
    //             $objTask = Deal::select(
    //                 [
    //                     'deal_tasks.id',
    //                     'deal_tasks.name',
    //                     'deals.id AS deal_id',
    //                 ]
    //             )->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->join('deal_tasks', 'deal_tasks.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', $usr->id)->where('deal_tasks.name', 'LIKE', $search . "%")->get();

    //             $html .= '<li>
    //                         <span class="list-link">
    //                             <i class="fas fa-search"></i>' . __('Tasks') . '
    //                         </span>
    //                     </li>';
    //             if($objTask->count() > 0)
    //             {
    //                 foreach($objTask as $task)
    //                 {
    //                     $html .= '<li>
    //                         <a class="list-link pl-4" href="' . route('deals.show', $task->deal_id) . '">
    //                             <span>' . $task->name . '</span>
    //                         </a>
    //                     </li>';
    //                 }
    //             }
    //             else
    //             {
    //                 $html .= '<li>
    //                             <a class="list-link pl-4" href="#">
    //                                 <span>' . __('No Tasks Found.') . '</span>
    //                             </a>
    //                         </li>';
    //             }
    //             // Task Wise Searching End

    //             // Lead Wise Searching
    //             $objLead = Lead::select(
    //                 [
    //                     'leads.id',
    //                     'leads.name',
    //                 ]
    //             )->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', $usr->id)->where('leads.name', 'LIKE', $search . "%")->get();

    //             $html .= '<li>
    //                         <span class="list-link">
    //                             <i class="fas fa-search"></i>' . __('Leads') . '
    //                         </span>
    //                     </li>';
    //             if($objLead->count() > 0)
    //             {
    //                 foreach($objLead as $lead)
    //                 {
    //                     $html .= '<li>
    //                         <a class="list-link pl-4" href="' . route('leads.show', $lead->id) . '">
    //                             <span>' . $lead->name . '</span>
    //                         </a>
    //                     </li>';
    //                 }
    //             }
    //             else
    //             {
    //                 $html .= '<li>
    //                             <a class="list-link pl-4" href="#">
    //                                 <span>' . __('No Leads Found.') . '</span>
    //                             </a>
    //                         </li>';
    //             }
    //             // Lead Wise Searching End
    //         }
    //     }
    //     else
    //     {
    //         $html .= '<li>
    //                     <a class="list-link pl-4" href="#">
    //                     <i class="fas fa-search"></i>
    //                         <span>' . __('Type and search By Deal, Lead and Tasks.') . '</span>
    //                     </a>
    //                   </li>';
    //     }

    //     print_r($html);
    // }

    public function notificationSeen($user_id)
    {
        Notification::where('user_id', '=', $user_id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }

    public function error(){
        return view('users.error');
    }
}
