<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Company;
use App\Models\RevenushareByDate;
use App\Models\Operator;
use App\Models\Revenushare;
use App\Http\Requests\RevenuShareByDateRequest;
use App\Models\CompanyOperators;
use App\Models\VatByDate;
use App\Models\WhtByDate;
use App\common\Utility as UserActivity;

class OperatorController extends Controller
{
    public function index()
    {
        $operators = Operator::get();

        return view('operator.operator-list', compact('operators'));
    }

    public function editOperator($id)
    {
        $company = Company::where('id','=',$id)->first();

        return view('management.edit_company', compact('company'));
    }

    public function store_operator(Request $request)
    {
        $data = $request->all();

        $validated = $request->validate([
            'company_id' => 'required',
        ]);

        $company = CompanyOperators::where('company_id', '=', $data['company_id']);

        if(!isset($request->operators))
        {
            $company->delete();

            UserActivity::user_activity('Remove Company Operators');

            Session::flash('success', 'Operator has been deleted');

            return redirect()->to('/management/company');
        }

        $operators = $data['operators'];

        if(!empty($data['operators']))
        {
            DB::beginTransaction();

            $operatorsArr = array();

            foreach($operators as $operator){
                $operatorsArr[] = ['company_id' => $data['company_id'], 'operator_id' => $operator];
            }

            $company->delete();

            $opterator_created = CompanyOperators::insert($operatorsArr);

            UserActivity::user_activity('Update Company Operator');

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

    public function update_operator(Request $request)
    {
        $res = [];
        $id = $request->operator_id;
        $status = $request->status;

        if ($status) {
            $msg = 'Category activated';
            Session::flash('success', 'Operator Updated successfully!!');
        }else {
            $msg = 'Category deactivated';
            Session::flash('error', 'Operator deactivated successfully!!');
        }

        $dtl = Operator::whereId($id)->first();

        $dtl->status = $status;

        UserActivity::user_activity('Update Operator');

        if ($dtl->save()) {
            $res['status'] = true;
            $res['msg'] = $msg;
        }else {
            $res['status'] = false;
            $res['msg'] = 'Somthing went wrong, try again later';
        }

        echo json_encode($res);
    }

    public function create_rev_share($id){
        $operator = Revenushare::where('operator_id','=',$id)->first();

        return view('operator.rev-share', compact('id', 'operator'));
    }

    public function updateRev_Share(Request $request)
    {
        $data = $request->all();

        $operator = Revenushare::where('operator_id','=',$data['operator'])->first();

        if(!empty($operator->operator_id)){
            $operator->operator_id = $data['operator'];
            $operator->operator_revenue_share = $data['operator_revenue_share'];
            $operator->merchant_revenue_share = $data['merchant_revenue_share'];

            if($operator->save()){
                Session::flash('success', 'Revenue Share updated successfully!!');
            }else{
                Session::flash('error', 'Failed to Update Revenue Share !!');
            }
        }
        else{
            $operator['operator_id'] = $data['operator'];
            $operator['operator_revenue_share'] = $data['operator_revenue_share'];
            $operator['merchant_revenue_share'] = $data['merchant_revenue_share'];

            if(Revenushare::insert($operator)){
                Session::flash('success', 'Revenue Share Created successfully!!');
            }else{
                Session::flash('error', 'Failed to Create Revenue Share !!');
            }
        }

        UserActivity::user_activity('Update Revenue Share');

        return redirect()->to('/management/operator');
    }

    public function createRevshareByDate($id){
        $operator = Revenushare::where('operator_id','=',$id)->first();
        $revShare = RevenushareByDate::findRevenushareByOperatorId($id)->get();

        return view('operator.revShareDate', compact('id', 'operator', 'revShare'));
    }

    public function updateRevshareByDate(Request $request)
    {
        $data = $request->all();

        if(!empty($data['operator_revenue_share'])){
            $operator = Revenushare::where('operator_id','=',$data['operator'])->first();

            if(!empty($operator->operator_id)){
                $operator->operator_id = $data['operator'];
                $operator->operator_revenue_share = $data['operator_revenue_share'];
                $operator->merchant_revenue_share = 100 - $data['operator_revenue_share'];

                if($operator->save()){
                    Session::flash('success', 'Revenue Share updated successfully!!');
                }else{
                    Session::flash('error', 'Failed to Update Revenue Share !!');
                }
            }
            else{
                $operator['operator_id'] = $data['operator'];
                $operator['operator_revenue_share'] = $data['operator_revenue_share'];
                $operator['merchant_revenue_share'] = 100 - $data['operator_revenue_share'];

                if(Revenushare::insert($operator)){
                    Session::flash('success', 'Revenue Share Created successfully!!');
                }else{
                    Session::flash('error', 'Failed to Create Revenue Share !!');
                }
            }
        }

        if(!empty($data['operator_revenue_share_date'])){
            $data = [
                'operator_id' => $data['operator'],
                'operator_revenue_share' => $data['operator_revenue_share_date'],
                'merchant_revenue_share' => 100 - $data['operator_revenue_share_date'],
                'year' => $data['year'],
                'month' => sprintf('%02d',$data['month']),
                'key' => $request->year."-".sprintf('%02d',$data['month']),
            ];

            $operator = RevenushareByDate::upsert($data,['operator_id','key',],['operator_revenue_share','merchant_revenue_share','year','month']);
        }

        UserActivity::user_activity('Update Revenue Share');

        return redirect()->back()->with('success', __('Revenue Share updated successfully!'));
    }

    public function createVatWhtByDate($id){
        $operator = Operator::filterOperatorID($id)->first();
        $vat = VatByDate::findVatByOperatorId($id)->get() ?? [];
        $wht = WhtByDate::findWhtByOperatorId($id)->get() ?? [];

        return view('operator.vatwhtDate', compact('id', 'operator', 'vat', 'wht'));
    }

    public function updateVatWhtByDate(Request $request){
        $datas = $request->all();
        // dd($data);


        if(!empty($datas['operator_vat']) || !empty($datas['operator_wht'])){
            $operator = Operator::filterOperatorID($datas['operator'])->first();

            if(!empty($operator->id_operator)){
                $operator->id_operator = $datas['operator'];
                $operator->vat = isset($datas['operator_vat']) ? $datas['operator_vat'] : $operator->vat;
                $operator->wht = isset($datas['operator_wht']) ? $datas['operator_wht'] : $operator->wht;

                if($operator->update()){
                    Session::flash('success', 'Vat And Wht updated successfully!!');
                }else{
                    Session::flash('error', 'Failed to Update Vat And Wht !!');
                }
            }
            else{
                $operator['id_operator'] = $datas['operator'];
                $operator['vat'] = $datas['operator_vat'];
                $operator['wht'] = $datas['operator_wht'];

                if(Operator::insert($operator)){
                    Session::flash('success', 'Vat And Wht Created successfully!!');
                }else{
                    Session::flash('error', 'Failed to Create Vat And Wht!!');
                }
            }
        }

        if(!empty($datas['operator_vat_date'])){
            $data = [
                'operator_id' => $datas['operator'],
                'vat' => $datas['operator_vat_date'],
                'year' => $datas['year'],
                'month' => sprintf('%02d',$datas['month']),
                'key' => $request->year."-".sprintf('%02d',$datas['month']),
            ];

            $operator = VatByDate::upsert($data,['operator_id','key',],['vat','year','month']);
        }

        if(!empty($datas['operator_wht_date'])){
            $Data = [
                'operator_id' => $datas['operator'],
                'wht' => $datas['operator_wht_date'],
                'year' => $datas['wht_year'],
                'month' => sprintf('%02d',$datas['wht_month']),
                'key' => $request->wht_year."-".sprintf('%02d',$datas['wht_month']),
            ];
        // dd($Data);
            $operator = WhtByDate::upsert($Data,['operator_id','key',],['wht','year','month']);
        }

        UserActivity::user_activity('Update Vat and Wht');

        return redirect()->back()->with('success', __('Vat And Wht updated successfully!'));
    }
}
