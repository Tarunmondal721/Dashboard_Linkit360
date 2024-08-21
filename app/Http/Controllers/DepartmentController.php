<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Deal;
use App\Models\EmailTemplateLang;
use App\Models\Lead;
use App\Models\Mdf;
use App\Models\Notification;
use App\Models\User;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Departmentstafflist;
use DB;
use App\Models\UserDeal;
use App\Models\Utility;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DepartmentController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    public function department()
    {
        $user = [];
        $user  = Auth::user();
        if($user->type == 'Admin'){
            $org_code = $user->org_code;
            $department = Department::orderBy('created_at','desc')->where('created_by', '=', $user->id)->where('org_code', '=', $org_code)->orderBy('id','desc')->get();
        }else{
            $department = Department::orderBy('created_at','desc')->orderBy('id','desc')->get(); 
        }

        return view('department.listview', compact('department'));
    }

    public function create()
    {
        // if(\Auth::user()->can('Create Lead'))
        // {
            $user = [];
            $user  = Auth::user();
            if($user->type == 'Admin'){ 
                $org_code = $user->org_code;
                $organization  = Organization::where('org_code', '=', $org_code)->get()->toArray();
                $stafflist = User::where('type', '=', 'User')->where('org_code', '=', $org_code)->get()->toArray();          
            }else{
                $organization  = Organization::get();
                $stafflist = User::where('type', '=', 'User')->get()->toArray();
            }
              // print_r($stafflist);die('++++'); 
            return view('department.create', compact('organization','user','stafflist'));
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
    }

    public function store(Request $request)
    {
        // if(\Auth::user()->can('Create User'))
        // {
        // print_r($request->all());die('+++');

            $objUser      = \Auth::user();
            $resp         = '';
            $default_lang = Utility::getValByName('default_language');
   
            $validator = \Validator::make(
                $request->all(), [
                   'dept_id' => 'required',
                   'dept_name' => 'required',
                   'org_code' => 'required',
               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('department.create')->with('error', $messages->first());
            }
            if ($request->selected_staff !='') {
                $staffs = $request->selected_staff;
            }else{
                $staffs = null;
            }
            // print_r($staffs);die('+++');
            $user = Department::create(
                [
                    'dept_id' => $request->dept_id,
                    'dept_name' => $request->dept_name,
                    'org_code' => $request->org_code,
                    'staff_list' => $staffs,
                    'created_by' => $objUser->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );

            if (isset($request->selected_staff) && !empty($request->selected_staff)) {
                $stafflist = explode(',', $request->selected_staff);

                foreach ($stafflist as $key => $value)
                {
                    $userstaff = User::where('staff_id', '=', $value)->first();

                    $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                    $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                    $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                    $staff_name =  $fname.$mid_name.$lname;
                    $user = Departmentstafflist::create(
                        [
                            'dept_id' => $request->dept_id,
                            'org_code' => $request->org_code,
                            'staff_id' => $value,
                            'staff_name' => $staff_name,
                            'assign_role_name' => 'Staff',
                            'assign_role_id' => '0001',
                            'year' => date("Y"),
                            'created_by' => $objUser->id,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }

            return redirect()->route('department')->with('success', __('Department created Successfully!') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function edit($dept_id)
    {
        // print_r($dept_id);die('++++++');
        // if(\Auth::user()->can('Edit User'))
        // {
            $user = \Auth::user();
            $departmentlist = array();
            $departmentlist['activedepartmentstaff'] = array();
            $departmentlist['departmentallstaff']=array();
        
            $dept_user_organization = '';
            $departmentstafflist = Departmentstafflist::select('*')->where('dept_id',$dept_id)->get()->toArray();

            foreach ($departmentstafflist as $key => $value) {
                $departmentlist['departmentallstaff'][$key] = $value['staff_id'];
            }

            $alreadyselected =[];
            $allstafflist_staff_id =[];
            // print_r($departmentlist);die('++++++');
            if($user->type == 'Admin'){
                $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->first();
                $departmentlist['allstafflist'] = User::where('type', '=', 'User')->where('org_code', '=', $department['org_code'])->get()->toArray();
                // print_r($departmentlist['allstafflist']);die('+++');
                // $departmentlist['allstafflist_staff_id'] = User::select('staff_id')->where('type', '=', 'User')->where('org_code', '=', $department['org_code'])->get()->toArray();

                foreach ($departmentlist['allstafflist'] as $key => $staffs) {
                    if(in_array($staffs['staff_id'],$departmentlist['departmentallstaff'])){
                        array_push($alreadyselected,$staffs['staff_id']);
                    }
                    $departmentlist['departmentallstaffsss'] = $alreadyselected;

                    array_push($allstafflist_staff_id,$staffs['staff_id']);
                    $departmentlist['allstafflist_staff_id'] = $allstafflist_staff_id;
                    // $departmentlist['allstafflistssss'] = User::where('type', '=', 'User')->where('staff_id', '=', $departmentlist['departmentallstaffsss'])->get()->toArray();
                }
                // echo "<pre>"; print_r($departmentlist['departmentallstaffsss']);
                // echo "<pre>"; print_r($departmentlist['allstafflist_staff_id']);
                // die('++++++');
                foreach ($departmentlist['departmentallstaffsss'] as $key => $departmentAdded_Staff_id) {

                    if (($key = array_search($departmentAdded_Staff_id, $departmentlist['allstafflist_staff_id'])) !== false) {
                        unset($departmentlist['allstafflist_staff_id'][$key]);
                    }
                    // print_r($value);die('++++++');
                    $departmentlist['departmentStaffAlreadySelected'][$departmentAdded_Staff_id] = User::where('type', '=', 'User')->where('staff_id', '=', $departmentAdded_Staff_id)->first()->toArray();
                }

                foreach ($departmentlist['allstafflist_staff_id'] as $key => $allstafflist_staff_ids) {
                    // print_r($value);die('++++++');
                    $departmentlist['unSelectedStafflist'][$allstafflist_staff_ids] = User::where('type', '=', 'User')->where('staff_id', '=', $allstafflist_staff_ids)->first()->toArray();
                }
                // echo "<pre>"; print_r($departmentlist['unSelectedStafflist']);die('++++++');

                $org_code = $user->org_code;
                $organization  = Organization::where('org_code', '=', $org_code)->get()->toArray();
            }else{
                $department = Department::where('dept_id', '=', $dept_id)->first();
                $departmentlist['allstafflist'] = User::where('type', '=', 'User')->get()->toArray();
                $organization    = Organization::get();
                // print_r($department);die('++++++');
                $dept_user_organization = $department->org_code;
            }  
            // print_r($department);die('++++++');
            return view('department.edit', compact('user','department','organization','dept_user_organization','departmentlist'));
            
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
    }

    public function viewlistuser($dept_id)
    {
        // print_r($dept_id);die('++++++');
        // if(\Auth::user()->can('Edit User'))
        // {
        $user = \Auth::user();
        $departmentlist = array();
        $departmentlist['activedepartmentstaff'] = array();
        $departmentlist['departmentallstaff']=array();

        $dept_user_organization = '';

        $departmentstafflist = Departmentstafflist::select('*')->where('dept_id',$dept_id)->get()->toArray();

        foreach ($departmentstafflist as $key => $value) {
                        
            $userstaff = User::where('staff_id', '=', $value['staff_id'])->first();
            if(isset($userstaff) && !empty($userstaff)){
                $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                $staff_name =  $fname.$mid_name.$lname;
                $data['stafflist'][$value['staff_id']] = $fname.$mid_name.$lname;
            }
            
            // print_r($data);die('+++');            
        }
        // print_r($data);exit;

        if(isset($departmentstafflist) && !empty($departmentstafflist)){
            foreach ($departmentstafflist as $key => $value) {
                // $departmentlist['departmentallstaff'][$key] = $value['staff_id'];
                $departmentlist['departmentallstaff'][$key] = $value['staff_name'];
            }
        }
        // print_r($departmentlist);die('++++++');
        // foreach ($departmentstafflist as $key => $value) {
        //     $departmentlist['departmentallstaff'][$key] = $value['staff_id'];
        // }
        if($user->type == 'Admin'){
            $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->first();

            // $departmentlist['allstafflist'] = User::where('type', '=', 'User')->where('org_code', '=', $department['org_code'])->get()->toArray();
            // $org_code = $user->org_code;
            // $organization  = Organization::where('org_code', '=', $org_code)->get()->toArray();
        }else{
            $department = Department::where('dept_id', '=', $dept_id)->first();
            // $departmentlist['allstafflist'] = User::where('type', '=', 'User')->get()->toArray();
            // $organization    = Organization::get();
            // // print_r($department);die('++++++');
            // $dept_user_organization = $department->org_code;           
        }         
            // $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->first();
            // if($department)
            // {
                
            //     if($user->type == 'Admin')
            //     {
                    
            //     }
            //     else
            //     {
                    
            //     }
            
            // }
        // print_r($departmentlist);die('++++++');
            return view('department.viewlistuser', compact('user','department','data'));
            // else
            // {
            //     return response()->json(['error' => __('Invalid Department.')], 401);
            // }
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
    }

     public function update(Request $request)
    {
        // print_r($request->all());die('++++++');

        // if(\Auth::user()->can('Edit User'))
        // {
            $dept_id = $request['dept_id'];
            $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', Auth::user()->id)->first();


            if($department)
            {
                $objUser = \Auth::user();
                $validator = \Validator::make(
                    $request->all(), [
                        'dept_id' => 'required',
                        'dept_name' => 'required',
                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('department.edit',$dept_id)->with('error', $messages->first());
                }
                
                if ($request->selected_staff !='') {
                    $staffs = $request->selected_staff;
                }elseif ($request->checkall != '') {
                    $staffs = implode(',',$request->checkall);
                    // print_r($staffs);die('+++');
                    // $staffs = $request->checkall;
                }
                else{
                    $staffs = null;
                }
                // print_r($staffs);die('+++');

                $post = [];
                $post['dept_name'] = $request->dept_name;
                $post['staff_list'] = $staffs;
                $post['created_by'] = $objUser->id;
                $post['updated_at'] = date('Y-m-d H:i:s');
                // print_r($post);die('++++++');
                $department->update($post);
                if (isset($request->selected_staff) && !empty($request->selected_staff)) 
                {
                    // die('>>>');
                    $stafflist = explode(',', $request->selected_staff);
                    Departmentstafflist::where('dept_id', '=', $dept_id)->delete();
                    // print_r($stafflist);die('iiiiiiiii');
                    foreach ($stafflist as $key => $value)
                    {
                        // print_r($key);die('iiiiiiiii');

                        $userstaff = User::where('staff_id', '=', $value)->first();

                        $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                        $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                        $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                        $staff_name =  $fname.$mid_name.$lname;
                        $user = Departmentstafflist::create(
                            [
                                'dept_id' => $request->dept_id,
                                'org_code' => $department['org_code'],
                                'staff_id' => $value,
                                'staff_name' => $staff_name,
                                'assign_role_name' => 'Staff',
                                'assign_role_id' => '0001',
                                'year' => date("Y"),
                                'created_by' => $objUser->id,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]
                        );
                    }
                }
                if($staffs == null){
                    // $stafflist = explode(',', $request->selected_staff);
                    Departmentstafflist::where('dept_id', '=', $dept_id)->delete();
                }
            }



            return redirect()->route('department')->with('success', __('Department Updated Successfully!'));
            // }
            // else
            // {
            //     return redirect()->back()->with('error', __('Invalid Department.'));
            // }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function destroy($dept_id)
    {
        
        // if(\Auth::user()->can('Delete User'))
        // {
            // $user = User::where('id', '=', $id)->where('created_by', '=', \Auth::user()->ownerId())->first();

            $department = Department::where('dept_id', '=', $dept_id)->first();
            if($department)
            {
                $departmentstafflist = Departmentstafflist::where('dept_id', '=', $dept_id)->get();
                if($departmentstafflist != ''){
                    Departmentstafflist::where('dept_id', '=', $dept_id)->delete();
                    $department->delete();
                }
                // Department::where('id', '=', $department->id)->delete();
                // Notification::where('user_id', '=', $user->id)->delete();
                // print_r($dept_id);die('+++');
                
                return redirect()->route('department')->with('success', __('Department Deleted Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid Department.'));
            }
        // }
        // else
        // {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function assignUserRole_backup($dept_id)
    {
        // print_r($dept_id);die('+++');
        
        // if(\Auth::user()->can('Edit User'))
        // {
        $supervisor_id = '';
        $officer_id = '';
        $data =[];
        $objUser = \Auth::user();
        if($objUser->type == 'Admin')
        {
            $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->first();
            // print_r($department['staff_list']);die('+++');
            if(isset($department) && !empty($department))
            {
                $org_code = $objUser->org_code;
                if(($department['staff_list']!='') || ($department['staff_list'] !=null)){
                    $stafflist = explode(',', $department['staff_list']);
                    // if($department['supervisor_id']!=''){
                    //     $supervisor_id = $department['supervisor_id'];
                        
                    // }
                    // if($department['officer_id']!=''){
                    //     $officer_id = $department['officer_id'];
                    // }
                    
                    foreach ($stafflist as $key => $value) {
                        $userstaff = User::where('staff_id', '=', $value)->first();

                        $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                        $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                        $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                        $staff_name =  $fname.$mid_name.$lname;
                        $data['stafflist'][$value] = $fname.$mid_name.$lname;
                        // print_r($data);
                    }
                    // die('+++');
                    // print_r($department['supervisor_id']);
                    // print_r($officer_id);
                    // die('+++');  
                }
            }else
            {
                return response()->json(['error' => __('Something Wrong!!.')], 401);
            }
        }else{
            $department = Department::where('dept_id', '=', $dept_id)->first();
            if(isset($department) && !empty($department))
            {
                $org_code = $objUser->org_code;
                if(($department['staff_list']!='') || ($department['staff_list'] !=null)){
                    $stafflist = explode(',', $department['staff_list']);
                    if($department['supervisor_id']!=''){
                        $supervisor_id = $department['supervisor_id'];
                        
                    }
                    if($department['officer_id']!=''){
                        $officer_id = $department['officer_id'];
                    }
                    // print_r($stafflist);die('+++');
                    foreach ($stafflist as $key => $value) {
                        $userstaff = User::where('staff_id', '=', $value)->first();
                        $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                        $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                        $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                        $staff_name =  $fname.$mid_name.$lname;
                        $data['stafflist'][$value] = $fname.$mid_name.$lname;
                    }
                    // print_r($department['supervisor_id']);
                    // print_r($officer_id);
                    // die('+++');  
                }
            }else
            {
                return response()->json(['error' => __('Something Wrong!!.')], 401);
            }
        }
            
        return view('department.assignUserRole', compact('department','data','supervisor_id','officer_id'));
        
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
    }

    public function assignUserstore_backup(Request $request)
    {
        // print_r($request->all());die('+++');
        $input = $request->all();
        $user      = \Auth::user();
        $validator = \Validator::make(
            $request->all(), [
               'supervisor_name' => 'required',
               'officer_name' => 'required',
           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('department')->with('error', $messages->first());
        }
        $dept_id = $input['dept_id'];
        $org_code = $input['dept_org_code'];
        $supervisor_id = $input['supervisor_name'];
        $officer_id = $input['officer_name'];
        $user_supervisor_details = User::select('*')->where('staff_id',$supervisor_id)->first()->toArray();

        $fname = ($user_supervisor_details['fname'] != "") ? $user_supervisor_details['fname'].' ' : '';
        $mid_name = ($user_supervisor_details['mid_name'] != "") ? $user_supervisor_details['mid_name'].' ' : '';
        $lname = ($user_supervisor_details['lname'] != "") ? $user_supervisor_details['lname'].' ' : '';
        $supervisor_name =  $fname.$mid_name.$lname;

        $user_officer_details = User::select('*')->where('staff_id',$officer_id)->first()->toArray();

        $o_fname = ($user_officer_details['fname'] != "") ? $user_officer_details['fname'].' ' : '';
        $o_mid_name = ($user_officer_details['mid_name'] != "") ? $user_officer_details['mid_name'].' ' : '';
        $o_lname = ($user_officer_details['lname'] != "") ? $user_officer_details['lname'].' ' : '';
        $officer_name =  $o_fname.$o_mid_name.$o_lname;

        if(isset($supervisor_id) && !empty($supervisor_id) && isset($officer_id) && !empty($officer_id)){
            $Departmentstafflist = Departmentstafflist::where('dept_id', '=', $dept_id)->get()->toArray();
            // print_r($Departmentstafflist);die('+++++++');
            foreach ($Departmentstafflist as $key => $value) {
                $post = [];
                $post['staff_role'] = 'supervisor';
                $post['supervisor_id'] = $supervisor_id;
                $post['supervisor_name'] = $supervisor_name;
                $post['officer_id'] = $officer_id;
                $post['officer_name'] = $officer_name;
                // print_r($post);die('+++++++');
                DB::table('departments')->where(array('dept_id'=>$dept_id))->update(
                    [
                    'officer_id' => $post['officer_id'],
                    'officer_name' =>  $post['officer_name'],
                    'supervisor_id' => $post['supervisor_id'],
                    'supervisor_name' =>  $post['supervisor_name'] ]);
                DB::table('department_assign_staff')->where(array('dept_id'=>$dept_id))->update(
                    [
                    'officer_id' => $post['officer_id'],
                    'officer_name' =>  $post['officer_name'],
                    'supervisor_id' => $post['supervisor_id'],
                    'supervisor_name' =>  $post['supervisor_name'] ]);
            }
        }

        if(isset($supervisor_id) && !empty($supervisor_id) && isset($officer_id) && !empty($officer_id)){
            $Departmentstafflist_officer = Departmentstafflist::where('dept_id', '=', $dept_id)->where('staff_id', '=', $officer_id)->first();
            if($Departmentstafflist_officer)
            {
                $post = [];
                $post['staff_role'] = 'officer';
                $Departmentstafflist_officer->update($post);
            }
            $Departmentstafflist_supervisor = Departmentstafflist::where('dept_id', '=', $dept_id)->where('staff_id', '=', $supervisor_id)->first();
            if($Departmentstafflist_supervisor)
            {
                $postnew = [];
                $postnew['staff_role'] = 'supervisor';
                $Departmentstafflist_supervisor->update($postnew);
            }
        }else{

        }
                    
        return redirect()->route('department')->with('success', __('Department Updated Successfully!'));   
    }

    public function assignUserRole($dept_id)
    {
        // print_r($dept_id);die('+++');
        
        // if(\Auth::user()->can('Edit User'))
        // {
        $supervisor_id = '';
        $officer_id = '';
        $data =[];
        $objUser = \Auth::user();
        if($objUser->type == 'Admin')
        {
            $department = Department::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->first();
            $departmentstafflist = Departmentstafflist::where('dept_id', '=', $dept_id)->where('created_by', '=', \Auth::user()->id)->get()->toArray();
            
            if(isset($departmentstafflist) && !empty($departmentstafflist))
            {
                $org_code = $objUser->org_code;
                if(($departmentstafflist != '') || ($departmentstafflist != null)){
                    // $stafflist = explode(',', $department['staff_list']);
                    // if($department['supervisor_id']!=''){
                    //     $supervisor_id = $department['supervisor_id'];
                        
                    // }
                    // if($department['officer_id']!=''){
                    //     $officer_id = $department['officer_id'];
                    // }

                    foreach ($departmentstafflist as $key => $value) {
                        
                        $userstaff = User::where('staff_id', '=', $value['staff_id'])->first();
                        if(isset($userstaff) && !empty($userstaff)){
                            $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                            $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                            $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                            $staff_name =  $fname.$mid_name.$lname;
                            $data['stafflist'][$value['staff_id']] = $fname.$mid_name.$lname;
                        }
                        
                        // print_r($data);die('+++');            
                    }

                    // $departmentStaffAssignRoleTwo = Departmentstafflist::where('dept_id', '=', $dept_id)->where('assign_role_id', '=', 002)->where('created_by', '=', \Auth::user()->id)->first();
                     
                    // if(isset($departmentStaffAssignRoleTwo) && !empty($departmentStaffAssignRoleTwo['staff_id'])){
                    //     $supervisor_id = $departmentStaffAssignRoleTwo['staff_id'];
                       
                    // }

                    // $departmentStaffAssignRoleThree = Departmentstafflist::where('dept_id', '=', $dept_id)->where('assign_role_id', '=', 003)->where('created_by', '=', \Auth::user()->id)->first();
                    // if(isset($departmentStaffAssignRoleThree) && !empty($departmentStaffAssignRoleThree['staff_id'])){
                    //     $officer_id = $departmentStaffAssignRoleThree['staff_id'];
                    // }
                    // die('+++');

                    if(isset($department['assignRoleDetails']) && !empty($department['assignRoleDetails'])){
                        $assignRoleDetails = json_decode($department['assignRoleDetails']);
                        
                        $supervisor_id = $assignRoleDetails->supervisor_id;
                        $officer_id = $assignRoleDetails->officer_id;
                        // print_r($supervisor_id);
                        // print_r($officer_id);
                        // die('+++');
                    }
                }
            }else
            {
                return response()->json(['error' => __('Something Wrong!!.')], 401);
            }
        }else{
            $department = Department::where('dept_id', '=', $dept_id)->first();
            $departmentstafflist = Departmentstafflist::where('dept_id', '=', $dept_id)->get()->toArray();
            
            if(isset($departmentstafflist) && !empty($departmentstafflist))
            {
                $org_code = $objUser->org_code;
                if(($departmentstafflist != '') || ($departmentstafflist != null)){
                                        
                    foreach ($departmentstafflist as $key => $value) {
                        
                        $userstaff = User::where('staff_id', '=', $value['staff_id'])->first();
                        if(isset($userstaff) && !empty($userstaff)){
                            $fname = ($userstaff['fname'] != "") ? $userstaff['fname'].' ' : '';
                            $mid_name = ($userstaff['mid_name'] != "") ? $userstaff['mid_name'].' ' : '';
                            $lname = ($userstaff['lname'] != "") ? $userstaff['lname'].' ' : '';

                            $staff_name =  $fname.$mid_name.$lname;
                            $data['stafflist'][$value['staff_id']] = $fname.$mid_name.$lname;
                        }
                        
                        // print_r($data);die('+++');            
                    }

                    if(isset($department['assignRoleDetails']) && !empty($department['assignRoleDetails'])){
                        $assignRoleDetails = json_decode($department['assignRoleDetails']);
                        
                        $supervisor_id = $assignRoleDetails->supervisor_id;
                        $officer_id = $assignRoleDetails->officer_id;
                    }
                }
            }else
            {
                return response()->json(['error' => __('Something Wrong!!.')], 401);
            }
        }
            
        return view('department.assignUserRole', compact('department','data','supervisor_id','officer_id'));
        
        // }
        // else
        // {
        //     return response()->json(['error' => __('Permission Denied.')], 401);
        // }
    }

    public function assignUserstore(Request $request)
    {
        
        $input = $request->all();
        $user      = \Auth::user();
        $validator = \Validator::make(
            $request->all(), [
               'supervisor_name' => 'required',
               'officer_name' => 'required',
           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->route('department')->with('error', $messages->first());
        }
        $dept_id = $input['dept_id'];
        $org_code = $input['dept_org_code'];
        $supervisor_id = $input['supervisor_name'];
        $officer_id = $input['officer_name'];
        $user_supervisor_details = User::select('*')->where('staff_id',$supervisor_id)->first()->toArray();

        $fname = ($user_supervisor_details['fname'] != "") ? $user_supervisor_details['fname'].' ' : '';
        $mid_name = ($user_supervisor_details['mid_name'] != "") ? $user_supervisor_details['mid_name'].' ' : '';
        $lname = ($user_supervisor_details['lname'] != "") ? $user_supervisor_details['lname'].' ' : '';
        $supervisor_name =  $fname.$mid_name.$lname;


        $user_officer_details = User::select('*')->where('staff_id',$officer_id)->first()->toArray();

        $o_fname = ($user_officer_details['fname'] != "") ? $user_officer_details['fname'].' ' : '';
        $o_mid_name = ($user_officer_details['mid_name'] != "") ? $user_officer_details['mid_name'].' ' : '';
        $o_lname = ($user_officer_details['lname'] != "") ? $user_officer_details['lname'].' ' : '';
        $officer_name =  $o_fname.$o_mid_name.$o_lname;

        if(isset($supervisor_id) && !empty($supervisor_id) && isset($officer_id) && !empty($officer_id))
        {     
            $departmentStaffAssignRoleTwo = Departmentstafflist::where('dept_id', '=', $dept_id)->where('assign_role_id', '=', 002)->where('created_by', '=', \Auth::user()->id)->first();
            // print_r($departmentStaffAssignRoleTwo);die('+++');
            if(isset($departmentStaffAssignRoleTwo) && !empty($departmentStaffAssignRoleTwo['assign_role_id'])){
                // $supervisor_id = $departmentStaffAssignRoleTwo['staff_id'];
                $post = [];
                $post['assign_role_name'] = 'Staff';
                $post['assign_role_id'] = '0001';
                $post['updated_at'] = date('Y-m-d H:i:s');
                $departmentStaffAssignRoleTwo->update($post);
            }

            $departmentStaffAssignRoleThree = Departmentstafflist::where('dept_id', '=', $dept_id)->where('assign_role_id', '=', 003)->where('created_by', '=', \Auth::user()->id)->first();
             // print_r($departmentStaffAssignRoleTwo);die('+++');
            if(isset($departmentStaffAssignRoleThree) && !empty($departmentStaffAssignRoleThree['assign_role_id'])){
                // $supervisor_id = $departmentStaffAssignRoleTwo['staff_id'];
                $post = [];
                $post['assign_role_name'] = 'Staff';
                $post['assign_role_id'] = '0001';
                $post['updated_at'] = date('Y-m-d H:i:s');
                $departmentStaffAssignRoleThree->update($post);
            }

            $Departmentstafflist_officer = Departmentstafflist::where('dept_id', '=', $dept_id)->where('staff_id', '=', $officer_id)->first();
            if($Departmentstafflist_officer)
            {
                $post = [];
                $post['assign_role_name'] = 'Officer';
                $post['assign_role_id'] = '0003';
                $post['updated_at'] = date('Y-m-d H:i:s');
                $Departmentstafflist_officer->update($post);
            }
            $Departmentstafflist_supervisor = Departmentstafflist::where('dept_id', '=', $dept_id)->where('staff_id', '=', $supervisor_id)->first();
            if($Departmentstafflist_supervisor)
            {
                $postnew = [];
                $postnew['assign_role_name'] = 'Supervisor';
                $postnew['assign_role_id'] = '0002';
                $postnew['updated_at'] = date('Y-m-d H:i:s');
                $Departmentstafflist_supervisor->update($postnew);
            }
            $assignRoleDetails = array(
                'officer_id'=>$officer_id,
                'officer_name'=>$officer_name,
                'supervisor_id'=>$supervisor_id,
                'supervisor_name'=>$supervisor_name,
            );
            // print_r(json_encode($assignRoleDetails));exit;
            DB::table('departments')->where(array('dept_id'=>$dept_id))->update(
                [
                    'assignRoleDetails' => json_encode($assignRoleDetails),
                    'updated_at' =>  date('Y-m-d H:i:s'),
                ]);
        }else{

        }
                    
        return redirect()->route('department')->with('success', __('Department Updated Successfully!'));   
    }

    public function previewAssignUser(Request $request)
    {
        // return view('department.previewAssignUser');
    }

}
