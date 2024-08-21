<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\Operator;
use App\Models\role_operators;
use Validator;
use App\common\Utility as UserActivity;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    public function index()
    {
        if(\Auth::user()->can('Manage Roles'))
        {
            $roles = Role::where('created_by', '=', \Auth::user()->id)->get();

            return view('roles.index')->with('roles', $roles);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('Create Role'))
        {
            $user = \Auth::user();

            if($user->type == 'Super Admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();//Get all permissions
            }
            else
            {
                $permissions = new Collection();

                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }

                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            return view('roles.create', ['permissions' => $permissions]);
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('Create Role'))
        {
            UserActivity::user_activity('Create Role');
            
            $role = Role::where('name','=', $request->name)->first();

            if(isset($role))
            {
                return redirect()->back()->with('error', __('The Role has Already Been Taken.'));
            }

            //Validate name and permissions field
            $this->validate(
                $request, 
                [
                    'name' => 'required|max:100|unique:roles,name,NULL,id,created_by,' . \Auth::user()->ownerId(),
                    'permissions' => 'required',
                ]
            );

            $name = $request['name'];
            $role = new Role();
            $role->name = $name;

            $user = \Auth::user();

            if($user->type == 'Super Admin' || $user->type == 'Owner')
            {
                $role->created_by = $user->id;
            }
            else
            {
                $role->created_by = $user->created_by;
            }

            $permissions = $request['permissions'];

            $role->save();
            //Looping thru selected permissions
            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail();
                //Fetch the newly created role and assign permission
                $role = Role::where('name', '=', $name)->first();
                $role->givePermissionTo($p);
            }

            return redirect()->route('roles.index')->with('success', __('Role successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($id)
    {
        return redirect('roles');
    }

    public function edit($id)
    {
        if(\Auth::user()->can('Edit Role'))
        {
            $user = \Auth::user();

            if($user->type == 'Super Admin')
            {
                $permissions = Permission::all()->pluck('name', 'id')->toArray();//Get all permissions
            }
            else
            {
                $permissions = new Collection();

                foreach($user->roles as $role)
                {
                    $permissions = $permissions->merge($role->permissions);
                }

                $permissions = $permissions->pluck('name', 'id')->toArray();
            }

            $role = Role::findOrFail($id);

            return view('roles.edit', compact('role', 'permissions'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('Edit Role'))
        {
            UserActivity::user_activity('Edit Role');

            $role = Role::findOrFail($id);//Get role with the given id
            //Validate name and permission fields
            $this->validate(
                $request, 
                [
                    'name' => 'required|max:100|unique:roles,name,' . $id . ',id,created_by,' . \Auth::user()->ownerId(),
                    'permissions' => 'required',
                ]
            );

            $input = $request->except(['permissions']);
            $permissions = $request['permissions'];
            $role->fill($input)->save();

            $p_all = Permission::all();//Get all permissions

            foreach($p_all as $p)
            {
                $role->revokePermissionTo($p); //Remove all permissions associated with role
            }

            foreach($permissions as $permission)
            {
                $p = Permission::where('id', '=', $permission)->firstOrFail(); //Get corresponding form //permission in db
                $role->givePermissionTo($p);  //Assign permission to role
            }

            return redirect()->route('roles.index')->with('success', __('Role successfully updated!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if(\Auth::user()->can('Delete Role'))
        {
            UserActivity::user_activity('Delete Role');

            $role = Role::findOrFail($id);
            $role->delete();

            return redirect()->route('roles.index')->with('success', __('Role successfully deleted!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function addOperator($id)
    {
        $role = Role::findOrFail($id);

        $activeRoleOperators = role_operators::GetRoleOperator($id)->pluck('operator_id')->toArray();
        
        $operators = Operator::all(); //not active operators i.e. not exist in company operators table

        return view('roles.role_operator', compact('role','operators','activeRoleOperators'));
    }

    public function storeOperator(Request $request)
    {
        $data = $request->all();

        $validated = $request->validate([
            'role_id' => 'required',
        ]);

        $company = role_operators::where('role_id', '=', $data['role_id']);

        if(!isset($request->operators))
        {
            $company->delete();
            Session::flash('success', 'Operator has been deleted');

            return redirect()->to('/roles');
        }

        $operators = $data['operators'];

        if(!empty($data['operators'])){

            DB::beginTransaction();
           
            $operatorsArr = array();

            foreach($operators as $operator){
                $operatorsArr[] = ['role_id' => $data['role_id'], 'operator_id' => $operator];
            }
             
            $company->delete();

            $opterator_created = role_operators::insert($operatorsArr);

            if($opterator_created)
            {
                Session::flash('success', 'Operator added successfully!!');
            }
            else
            {
                DB::rollBack();
                Session::flash('error', 'Error, something is going wrong!!');
            }

            DB::commit();
        }

        return redirect()->to('/roles');
    }
}
