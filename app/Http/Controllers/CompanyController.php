<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Session;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Company;
use App\Models\Country;
use App\Models\Operator;
use App\Models\CompanyOperators;
use Illuminate\Support\Facades\DB;
use Validator;
use App\common\Utility as UserActivity;

class CompanyController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('Company Management'))
        {
            $companies = Company::with('company_operators')->orderby('id','DESC')->get();

            $operators = DB::table('companies')
            ->join('company_operators', 'companies.id', '=', 'company_operators.company_id')
            ->join('operators', 'company_operators.operator_id', '=', 'operators.id_operator')
            ->select('companies.*', 'company_operators.company_id', 'operators.id_operator', 'operators.operator_name', 'operators.status')
            ->get();

            $id_operator = $operators->pluck('id_operator')->toArray();

            $total_operators = Operator::all();
            $total_operators = count($total_operators);
            $unknown_operators = $total_operators - count($id_operator);

            return view('company.company_list', compact('companies','operators','unknown_operators'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function addOperator($id)
    {
        $company = Company::findOrFail($id);

        $activeOperators = CompanyOperators::GetOperator($id)->pluck('operator_id')->toArray();

        $operators = Operator::all(); //not active operators i.e. not exist in company operators table

        return view('company.create_operator', compact('company','operators','activeOperators'));
    }

    public function store_operator(Request $request){

        $data = $request->all();

        $validated = $request->validate([
            'company_id' => 'required',
        ]);

        $company = CompanyOperators::where('company_id', '=', $data['company_id']);

        if(!isset($request->operators))
        {
            $company->delete();

            UserActivity::user_activity('Delete Company Operator');

            Session::flash('success', 'Operator has been deleted');
            return redirect()->to('/management/company');
        }

        $operators = $data['operators'];

        if(!empty($data['operators'])){

            DB::beginTransaction();

            $operatorsArr = array();

            foreach($operators as $operator){
                $operatorsArr[] = ['company_id' => $data['company_id'], 'operator_id' => $operator];
            }

            $company->delete();

            $opterator_created = CompanyOperators::insert($operatorsArr);

            UserActivity::user_activity('Edit Company Operators');

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

        return redirect()->to('/management/company');
    }

    public function editCompany($id)
    {
        $company = Company::where('id','=',$id)->first();

        return view('company.edit_company', compact('company'));
    }

    public function updateCompany(Request $request,$id)
    {
        $data = $request->all();
        $company = Company::where('id','=',$id)->first();
        $company->name = $data['company'];

        UserActivity::user_activity('Edit Company');

        if($company->save()){
            Session::flash('success', 'Company updated successfully!!');
        }else{
            Session::flash('error', 'Error, something is going wrong!!');
        }

        return redirect()->to('/management/company');
    }

    public function editOperator($id)
    {
        $company = DB::table('companies')->where('id',$id)->first();

        $companyOperators = DB::table('companies')
        ->join('company_operators', 'companies.id', '=', 'company_operators.company_id')// joining the contacts table , where user_id and contact_user_id are same
        ->join('operators', 'company_operators.operator_id', '=', 'operators.id_operator')
        ->select('company_operators.company_id','company_operators.operator_id', 'operators.operator_name', 'operators.status')
        ->where('companies.id', $id)
        ->get();

        $activeOperators = CompanyOperators::get()->where('company_id', $id)->pluck('operator_id')->toArray();

        $operators = Operator::whereNotIn('id_operator', $activeOperators)->get(); //not active operators i.e. not exist in company operators table

        return view('company.edit_operator', compact('company','operators','companyOperators'));
    }

    public function all_com_operators($id){

        $companyOperators = CompanyOperators::GetOperator($id)->get();

        $company_id = $id;

        return view('company.company_operators', compact( 'companyOperators', 'company_id'));
    }

    public function all_unknown_operators()
    {
        $companyOperators = DB::table('companies')
        ->join('company_operators', 'companies.id', '=', 'company_operators.company_id')
        ->join('operators', 'company_operators.operator_id', '=', 'operators.id_operator')
        ->select('companies.*', 'company_operators.company_id', 'operators.id_operator', 'operators.operator_name', 'operators.status')
        ->get();

        $id_operator = $companyOperators->pluck('id_operator')->toArray();

        $operators = Operator::NotInOperators($id_operator)->Status(1)->get();

        return view('company.company_operators', compact( 'operators'));
    }
}
