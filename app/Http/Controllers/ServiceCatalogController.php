<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Operator;
use App\Models\Country;
use App\Models\User;
use App\Models\ScServices;
use App\Models\ScOperators;
use App\Models\ScServiceProgres;
use App\Models\ScServiceStatus;
use App\Http\Requests\ServiceRequest;
use App\Http\Requests\ServiceEditRequest;
use App\Mail\ServiceCatalogMail;
use App\Mail\ServiceCatalogUpdateMail;
use App\Models\ServiceChecklists;
use Illuminate\Support\Facades\DB;
use Session;
use Mail;
use PhpParser\Node\Expr\Isset_;
use DateTime;
use DateInterval;
use DatePeriod;

class ServiceCatalogController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
    public function create()
    {
        if (\Auth::user()->can('Add New Service')) {
            $companys = Company::orderBy('name', 'ASC')->get();
            $countrys = Country::orderBy('country', 'ASC')->get();
            $operators = Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
            $ScOperators = ScOperators::get()->toArray();
            $notAllowuserTypes = array("Owner", "Super Admin", "Admin");
            $Users = User::Types($notAllowuserTypes)->Active()->get();
            // $merged = $operators->merge($ScOperators);
            // $operators = array_merge($operators,$ScOperators);
            // dd($result);
            Session::forget('error');
            return view('service.addService', compact('companys', 'countrys', 'Users', 'ScOperators'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function draftstore(ServiceEditRequest $request)
    {
        $channal = [
            $request->channelowap,
            $request->channeloussd,
            $request->channelosms,
            $request->channeloivr,
        ];
        $cycle = [
            'changeCycleDaily' => $request->changeCycleDaily,
            'changeCycleWeekly' => $request->changeCycleWeekly,
            'changeCycleMonthly' => $request->changeCycleMonthly,
        ];
        $campaign = [
            $request->sms,
            $request->wap,
            $request->api,
        ];
        $currencyata = [
            'currency' => $request['currency'],
            'service_price' => $request['service_price']
        ];
        $currencyJson = json_encode($currencyata);
        // dd($currencyJson);


        $teamData = [
            'team_name' => $request['team_name'],
            'team_email' => $request['team_email'],
            'team_whatsapp' => $request['team_whatsapp'],
            'level' => $request['level']
        ];

        // Convert the array to JSON
        $teamJson = json_encode($teamData);

        $clientData = [
            'client_name' => $request['client_name'],
            'client_email' => $request['client_email'],
            'client_whatsapp' => $request['client_whatsapp'],
        ];

        // Convert the array to JSON
        $clientJson = json_encode($clientData);

        $telcoData = [
            'telco_name' => $request['telco_name'],
            'telco_email' => $request['telco_email'],
            'telco_whatsapp' => $request['telco_whatsapp'],
        ];

        // Convert the array to JSON
        $telcoJson = json_encode($telcoData);

        $files =  $request->product_brief_file;
        $newFile = isset($files) ? $files : '';

        if (!empty($newFile)) {
            $file = 'product' . rand() . time() . '.' . $newFile->extension();
            $path = 'assets/service_catalogue/product_brief';
            $newFile->storeAs($path, $file, ['disk' => 'public_uploads']);
        }

        $faq =  $request->faq_file;
        $newFile1 = isset($faq) ? $faq : '';

        if (!empty($newFile1)) {
            $file1 = 'faq' . rand() . time() . '.' . $newFile1->extension();
            $path1 = 'assets/service_catalogue/faq_file';
            $newFile1->storeAs($path1, $file1, ['disk' => 'public_uploads']);
        }


        $contract =  $request->contract_file;
        $newFile2 = isset($contract) ? $contract : '';

        if (!empty($newFile2)) {
            $file2 = 'contract' . rand() . time() . '.' . $newFile2->extension();
            $path2 = 'assets/service_catalogue/contract_file';
            $newFile2->storeAs($path2, $file2, ['disk' => 'public_uploads']);
        }
        $coi =  $request->coi_file;
        $newFile3 = isset($coi) ? $coi : '';

        if (!empty($newFile3)) {
            $file3 = 'coi' . rand() . time() . '.' . $newFile3->extension();
            $path3 = 'assets/service_catalogue/coi_file';
            $newFile3->storeAs($path3, $file3, ['disk' => 'public_uploads']);
        }
        $adden =  $request->addendums_file;
        $newFile4 = isset($adden) ? $adden : '';

        if (!empty($newFile4)) {
            $file4 = 'addendums' . rand() . time() . '.' . $newFile4->extension();
            $path4 = 'assets/service_catalogue/addendums_file';
            $newFile4->storeAs($path4, $file4, ['disk' => 'public_uploads']);
        }
        $authority =  $request->authority_file;
        $newFile5 = isset($authority) ? $authority : '';

        if (!empty($newFile5)) {
            $file5 = 'authority' . rand() . time() . '.' . $newFile5->extension();
            $path5 = 'assets/service_catalogue/authority_file';
            $newFile5->storeAs($path5, $file5, ['disk' => 'public_uploads']);
        }
        $cor_dgt =  $request->cor_dgt_file;
        $newFile6 = isset($cor_dgt) ? $cor_dgt : '';

        if (!empty($newFile6)) {
            $file6 = 'cor_dgt' . rand() . time() . '.' . $newFile6->extension();
            $path6 = 'assets/service_catalogue/cor_dgt_file';
            $newFile6->storeAs($path6, $file6, ['disk' => 'public_uploads']);
        }
        if ($request->is_draf  == 1) {
            $data = "Draft";
        }

        if ($request->is_draf  == 1) {
            $status = 1;
        }

        if ($request->exit_operator == 1 && !empty($request->operator)) {
            $operator = $request->operator;
        }
        if ($request->exit_operator == 0 && !empty($request->newOperatorName)) {
            $operators = $request->newOperatorName;
        }
        if (isset($operator)) {
            $datas = $operator;
        } elseif (isset($operators)) {
            $datas = $operators;
        } else {
            $datas = NULL;
        }
        $start_date = new DateTime('now');

        $service = [
            'country_id' => $request->country,
            'company_id' => $request->company,
            // 'operator_id' => $request->operator,
            'operator_name' => $datas,
            'exit_operator' => $request->exit_operator,
            'service_name' => $request->servicename,
            'aggregator_status' => $request->aggregratorPermission,
            'aggregator' => $request->aggregrator,
            'subkeyword' => $request->subkeyword,
            'short_code' => $request->short_code,
            'type' => $request->type,
            'service_type' => $request->service_type,
            'channel' => serialize($channal),
            'cycle' =>  serialize($cycle),
            'is_freemium' => $request->freemiumPermission,
            'freemium' => ($request->freemiumPermission == 'no') ? NULL : $request->freemiumDays,
            // 'service_price' => $request->service_price,
            'currency_service_price' =>  $currencyJson,
            'revenue_share' => !empty($request->revenueshare) ? $request->revenueshare : 0.0000,
            'merchant_share' => !empty($request->revenuemerchant) ? $request->revenuemerchant : 0.0000,
            'report_source' =>  $request->report_source,
            'report_partner' => $request->report_partner,
            'sub_domain_portal' =>  $request->domain_portal,
            'portal_url' =>  $request->portal_url,
            'cms_portal' =>  $request->cms_portal,
            'username_cms_portal' =>  $request->username_portal,
            'password_cms_portal' =>  $request->password_portal,
            'url_cs_tools' =>  $request->cs_tool,
            'url_cs_tools_main' =>  $request->cs_tool_main,
            'campaign_type' =>  serialize($campaign),
            'url_postback' =>  $request->postback,
            'url_campaign' =>  $request->campaign_url,
            'product_brief_file' => isset($file) ? $file : NULL,
            'faq_file' => isset($file1) ? $file1 : NULL,
            'contract_file' =>  isset($file2) ? $file2 : NULL,
            'merchant_coi_file' => isset($file3) ? $file3 : NULL,
            'addendums_file' => isset($file4) ? $file4 : NULL,
            'content_authority_letter' => isset($file5) ? $file5 : NULL,
            'cor_dgt_file' => isset($file6) ? $file6 : NULL,
            'matrix_enternal_team' =>  isset($teamJson) ? $teamJson : NULL,
            'matrix_client' =>  isset($clientJson) ? $clientJson : NULL,
            'matrix_telco' => isset($telcoJson) ? $telcoJson : NULL,
            'account_manager' => $request->account_manager,
            'cs_team' => $request->csteam,
            'infra_team' => $request->infrateam,
            'cs_team' => $request->csteam,
            'account_manager' => $request->account_manager,
            'pmo' => $request->pmo,
            'backend' => $request->backend,
            'status_intregration' => $data,
            'project_start_date' => !empty($request->start_date) ? $request->start_date : NULL,
            'project_end_date' => !empty($request->end_date) ? $request->end_date : NULL,
            // 'go_live_date'=>$request->go_live_date,
            'schedule_payment' => !empty($request->schedule_payment) ? $request->schedule_payment : NULL,
            'payment_come_date' => !empty($request->payment_come) ? $request->payment_come : NULL,
            'is_draf' => $request->is_draf,
            'is_active' => $status,
            // 'status_count' => 1
        ];
        // dd($service);
        $user      = ScServices::create($service);


        return redirect()->route('report.list')->with(
            'success',
            __('Service successfully create!')
        );
    }

    public function store(ServiceRequest $request)
    {

        $channal = [
            $request->channelowap,
            $request->channeloussd,
            $request->channelosms,
            $request->channeloivr,
        ];
        $cycle = [
            'changeCycleDaily' => $request->changeCycleDaily,
            'changeCycleWeekly' => $request->changeCycleWeekly,
            'changeCycleMonthly' => $request->changeCycleMonthly,
        ];
        $campaign = [
            $request->sms,
            $request->wap,
            $request->api,
        ];
        $currencyata = [
            'currency' => $request['currency'],
            'service_price' => $request['service_price']
        ];
        $currencyJson = json_encode($currencyata);
        // dd($currencyJson);


        $teamData = [
            'team_name' => $request['team_name'],
            'team_email' => $request['team_email'],
            'team_whatsapp' => $request['team_whatsapp'],
            'level' => $request['level']
        ];

        // Convert the array to JSON
        $teamJson = json_encode($teamData);

        $clientData = [
            'client_name' => $request['client_name'],
            'client_email' => $request['client_email'],
            'client_whatsapp' => $request['client_whatsapp'],
        ];

        // Convert the array to JSON
        $clientJson = json_encode($clientData);

        $telcoData = [
            'telco_name' => $request['telco_name'],
            'telco_email' => $request['telco_email'],
            'telco_whatsapp' => $request['telco_whatsapp'],
        ];

        // Convert the array to JSON
        $telcoJson = json_encode($telcoData);

        $files =  $request->product_brief_file;
        $newFile = isset($files) ? $files : '';

        if (!empty($newFile)) {
            $file = 'product' . rand() . time() . '.' . $newFile->extension();
            $path = 'assets/service_catalogue/product_brief';
            $newFile->storeAs($path, $file, ['disk' => 'public_uploads']);
        }

        $faq =  $request->faq_file;
        $newFile1 = isset($faq) ? $faq : '';

        if (!empty($newFile1)) {
            $file1 = 'faq' . rand() . time() . '.' . $newFile1->extension();
            $path1 = 'assets/service_catalogue/faq_file';
            $newFile1->storeAs($path1, $file1, ['disk' => 'public_uploads']);
        }


        $contract =  $request->contract_file;
        $newFile2 = isset($contract) ? $contract : '';

        if (!empty($newFile2)) {
            $file2 = 'contract' . rand() . time() . '.' . $newFile2->extension();
            $path2 = 'assets/service_catalogue/contract_file';
            $newFile2->storeAs($path2, $file2, ['disk' => 'public_uploads']);
        }
        $coi =  $request->coi_file;
        $newFile3 = isset($coi) ? $coi : '';

        if (!empty($newFile3)) {
            $file3 = 'coi' . rand() . time() . '.' . $newFile3->extension();
            $path3 = 'assets/service_catalogue/coi_file';
            $newFile3->storeAs($path3, $file3, ['disk' => 'public_uploads']);
        }
        $adden =  $request->addendums_file;
        $newFile4 = isset($adden) ? $adden : '';

        if (!empty($newFile4)) {
            $file4 = 'addendums' . rand() . time() . '.' . $newFile4->extension();
            $path4 = 'assets/service_catalogue/addendums_file';
            $newFile4->storeAs($path4, $file4, ['disk' => 'public_uploads']);
        }
        $authority =  $request->authority_file;
        $newFile5 = isset($authority) ? $authority : '';

        if (!empty($newFile5)) {
            $file5 = 'authority' . rand() . time() . '.' . $newFile5->extension();
            $path5 = 'assets/service_catalogue/authority_file';
            $newFile5->storeAs($path5, $file5, ['disk' => 'public_uploads']);
        }
        $cor_dgt =  $request->cor_dgt_file;
        $newFile6 = isset($cor_dgt) ? $cor_dgt : '';

        if (!empty($newFile6)) {
            $file6 = 'cor_dgt' . rand() . time() . '.' . $newFile6->extension();
            $path6 = 'assets/service_catalogue/cor_dgt_file';
            $newFile6->storeAs($path6, $file6, ['disk' => 'public_uploads']);
        }
        if ($request->is_draft  == 0) {
            $Data = "On Progress Development";
        }

        if ($request->is_draft  == 0) {
            $status = 1;
        }
        if ($request->exit_operator == 1 && !empty($request->operator)) {
            $operator = $request->operator;
        }
        if ($request->exit_operator == 0 && !empty($request->newOperatorName)) {
            $operators = $request->newOperatorName;
        }
        $service = [
            'country_id' => $request->country,
            'company_id' => $request->company,
            // 'operator_id' => $request->operator,
            'operator_name' => isset($operator) ? $operator : $operators,
            'exit_operator' => $request->exit_operator,
            'service_name' => $request->servicename,
            'aggregator_status' => $request->aggregratorPermission,
            'aggregator' => $request->aggregrator,
            'subkeyword' => $request->subkeyword,
            'short_code' => $request->short_code,
            'type' => $request->type,
            'service_type' => $request->service_type,
            'channel' => serialize($channal),
            'cycle' =>  serialize($cycle),
            'is_freemium' => $request->freemiumPermission,
            'freemium' => ($request->freemiumPermission == 'no') ? NULL : $request->freemiumDays,
            // 'service_price' => $request->service_price,
            'currency_service_price' =>  $currencyJson,
            'revenue_share' => $request->revenueshare,
            'merchant_share' => $request->revenuemerchant,
            'report_source' =>  $request->report_source,
            'report_partner' => $request->report_partner,
            'sub_domain_portal' =>  $request->domain_portal,
            'portal_url' =>  $request->portal_url,
            'cms_portal' =>  $request->cms_portal,
            'username_cms_portal' =>  $request->username_portal,
            'password_cms_portal' =>  $request->password_portal,
            'url_cs_tools' =>  $request->cs_tool,
            'url_cs_tools_main' =>  $request->cs_tool_main,
            'campaign_type' =>  serialize($campaign),
            'url_postback' =>  $request->postback,
            'url_campaign' =>  $request->campaign_url,
            'product_brief_file' => isset($file) ? $file : NULL,
            'faq_file' => isset($file1) ? $file1 : NULL,
            'contract_file' =>  isset($file2) ? $file2 : NULL,
            'merchant_coi_file' => isset($file3) ? $file3 : NULL,
            'addendums_file' => isset($file4) ? $file4 : NULL,
            'content_authority_letter' => isset($file5) ? $file5 : NULL,
            'cor_dgt_file' => isset($file6) ? $file6 : NULL,
            'matrix_enternal_team' =>  isset($teamJson) ? $teamJson : NULL,
            'matrix_client' =>  isset($clientJson) ? $clientJson : NULL,
            'matrix_telco' => isset($telcoJson) ? $telcoJson : NULL,
            'account_manager' => $request->account_manager,
            'cs_team' => $request->csteam,
            'infra_team' => $request->infrateam,
            'cs_team' => $request->csteam,
            'account_manager' => $request->account_manager,
            'pmo' => $request->pmo,
            'backend' => $request->backend,
            'status_intregration' => $Data,
            'project_start_date' => $request->start_date,
            'project_end_date' => $request->end_date,
            // 'go_live_date'=>$request->go_live_date,
            'schedule_payment' => !empty($request->schedule_payment) ? $request->schedule_payment : NULL,
            'payment_come_date' => !empty($request->payment_come) ? $request->payment_come : NULL,
            'is_draf' => $request->is_draft,
            'is_active' => $status,
            'status_count' => 1
        ];
        // dd($service);
        $user      = ScServices::create($service);

        $progress = ScServiceStatus::get();
        foreach ($progress as $progres) {
            // dd($request->$dute_date);
            if ($progres->id == 1) {
                $data = [
                    'id_service' => $user->id,
                    'id_service_status' => $progres->id,
                    'dute_date' => date("Y-m-d"),
                    'complete_due_date' => date("Y-m-d"),
                    'status' => 'done',
                ];
            } else {
                $data = [
                    'id_service' => $user->id,
                    'id_service_status' => $progres->id,
                    'dute_date' => null,
                    'complete_due_date' => null,
                    'status' => 'pending',
                ];
            }


            $datas[] = $data;
        }
        ScServiceProgres::upsert($datas, ['id_service', 'id_service_status'], ['dute_date', 'complete_due_date', 'status',]);

        // $accountManager = User::where('id', $request->account_manager)->first();
        // $pmo = User::where('id', $request->pmo)->first();

        // $detailsPMO = [
        //     'name' => $pmo->name,
        //     'service_name' => $request->servicename,
        //     'information' => "We inform you that your account has been added to the list of PMO users with the following services:"
        // ];
        // $detailsAccountManager = [
        //     'name' => $accountManager->name,
        //     'service_name' => $request->servicename,
        //     'information' => "We inform you that a new service has been added, here is the data:"
        // ];
        // // email account manager/
        // Mail::to([$accountManager->email])->send(new ServiceCatalogMail($detailsAccountManager));

        // // email pmo user
        // Mail::to(['budinugrohomei6@gmail.com', 'budisetionugroho0001@gmail.com'])->send(new ServiceCatalogMail($detailsPMO));

        return redirect()->route('report.list')->with(
            'success',
            __('Service successfully create!')
        );
    }

    public function list(Request $request)
    {
        // dd(phpinfo());
        if (\Auth::user()->can('Service List')) {
            $countrys = Country::orderBy('country', 'ASC')->get();
            $operators = Operator::Status(1)->orderBy('operator_name', 'ASC')->get()->toArray();
            $ScOperators = ScOperators::get()->toArray();
            $notAllowuserTypes = array("Owner", "Super Admin", "Admin");
            $users = User::Types($notAllowuserTypes)->Active()->get();
            // $merged = $operators->merge($ScOperators);
            $operators = array_merge($operators, $ScOperators);
            // Controller method or query fetching part
               // Controller method or query fetching part
            $services = ScServices::orderByRaw("
            CASE
                WHEN status_intregration = 'Go Live' THEN 1
                WHEN is_active = 1 THEN 2
                ELSE 3
            END,
            id DESC
            ");


            $countryId = $request->country;
            $operatorId = $request->operator;
            $account_managerId = $request->account_manager;
            $pmoId = $request->pmo;
            $backendId =  $request->backend;
            $statu_s = $request->status;
            $inter = $request->intergration;
            // dd($statu_s);// Example range string
            $start_date = $request->from;
            $end_date =  $request->to;
            $range = $statu_s;

            if ($request->filled('country')) {
                $services = $services->findByCountry($countryId);
            }
            if ($request->filled('operator')) {
                $services = $services->findByOperatorname($operatorId);
            }
            if ($request->filled('account_manager')) {
                $services = $services->findByAccountManager($account_managerId);
            }

            if ($request->filled('pmo')) {
                $services = $services->findByPmo($pmoId);
            }
            if ($request->filled('backend')) {
                $services = $services->findByBackend($backendId);
            }
            // if ($request->filled('status')) {
            //     if($range != 26 && $range != 0){
            //         list($start, $end) = explode("-", $range);
            //     $values = range($start, $end);
            //     $services = $services->findByStatus($values);
            //     }
            //     if($range == 26 || $range == 0){
            //         $services =$services->where('status_count', $range);
            //     }
            // }
            if ($request->filled('status')) {
                $services = $services->findByStatus($statu_s);
            }
            if ($request->filled('intergration')) {
                $services = $services->findByIntergration($inter);
            }
            if ($request->filled('from') && $request->filled('to')) {
                $services = $services->filterDateRange($start_date, $end_date);
            }

            $services = $services->get();

            $count = $this->checklistdata($services);
            $days = $this->dayscalculation($services);


            return view('service.list', compact('services', 'countrys', 'operators', 'users','count','days'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('Service List')) {
            $service = ScServices::FindOrFail($id);
            $companys = Company::orderBy('name', 'ASC')->get();
            $countrys = Country::orderBy('country', 'ASC')->get();
            $operators = Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
            $ScOperators = ScOperators::get();
            $notAllowuserTypes = array("Owner", "Super Admin", "Admin",);
            $Users = User::Types($notAllowuserTypes)->Active()->get();
            // dd($Users);
            Session::forget('error');
            return view('service.edit', compact('service', 'companys', 'countrys', 'operators', 'ScOperators', 'Users'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
    public function update(ServiceRequest $request)
    {


        $channal = [
            $request->channelowap,
            $request->channeloussd,
            $request->channelosms,
            $request->channeloivr,
        ];
        $cycle = [
            'changeCycleDaily' => $request->changeCycleDaily,
            'changeCycleWeekly' => $request->changeCycleWeekly,
            'changeCycleMonthly' => $request->changeCycleMonthly,
        ];

        $campaign = [
            $request->sms,
            $request->wap,
            $request->api,
        ];
        $currencyata = [
            'currency' => $request['currency'],
            'service_price' => $request['service_price']
        ];
        $currencyJson = json_encode($currencyata);
        // dd($currencyJson);


        $teamData = [
            'team_name' => $request['team_name'],
            'team_email' => $request['team_email'],
            'team_whatsapp' => $request['team_whatsapp'],
            'level' => $request['level']
        ];

        // Convert the array to JSON
        $teamJson = json_encode($teamData);

        $clientData = [
            'client_name' => $request['client_name'],
            'client_email' => $request['client_email'],
            'client_whatsapp' => $request['client_whatsapp'],
        ];

        // Convert the array to JSON
        $clientJson = json_encode($clientData);

        $telcoData = [
            'telco_name' => $request['telco_name'],
            'telco_email' => $request['telco_email'],
            'telco_whatsapp' => $request['telco_whatsapp'],
        ];

        // Convert the array to JSON
        $telcoJson = json_encode($telcoData);

        $files =  $request->product_brief_file;
        $newFile = isset($files) ? $files : '';

        if (!empty($newFile)) {
            // dd($newFile);
            $file = 'product' . rand() . time() . '.' . $newFile->extension();
            $path = 'assets/service_catalogue/product_brief';
            $newFile->storeAs($path, $file, ['disk' => 'public_uploads']);
        }

        $faq =  $request->faq_file;
        $newFile1 = isset($faq) ? $faq : '';

        if (!empty($newFile1)) {
            $file1 = 'faq' . rand() . time() . '.' . $newFile1->extension();
            $path1 = 'assets/service_catalogue/faq_file';
            $newFile1->storeAs($path1, $file1, ['disk' => 'public_uploads']);
        }


        $contract =  $request->contract_file;
        $newFile2 = isset($contract) ? $contract : '';

        if (!empty($newFile2)) {
            $file2 = 'contract' . rand() . time() . '.' . $newFile2->extension();
            $path2 = 'assets/service_catalogue/contract_file';
            $newFile2->storeAs($path2, $file2, ['disk' => 'public_uploads']);
        }
        $coi =  $request->coi_file;
        $newFile3 = isset($coi) ? $coi : '';

        if (!empty($newFile3)) {
            $file3 = 'coi' . rand() . time() . '.' . $newFile3->extension();
            $path3 = 'assets/service_catalogue/coi_file';
            $newFile3->storeAs($path3, $file3, ['disk' => 'public_uploads']);
        }
        $adden =  $request->addendums_file;
        $newFile4 = isset($adden) ? $adden : '';

        if (!empty($newFile4)) {
            $file4 = 'addendums' . rand() . time() . '.' . $newFile4->extension();
            $path4 = 'assets/service_catalogue/addendums_file';
            $newFile4->storeAs($path4, $file4, ['disk' => 'public_uploads']);
        }
        $authority =  $request->authority_file;
        $newFile5 = isset($authority) ? $authority : '';

        if (!empty($newFile5)) {
            $file5 = 'authority' . rand() . time() . '.' . $newFile5->extension();
            $path5 = 'assets/service_catalogue/authority_file';
            $newFile5->storeAs($path5, $file5, ['disk' => 'public_uploads']);
        }
        $cor_dgt =  $request->cor_dgt_file;
        $newFile6 = isset($cor_dgt) ? $cor_dgt : '';

        if (!empty($newFile6)) {
            $file6 = 'cor_dgt' . rand() . time() . '.' . $newFile6->extension();
            $path6 = 'assets/service_catalogue/cor_dgt_file';
            $newFile6->storeAs($path6, $file6, ['disk' => 'public_uploads']);
        }
        // dd($request->is_draft);
        if ($request->is_draft  == '0') {
            $Data = "On Progress Development";
        }

        if ($request->exit_operator == 1 && !empty($request->operator)) {
            $operator = $request->operator;
        }
        if ($request->exit_operator == 0 && !empty($request->newOperatorName)) {
            $operators = $request->newOperatorName;
        }
        if (isset($operator)) {
            $datas = $operator;
        } elseif (isset($operators)) {
            $datas = $operators;
        } else {
            $datas = NULL;
        }

        $user  = ScServices::findServices($request->id)->first();

        $service = [
            'country_id' => $request->country,
            'company_id' => $request->company,
            // 'operator_id' => $request->operator,
            'operator_name' => isset($datas) ? $datas : $user->operator_name,
            'service_name' => $request->servicename,
            'aggregator_status' => $request->aggregratorPermission,
            'aggregator' => $request->aggregrator,
            'subkeyword' => $request->subkeyword,
            'short_code' => $request->short_code,
            'type' => $request->type,
            'service_type' => $request->service_type,
            'channel' => serialize($channal),
            'cycle' =>  serialize($cycle),
            'is_freemium' => $request->freemiumPermission,
            'freemium' => ($request->freemiumPermission == 'no') ? NULL : $request->freemiumDays,
            // 'service_price' => $request->service_price,
            'currency_service_price' =>  $currencyJson,
            'revenue_share' => $request->revenueshare,
            'merchant_share' => $request->revenuemerchant,
            'report_source' =>  $request->report_source,
            'report_partner' => $request->report_partner,
            'sub_domain_portal' =>  $request->domain_portal,
            'portal_url' =>  $request->portal_url,
            'cms_portal' =>  $request->cms_portal,
            'username_cms_portal' =>  $request->username_portal,
            'password_cms_portal' =>  $request->password_portal,
            'url_cs_tools' =>  $request->cs_tool,
            'url_cs_tools_main' =>  $request->cs_tool_main,
            'campaign_type' =>  serialize($campaign),
            'url_postback' =>  $request->postback,
            'url_campaign' =>  $request->campaign_url,
            'product_brief_file' => isset($file) ? $file : $user->product_brief_file,
            'faq_file' => isset($file1) ? $file1 : $user->faq_file,
            'contract_file' =>  isset($file2) ? $file2 : $user->contract_file,
            'merchant_coi_file' => isset($file3) ? $file3 : $user->merchant_coi_file,
            'addendums_file' => isset($file4) ? $file4 : $user->addendums_file,
            'content_authority_letter' => isset($file5) ? $file5 : $user->content_authority_letter,
            'cor_dgt_file' => isset($file6) ? $file6 : $user->cor_dgt_file,
            'matrix_enternal_team' =>  isset($teamJson) ? $teamJson : NULL,
            'matrix_client' =>  isset($clientJson) ? $clientJson : NULL,
            'matrix_telco' => isset($telcoJson) ? $telcoJson : NULL,
            'account_manager' => $request->account_manager,
            'cs_team' => $request->csteam,
            'infra_team' => $request->infrateam,
            'cs_team' => $request->csteam,
            'account_manager' => $request->account_manager,
            'pmo' => $request->pmo,
            'backend' => $request->backend,
            'status_intregration' => isset($Data) ? $Data : $request->status_intregration,
            'project_start_date' => $request->start_date,
            'project_end_date' => $request->end_date,
            'go_live_date' => $request->go_live_date,
            'schedule_payment' => !empty($request->schedule_payment) ? $request->schedule_payment : NULL,
            'payment_come_date' => !empty($request->payment_come) ? $request->payment_come : NULL,
            'is_draf' => isset($request->is_draft) ? $request->is_draft : $user->is_draf,
            // 'is_active' => $request->is_active,
            'status_count' => ($request->is_draft == '0') ? 1 : $user->status_count
        ];
        // dd($service);
        $user->update($service);

        if ($request->is_draft  == '0') {
            $progress = ScServiceStatus::get();
            foreach ($progress as $progres) {
                // dd($request->$dute_date);
                if ($progres->id == 1) {
                    $data = [
                        'id_service' => $user->id,
                        'id_service_status' => $progres->id,
                        'dute_date' => date("Y-m-d"),
                        'complete_due_date' => date("Y-m-d"),
                        'status' => 'done',
                    ];
                } else {
                    $data = [
                        'id_service' => $user->id,
                        'id_service_status' => $progres->id,
                        'dute_date' => null,
                        'complete_due_date' => null,
                        'status' => 'pending',
                    ];
                }


                $Datas[] = $data;
            }
            ScServiceProgres::upsert($Datas, ['id_service', 'id_service_status'], ['dute_date', 'complete_due_date', 'status',]);
        }

        return redirect()->route('report.list')->with(
            'success',
            __('Service Updated Successfully!')
        );
    }

    public function draftupdate(ServiceEditRequest $request)
    {

        // dd($request->all());
        $channal = [
            $request->channelowap,
            $request->channeloussd,
            $request->channelosms,
            $request->channeloivr,
        ];
        $cycle = [
            'changeCycleDaily' => $request->changeCycleDaily,
            'changeCycleWeekly' => $request->changeCycleWeekly,
            'changeCycleMonthly' => $request->changeCycleMonthly,
        ];
        $campaign = [
            $request->sms,
            $request->wap,
            $request->api,
        ];
        $currencyata = [
            'currency' => $request['currency'],
            'service_price' => $request['service_price']
        ];
        $currencyJson = json_encode($currencyata);
        // dd($currencyJson);


        $teamData = [
            'team_name' => $request['team_name'],
            'team_email' => $request['team_email'],
            'team_whatsapp' => $request['team_whatsapp'],
            'level' => $request['level']
        ];

        // Convert the array to JSON
        $teamJson = json_encode($teamData);

        $clientData = [
            'client_name' => $request['client_name'],
            'client_email' => $request['client_email'],
            'client_whatsapp' => $request['client_whatsapp'],
        ];

        // Convert the array to JSON
        $clientJson = json_encode($clientData);

        $telcoData = [
            'telco_name' => $request['telco_name'],
            'telco_email' => $request['telco_email'],
            'telco_whatsapp' => $request['telco_whatsapp'],
        ];

        // Convert the array to JSON
        $telcoJson = json_encode($telcoData);

        $files =  $request->product_brief_file;
        $newFile = isset($files) ? $files : '';

        if (!empty($newFile)) {
            $file = 'product' . rand() . time() . '.' . $newFile->extension();
            $path = 'assets/service_catalogue/product_brief';
            $newFile->storeAs($path, $file, ['disk' => 'public_uploads']);
        }

        $faq =  $request->faq_file;
        $newFile1 = isset($faq) ? $faq : '';

        if (!empty($newFile1)) {
            $file1 = 'faq' . rand() . time() . '.' . $newFile1->extension();
            $path1 = 'assets/service_catalogue/faq_file';
            $newFile1->storeAs($path1, $file1, ['disk' => 'public_uploads']);
        }


        $contract =  $request->contract_file;
        $newFile2 = isset($contract) ? $contract : '';

        if (!empty($newFile2)) {
            $file2 = 'contract' . rand() . time() . '.' . $newFile2->extension();
            $path2 = 'assets/service_catalogue/contract_file';
            $newFile2->storeAs($path2, $file2, ['disk' => 'public_uploads']);
        }
        $coi =  $request->coi_file;
        $newFile3 = isset($coi) ? $coi : '';

        if (!empty($newFile3)) {
            $file3 = 'coi' . rand() . time() . '.' . $newFile3->extension();
            $path3 = 'assets/service_catalogue/coi_file';
            $newFile3->storeAs($path3, $file3, ['disk' => 'public_uploads']);
        }
        $adden =  $request->addendums_file;
        $newFile4 = isset($adden) ? $adden : '';

        if (!empty($newFile4)) {
            $file4 = 'addendums' . rand() . time() . '.' . $newFile4->extension();
            $path4 = 'assets/service_catalogue/addendums_file';
            $newFile4->storeAs($path4, $file4, ['disk' => 'public_uploads']);
        }
        $authority =  $request->authority_file;
        $newFile5 = isset($authority) ? $authority : '';

        if (!empty($newFile5)) {
            $file5 = 'authority' . rand() . time() . '.' . $newFile5->extension();
            $path5 = 'assets/service_catalogue/authority_file';
            $newFile5->storeAs($path5, $file5, ['disk' => 'public_uploads']);
        }
        $cor_dgt =  $request->cor_dgt_file;
        $newFile6 = isset($cor_dgt) ? $cor_dgt : '';

        if (!empty($newFile6)) {
            $file6 = 'cor_dgt' . rand() . time() . '.' . $newFile6->extension();
            $path6 = 'assets/service_catalogue/cor_dgt_file';
            $newFile6->storeAs($path6, $file6, ['disk' => 'public_uploads']);
        }
        // if($request->is_draft  == 0){
        //     $data = "On Progress Development";
        // }

        if ($request->exit_operator == 1 && !empty($request->operator)) {
            $operator = $request->operator;
        }
        if ($request->exit_operator == 0 && !empty($request->newOperatorName)) {
            $operators = $request->newOperatorName;
        }
        if (isset($operator)) {
            $datas = $operator;
        } elseif (isset($operators)) {
            $datas = $operators;
        } else {
            $datas = NULL;
        }
        $start_date = new DateTime('now');

        $user  = ScServices::findServices($request->id)->first();

        $service = [
            'country_id' => $request->country,
            'company_id' => $request->company,
            // 'operator_id' => $request->operator,
            'operator_name' => $datas,
            'exit_operator' => $request->exit_operator,
            'service_name' => $request->servicename,
            'aggregator_status' => $request->aggregratorPermission,
            'aggregator' => $request->aggregrator,
            'subkeyword' => $request->subkeyword,
            'short_code' => $request->short_code,
            'type' => $request->type,
            'service_type' => $request->service_type,
            'channel' => serialize($channal),
            'cycle' =>  serialize($cycle),
            'is_freemium' => $request->freemiumPermission,
            'freemium' => ($request->freemiumPermission == 'no') ? NULL : $request->freemiumDays,
            // 'service_price' => $request->service_price,
            'currency_service_price' =>  $currencyJson,
            'revenue_share' => $request->revenueshare,
            'merchant_share' => $request->revenuemerchant,
            'report_source' =>  $request->report_source,
            'report_partner' => $request->report_partner,
            'sub_domain_portal' =>  $request->domain_portal,
            'portal_url' =>  $request->portal_url,
            'cms_portal' =>  $request->cms_portal,
            'username_cms_portal' =>  $request->username_portal,
            'password_cms_portal' =>  $request->password_portal,
            'url_cs_tools' =>  $request->cs_tool,
            'url_cs_tools_main' =>  $request->cs_tool_main,
            'campaign_type' =>  serialize($campaign),
            'url_postback' =>  $request->postback,
            'url_campaign' =>  $request->campaign_url,
            'product_brief_file' => isset($file) ? $file : $user->product_brief_file,
            'faq_file' => isset($file1) ? $file1 : $user->faq_file,
            'contract_file' =>  isset($file2) ? $file2 : $user->contract_file,
            'merchant_coi_file' => isset($file3) ? $file3 : $user->merchant_coi_file,
            'addendums_file' => isset($file4) ? $file4 : $user->addendums_file,
            'content_authority_letter' => isset($file5) ? $file5 : $user->content_authority_letter,
            'cor_dgt_file' => isset($file6) ? $file6 : $user->cor_dgt_file,
            'matrix_enternal_team' =>  isset($teamJson) ? $teamJson : NULL,
            'matrix_client' =>  isset($clientJson) ? $clientJson : NULL,
            'matrix_telco' => isset($telcoJson) ? $telcoJson : NULL,
            'account_manager' => $request->account_manager,
            'cs_team' => $request->csteam,
            'infra_team' => $request->infrateam,
            'cs_team' => $request->csteam,
            'account_manager' => $request->account_manager,
            'pmo' => $request->pmo,
            'backend' => $request->backend,
            // 'status_intregration' => $request->status_intregration,
            'project_start_date' => !empty($request->start_date) ? $request->start_date : NULL,
            'project_end_date' => !empty($request->end_date) ? $request->end_date : NULL,
            'go_live_date' => NULL,
            'schedule_payment' => !empty($request->schedule_payment) ? $request->schedule_payment : NULL,
            'payment_come_date' => !empty($request->payment_come) ? $request->payment_come : NULL,
            // 'is_draf' => isset($request->is_draft) ? $request->is_draft : $user->is_draf,
            // 'is_active' => $request->is_active,
        ];
        // dd($service);
        $user->update($service);
        return redirect()->route('report.list')->with(
            'success',
            __('Draft Service Updated Successfully!')
        );
    }


    public function statusChange($id)
    {
        if (!\Auth::user()->can('Service List')) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'error' => __('Permission Denied.')], 403)
                : redirect()->back()->with('error', __('Permission Denied.'));
        }

        $service = ScServices::find($id);
        if (!$service) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'error' => __('Invalid User.')], 404)
                : redirect()->back()->with('error', __('Invalid User.'));
        }

        $service->is_active = !$service->is_active;
        $service->update();

        $redirectUrl = route('report.list');

        return request()->expectsJson()
            ? response()->json(['success' => true, 'redirect' => $redirectUrl])
            : redirect($redirectUrl)->with('success', __('Service Status changed successfully!'));
    }

    public function golive(Request $request)
    {
        if (!\Auth::user()->can('Service List')) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'error' => __('Permission Denied.')], 403)
                : redirect()->back()->with('error', __('Permission Denied.'));
        }
        // dd( $request->all());
        $service = ScServices::findServices($request->id)->first();
        if (!$service) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'error' => __('Invalid User.')], 404)
                : redirect()->back()->with('error', __('Invalid User.'));
        }
        $data = [

            'status_intregration' => 'Go Live',
            'is_golive' => $request->golive,
            'note' => $request->note,
            'go_live_date' => new DateTime('now'),
        ];
        // dd($data);
        $service->update($data);

        $redirectUrl = route('report.list');

        return request()->expectsJson()
            ? response()->json(['success' => true, 'redirect' => $redirectUrl])
            : redirect($redirectUrl)->with('success', __('Go Live Date Add Successfully!'));
    }

    public function operatorCreate(Request $request)
    {
        // $country = Country::GetCountryByCountryId([$request->country])->first();
        if ($request->operatorName != null) {
            // dd($request->operator);
            // $status = Operator::GetOperatorByOperatorId([$request->operator])->first();
            // dd($status);
            $data = [
                // 'country_id' => $status->country_id,
                'operator_name' => $request->operatorName,
                // 'country_name' => $status->country_name,
                'status' => 1,
            ];
            // ScOperators::upsert($data, ['country_id', 'operator_name',], ['country_name', 'status'], 'id');

            ScOperators::upsert($data, ['operator_name',], ['status'], 'id');

            // dd(ScOperators::last());
            $status = ScOperators::findByOperatorName($request->operatorName)->select('operator_name')->first();
            // $status=ScOperators::scopefindById($status)->first();
            return $status;
        }

        // $data = [
        //     'country_id' => $request->country,
        //     'operator_name' => $request->operatorName,
        //     'country_name' => $country->country,
        //     'status' => 1,
        // ];
        // ScOperators::upsert($data, ['country_id', 'operator_name',], ['country_name', 'status'],);
        // $status = ScOperators::findByCountryId($request->country)->findByOperatorName($request->operatorName)->first();
        // return $status;
    }

    public function progressCreate($id)
    {
        $progress = ScServiceStatus::get();
        $progressReports = ScServiceProgres::FindByIdService($id)->get();
        $progressOldData = [];
        foreach ($progressReports as $data) {
            $progressOldData[$data->id_service_status] = $data;
        }
        // dd($progressOldData[1]);
        return view('service.progressCreate', compact('progress', 'id', 'progressOldData'));
    }
    public function progressReport($id)
    {
        $progressReports = ScServiceProgres::FindByIdService($id)->get();
        //dd($progressReports);
        return view('service.progressReport', compact('progressReports'));
    }
    public function progressUpdate(Request $request)
    {
        $datas = [];
        $data = [];
        $progress = ScServiceStatus::get();
        foreach ($progress as $progres) {
            $file = '';
            $progresId = 'progres_' . $progres->id;
            $dute_date = 'date_' . $progres->id;
            $status = 'status_' . $progres->id;
            $note = 'note_' . $progres->id;
            $file_id = 'file_' . $progres->id;


            $files = $request->$file_id != '' ? $request->$file_id : null;
            if (!empty($files)) {
                $file = 'service_catalogue_progress_' . rand() . time() . '.' . $files->extension();
                $path = 'assets/service_catalogue/';
                $files->storeAs($path, $file, ['disk' => 'public_uploads']);
            }

            $data = [
                'id_service' => $request->service_id,
                'id_service_status' => $request->$progresId,
                'dute_date' => $request->$dute_date != '' ? $request->$dute_date : null,
                'complete_due_date' => $request->$dute_date != '' ? $request->$dute_date : null,
                'status' => $request->$status,
                'note' => $request->$note != '' ? $request->$note : null,
                'file' => $file != '' ? $file : null,
            ];

            if ($request->$status == "blocked") {
                $oldStatus = ScServiceProgres::where('id_service', $request->service_id)->where('id_service_status', $request->$progresId)->first()->status;

                if ($oldStatus != $request->$status) {
                    $pmo = DB::table('sc_services')
                        ->select('users.email', 'users.name', 'sc_services.service_name')
                        ->leftJoin('users', 'users.id', '=', 'sc_services.pmo')
                        ->where('sc_services.id', $request->service_id)
                        ->first();

                    $details = [
                        'name' => $pmo->name,
                        'service_name' => $pmo->service_name,
                        'status' => 'Blocked',
                        'task_status' => ScServiceStatus::where('id', $request->$progresId)->first()->name
                    ];

                    // Mail::to([$pmo->email])->send(new ServiceCatalogUpdateMail($details));
                }
            } else if ($request->$status == "done") {
                $oldStatus = ScServiceProgres::where('id_service', $request->service_id)->where('id_service_status', $request->$progresId)->first()->status;

                if ($oldStatus != $request->$status) {
                    $userScServices = ScServices::where('id', $request->service_id)->first();
                    $user =  $userScServices->increment('status_count');
                    // dd($user);
                    $userEmail = DB::table('users')
                        ->select('email')
                        ->whereIn('id', [$userScServices->account_manager, $userScServices->pmo])
                        ->orWhere('type', "Business Manager")
                        ->get();

                    $pmo = DB::table('sc_services')
                        ->select('users.email', 'users.name', 'sc_services.service_name')
                        ->leftJoin('users', 'users.id', '=', 'sc_services.pmo')
                        ->where('sc_services.id', $request->service_id)
                        ->first();

                    $details = [
                        'name' => "Teams",
                        'service_name' => $pmo->service_name,
                        'status' => 'Done',
                        'task_status' => ScServiceStatus::where('id', $request->$progresId)->first()->name
                    ];

                    // Mail::to($userEmail)->send(new ServiceCatalogUpdateMail($details));
                }
            }

            $datas[] = $data;
        }

        ScServiceProgres::upsert($datas, ['id_service', 'id_service_status'], ['dute_date', 'complete_due_date', 'status', 'note', 'file']);


        return redirect()->route('report.list')->with(
            'success',
            __('Service Progress Updated Successfully!')
        );

        return $request;
    }

    public function detail($id)
    {

        //edit service
        $service = ScServices::FindOrFail($id);
        $companys = Company::orderBy('name', 'ASC')->get();
        $countrys = Country::orderBy('country', 'ASC')->get();
        $operators = Operator::Status(1)->orderBy('operator_name', 'ASC')->get();
        $ScOperators = ScOperators::get();
        $notAllowuserTypes = array("Owner", "Super Admin", "Business Manager", "Admin", "BOD");
        $Users = User::Types($notAllowuserTypes)->Active()->get();
        // dd($Users);
        Session::forget('error');

        // progress create service
        $progress = ScServiceStatus::get();
        $progressReports = ScServiceProgres::FindByIdService($id)->get();
        $progressOldData = [];
        foreach ($progressReports as $data) {
            $progressOldData[$data->id_service_status] = $data;
        }

        //progressReports
        $progressReports = ScServiceProgres::FindByIdService($id)->get();

        return view('service.detail', compact('service', 'companys', 'countrys', 'operators', 'ScOperators', 'id', 'progress', 'progressOldData', 'progressReports', 'Users'));
    }

    public function checklist($id)
    {
        if (\Auth::user()->can('Service List')) {
            $service = ScServices::findServices($id)->first();
            $service['flag'] = Country::select('flag')->GetById($service['country_id'])->first();
            $service['company'] = Company::select('name')->GetById($service['company_id'])->first();
            $checklist = ServiceChecklists::Getbyserviceid($id)->first();
            if (!isset($checklist)) {
                $checklist = [];
            }
            return view('service.checklist', compact('service', 'checklist'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function checklistupdate(Request $request)
    {

        $service = ScServices::findServices($request->service_id)->select('id', 'percentage')->first();

        if (!$service) {
            abort(404, 'Service not found');
        }

        // Define all the keys
        $all_keys = [
            'pmo_1', 'pmo_2', 'pmo_3', 'pmo_4', 'pmo_5', 'pmo_6', 'pmo_7', 'pmo_8',
            'pmo_9', 'pmo_10', 'pmo_11', 'pmo_12', 'pmo_13', 'pmo_14', 'pmo_15',
            'pmo_16', 'pmo_17', 'pmo_18', 'pmo_19', 'infra_1', 'infra_2', 'infra_3',
            'infra_4', 'infra_5', 'infra_6', 'infra_7', 'business_1', 'business_2',
            'business_3', 'business_4', 'business_5','business_6', 'cs_1', 'cs_2', 'cs_3', 'cs_4',
            'cs_5', 'finance_1', 'finance_2', 'finance_3', 'finance_4',
        ];

        $count = 0;

        foreach ($all_keys as $key) {
            if ($request->has($key)) {
                $count++;
            }
        }


        // Define the fields for each category
        $categories = [
            'pmo' => range(1, 19),
            'infra' => range(1, 7),
            'business' => range(1, 6),
            'cs' => range(1, 5),
            'finance' => range(1, 4)
        ];

        // Initialize the $datas array with the service_id
        $datas = [
            'service_id' => $request->service_id
        ];

        // Helper function to process fields
        function processFields($request, $prefix, $fields)
        {
            $data = [];
            foreach ($fields as $field) {
                $key = $prefix . $field;
                if ($request->has($key)) {
                    $data[$key] = $request->input($key, 'no');
                }
            }
            return $data;
        }

        // Process each category of fields
        foreach ($categories as $prefix => $fields) {
            $datas += processFields($request, $prefix . '_', $fields);
        }

        // Get the columns to be updated based on the request
        $updateColumns = array_intersect($all_keys, array_keys($datas));

        if (!empty($datas) && !empty($updateColumns)) {
            ServiceChecklists::upsert($datas, ['service_id'], $updateColumns);
        }
        $percentage = $this->GetPercentage($request->service_id);

        $service->update([
            'percentage' => ($percentage == 0) ? $service->percentage : $percentage
        ]);

        return redirect()->route('report.list')->with('success', __('Checklist updated successfully!'));
    }

    function checklistdata($service)
    {


        if (isset($service)) {
            $data = [];

            foreach ($service as $value) {
                $checklist = ServiceChecklists::Getbyserviceid($value->id)->first();
                if ($checklist) {
                    $count = 0;

                    $conditions = [
                        [$checklist->pmo_1, $checklist->pmo_2, $checklist->pmo_3, $checklist->pmo_4, 'all'],
                        [$checklist->pmo_5, $checklist->pmo_6, 'any'],
                        [$checklist->pmo_9, $checklist->pmo_10, $checklist->pmo_11, 'any'],
                        [$checklist->pmo_12, $checklist->pmo_13, 'all'],
                        [$checklist->pmo_16, $checklist->pmo_17, 'any'],
                        [$checklist->pmo_19, 'any'],
                        [$checklist->infra_1, $checklist->infra_2, $checklist->infra_3, 'any'],
                        [$checklist->infra_4, $checklist->infra_5, 'any'],
                        [$checklist->business_1, $checklist->business_2, $checklist->business_3, $checklist->business_4, 'any'],
                        [$checklist->business_5, $checklist->business_6, 'all'],
                        [$checklist->cs_1, $checklist->cs_4, $checklist->cs_5, 'all'],
                        [$checklist->finance_2, $checklist->finance_3, 'any'],
                        [$checklist->finance_3, 'any'],

                    ];

                    foreach ($conditions as $condition) {
                        $type = array_pop($condition);
                        if (($type == 'all' && !in_array('no', $condition)) || ($type == 'any' && in_array('yes', $condition))) {
                            $count++;
                        }
                    }

                    $data[$value->id] = $count;
                }
            }

            return $data;
        } else {
            return [];
        }




    }

    function GetPercentage($id){

        $data = ServiceChecklists::Getbyserviceid($id)->first();

        if ($data) {
            $count_yes = 0;

            foreach ($data->toArray() as $key => $value) {
                if ($value === 'yes') {
                    $count_yes++;
                }
            }
            $percentage = ($count_yes == 0) ? 0 : round(($count_yes / 39) * 100, 2);
            return $percentage;
        } else {
            return 0;
        }
    }

    function dayscalculation($service) {
        if (empty($service)) {
            return [];
        }

        $data = [];

        foreach ($service as $value) {
            $Data = ScServices::findServices($value->id)->select('is_draf','project_start_date', 'project_end_date')->first();
            if($Data -> is_draf == 0){
                if ($Data && $Data->project_start_date && $Data->project_end_date) {
                    $start_date = $Data->project_start_date;
                    $end_date = $Data->project_end_date;
    
                    $start = new DateTime($start_date);
                    $end = new DateTime($end_date);
                    $end->modify('+1 day'); // Make the end date inclusive
    
                    $interval = new DateInterval('P1D');
                    $period = new DatePeriod($start, $interval, $end);
    
                    $weekday_count = 0;
                    foreach ($period as $dt) {
                        if ($dt->format('N') < 6) { // 6 and 7 are Saturday and Sunday
                            $weekday_count++;
                        }
                    }
    
                    $data[$value->id] = $weekday_count;
                } else {
                    $data[$value->id] = null; // Handle the case when dates are null or no data is found
                }

            }
        }
        return $data;
    }



}
