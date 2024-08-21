<?php

use App\Http\Controllers\ServiceCatalogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::group(['prefix' => 'admin'], function () {
Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

Route::get('/register', function () {
    return redirect('/login');
});

Route::get('/', function () {
    return abort(404);
});

Route::get('/', ['as' => 'home', 'uses' => 'Dashboard_V2_Controller@index',])->middleware(['XSS']);

Route::get('/checkrevenue', 'AnalyticController@checkrevenue')->middleware(['auth', 'XSS',]);

Route::get('/home', ['as' => 'homes', 'uses' => 'Dashboard_V2_Controller@index',])->middleware(['auth', 'XSS',]);
Route::get('/check', 'HomeController@check')->middleware(['auth', 'XSS',]);

Route::get('/profile', ['as' => 'profile', 'uses' => 'UserController@profile',])->middleware(['auth', 'XSS',]);
Route::post('/profile', ['as' => 'update.profile', 'uses' => 'UserController@updateProfile',])->middleware(['auth', 'XSS',]);
Route::post('/profile/password', ['as' => 'update.password', 'uses' => 'UserController@updatePassword',])->middleware(['auth', 'XSS',]);
Route::delete('/profile', ['as' => 'delete.avatar', 'uses' => 'UserController@deleteAvatar',])->middleware(['auth', 'XSS',]);

Route::get('/users', ['as' => 'users', 'uses' => 'UserController@index',])->middleware(['auth', 'XSS',]);
Route::post('/users', ['as' => 'users.store', 'uses' => 'UserController@store',])->middleware(['auth', 'XSS',]);
Route::get('/users/create', ['as' => 'users.create', 'uses' => 'UserController@create',])->middleware(['auth', 'XSS',]);
Route::get('/users/edit/{id}', ['as' => 'users.edit', 'uses' => 'UserController@edit',])->middleware(['auth', 'XSS',]);
Route::get('/users/{id}', ['as' => 'users.show', 'uses' => 'UserController@show'])->middleware(['auth', 'XSS']);
Route::post('/users/{id}', ['as' => 'users.update', 'uses' => 'UserController@update',])->middleware(['auth', 'XSS',]);
Route::delete('/users/{id}', ['as' => 'users.destroy', 'uses' => 'UserController@destroy',])->middleware(['auth', 'XSS',]);
Route::post('/users/update/{id}', ['as' => 'users.update.status', 'uses' => 'UserController@status',])->middleware(['auth', 'XSS',]);
Route::post('/userCreateFromCsv', ['as' => 'userCreateFromCsv', 'uses' => 'UserController@userCreateFromCsv',])->middleware(['auth', 'XSS',]);
Route::post('/profile/userpassword', ['as' => 'update.userpassword', 'uses' => 'UserController@userpassword',])->middleware(['auth', 'XSS',]);
Route::get('/management/pivot', ['as' => 'management.pivot', 'uses' => 'PivotFilterController@index',])->middleware(['auth', 'XSS',]);
Route::post('/management/pivot/update', ['as' => 'management.pivot.update', 'uses' => 'PivotFilterController@update',])->middleware(['auth', 'XSS',]);

Route::post('/companies', ['as' => 'companies.create', 'uses' => 'ManagementController@createCompany',])->middleware(['auth', 'XSS',]);
// Route::post('/companies/{id}', ['as' => 'companies.update','uses' => 'ManagementController@updateCompany',])->middleware(['auth','XSS',]);
Route::post('/countries/', ['as' => 'countries.create', 'uses' => 'ManagementController@createCountry',])->middleware(['auth', 'XSS',]);
Route::post('/countries/{id}', ['as' => 'countries.update', 'uses' => 'ManagementController@updateCountry',])->middleware(['auth', 'XSS',]);

Route::post('/companies/{id}', ['as' => 'companies.update', 'uses' => 'CompanyController@updateCompany',])->middleware(['auth', 'XSS',]);

Route::get('/test', ['as' => 'test.email', 'uses' => 'SettingsController@testEmail',])->middleware(['auth', 'XSS',]);
Route::post('/test/send', ['as' => 'test.email.send', 'uses' => 'SettingsController@testEmailSend',])->middleware(['auth', 'XSS',]);
Route::post('/payment-settings', 'SettingsController@adminPaymentSettings')->name('payment.settings')->middleware(['auth', 'XSS']);

Route::post('/template-setting', ['as' => 'template.setting', 'uses' => 'SettingsController@saveTemplateSettings'])->middleware(['auth', 'XSS']);
Route::get('/invoices/preview/{template}/{color}', ['as' => 'invoice.preview', 'uses' => 'InvoiceController@previewInvoice']);
Route::get('/invoices/preview/{template}/{color}', ['as' => 'invoice.preview', 'uses' => 'InvoiceController@previewInvoice']);

Route::post('/site-settings', ['as' => 'site.settings.store', 'uses' => 'SettingsController@site_setting',])->middleware(['auth', 'XSS',]);
Route::get('/ip-settings', ['as' => 'add.ip.client', 'uses' => 'SettingsController@addIp',])->middleware(['auth', 'XSS',]);
Route::get('/ip-settings/{id}', ['as' => 'get.ip.client', 'uses' => 'SettingsController@updateIp',])->middleware(['auth', 'XSS',]);
Route::delete('/ip-settings', ['as' => 'delete.ip.client', 'uses' => 'SettingsController@deleteIp',])->middleware(['auth', 'XSS',]);
Route::put('/ip-settings', ['as' => 'update.ip.client', 'uses' => 'SettingsController@saveUpdateIp',])->middleware(['auth', 'XSS',]);
Route::post('/ip-settings', ['as' => 'store.ip.client', 'uses' => 'SettingsController@storeIp',])->middleware(['auth', 'XSS',]);

Route::get('/settings', ['as' => 'settings', 'uses' => 'SettingsController@index',])->middleware(['auth', 'XSS',]);
Route::post('/settings', ['as' => 'settings.store', 'uses' => 'SettingsController@store',])->middleware(['auth', 'XSS',]);
Route::post('/middleware-settings', ['as' => 'middleware.store', 'uses' => 'SettingsController@middlewareUpdate',])->middleware(['auth', 'XSS',]);

Route::get('/{uid}/notification/seen', ['as' => 'notification.seen', 'uses' => 'UserController@notificationSeen']);

/* fake Router */
Route::post('/message_data', 'SettingsController@savePaymentSettings')->name('message.data')->middleware(['auth', 'XSS']);

Route::post('/message_seen', 'SettingsController@savePaymentSettings')->name('message.seen')->middleware(['auth', 'XSS']);
//================================= Invoice Payment Gateways  ====================================//

Route::get('/search', ['as' => 'search.json', 'uses' => 'UserController@search']);
//
Route::get('/invoices/payments', ['as' => 'invoices.payments', 'uses' => 'InvoiceController@payments',])->middleware(['auth', 'XSS',]);

Route::resource('roles', 'RoleController');
Route::prefix('roles')->middleware(['auth', 'XSS',])->group(function () {

    Route::get('/{id}/operator', 'RoleController@addOperator')->name('roles.operator')->middleware(['auth', 'XSS',]);
    Route::post('/operator/store', 'RoleController@storeOperator')->name('roles.operator.store')->middleware(['auth', 'XSS',]);
});
Route::resource('permissions', 'PermissionController');

/* Reports Web */
Route::prefix('report')->middleware(['auth', 'XSS',])->group(function () {
    Route::post('/country', 'ReportController@country')->name('report.country');
    Route::post('/operator', 'ReportController@operator')->name('report.operator');
    Route::post('/user/filter/country', 'ReportController@userFilterCountry')->name('report.user.filter.country');
    Route::post('/user/filter/operator', 'ReportController@userFilterOperator')->name('report.user.filter.operator');
    Route::post('/user/filter/business/operator', 'ReportController@userFilterBusinessTypeOperator')->name('report.user.filter.business.operator');
    Route::post('/service', 'ReportController@service')->name('report.service');

    // report summary routes
    Route::get('/summary', 'ReportController@summary')->name('report.summary');
    Route::get('/summary/country', 'ReportController@CountryWiseSummary')->name('report.summary.daily.country');
    Route::get('/summary/monthly/country', 'MonthlyReportController@MonthlyReportCountry')->name('report.summary.monthly.country');
    Route::get('/summary/monthly', 'MonthlyReportController@MonthlyReportOperator')->name('report.summary.monthly.operator');
    Route::get('/summary/manager', 'ReportController@DailyReportManager')->name('report.summary.daily.manager');
    Route::get('/summary/monthly/manager', 'MonthlyReportController@MonthlyReportManager')->name('report.summary.monthly.manager');
    Route::get('/summary/business', 'ReportController@BusinessWiseSummary')->name('report.summary.daily.business');
    Route::get('/summary/monthly/business', 'MonthlyReportController@MonthlyBusinessWise')->name('report.summary.monthly.business');
    // report detials routes
    Route::get('/details/{id?}', 'ReportDetails@reportingdetails')->name('report.details');
    // report detials routes
    Route::get('/service/details/{id?}', 'ReportDetails@reportServiceDetails')->name('report.service.details');

    // report pnlsummary routes
    Route::get('/pnlsummary', 'PnlReportController@pnlsummary')->name('report.pnlsummary');
    Route::get('/pnldetails/{id?}', 'ReportDetails@reportPnlDetails')->name('report.pnldetails');
    Route::get('/pnldetail', 'PnlDailtyReportDetailsController@DailyPnlReportOperatorDetails')->name('report.daily.operator.pnldetail');
    Route::get('/pnldetail/operator', 'PnlDailtyReportDetailsController@DailyPnlReportOperatorDetails')->name('report.daily.operator.pnldetails');
    Route::get('/pnldetail/country', 'PnlDailtyReportDetailsController@DailyPnlReportCountryDetails')->name('report.daily.country.pnldetails');
    Route::get('/pnldetail/company', 'PnlDailtyReportDetailsController@DailyPnlReportCompanyDetails')->name('report.daily.company.pnldetails');
    Route::get('/pnldetail/business', 'PnlDailtyReportDetailsController@DailyPnlReportBusinessDetails')->name('report.daily.business.pnldetails');

    Route::get('/pnldetail/monthly', 'PnlMonthlyReportDetailsController@MonthlyPnlReportOperatorDetails')->name('report.monthly.operator.pnldetail');
    Route::get('/pnldetail/monthly/operator', 'PnlMonthlyReportDetailsController@MonthlyPnlReportOperatorDetails')->name('report.monthly.operator.pnldetails');
    Route::get('/pnldetail/monthly/country', 'PnlMonthlyReportDetailsController@MonthlyPnlReportCountryDetails')->name('report.monthly.country.pnldetails');
    Route::get('/pnldetail/monthly/company', 'PnlMonthlyReportDetailsController@MonthlyPnlReportCompanyDetails')->name('report.monthly.company.pnldetails');
    Route::get('/pnldetail/monthly/business', 'PnlMonthlyReportDetailsController@MonthlyPnlReportBusinessDetails')->name('report.monthly.business.pnldetails');

    Route::get('/pnlsummary/operator', 'PnlReportController@pnlsummary')->name('report.pnlsummary.daily.operator');
    Route::get('/pnlsummary/monthly', 'MonthlyPnlReportController@MonthlyPnlSummaryOperator')->name('report.pnlsummary.monthly.operator');
    Route::get('/pnlsummary/country', 'PnlReportController@DailyPnlReportCountry')->name('report.pnlsummary.daily.country');
    Route::get('/pnlsummary/monthly/country', 'MonthlyPnlReportController@MonthlyPnlSummaryCountry')->name('report.pnlsummary.monthly.country');
    Route::get('/pnlsummary/company', 'PnlReportController@DailyPnlReportCompany')->name('report.pnlsummary.daily.company');
    Route::get('/pnlsummary/monthly/company', 'MonthlyPnlReportController@MonthlyPnlSummaryCompany')->name('report.pnlsummary.monthly.company');
    Route::get('/pnlsummary/business', 'PnlReportController@DailyPnlReportBusinessType')->name('report.pnlsummary.daily.business');
    Route::get('/pnlsummary/monthly/business', 'MonthlyPnlReportController@MonthlyPnlSummaryBusinessType')->name('report.pnlsummary.monthly.business');
    Route::get('/unknown/operator', 'PnlReportController@unknown_operator')->name('report.unknown_operator');



    //report roi monitor
    Route::get('roi/operator','MonthlyReportController@RoiMonitorOperatorWise')->name('roi.monitor.operator');
    Route::get('roi/country','MonthlyReportController@RoiMonitorCountryWise')->name('roi.monitor.country');

    // report adnet details
    Route::post('/mappingoperator', 'AdnetController@mappingoperator')->name('report.mappingoperator');
    Route::post('/mappingservice', 'AdnetController@mappingservice')->name('report.mappingservice');
    Route::post('/mappingkeyword', 'AdnetController@mappingkeyword')->name('report.mappingkeyword');
    Route::get('/adnetreport', 'AdnetController@index')->name('report.adnetreport');
    Route::get('/adnetreport/detail', 'AdnetController@detailSubs')->name('report.detail.subs');
    Route::get('/adnetreport/detail/cs-tool', 'AdnetController@cstool')->name('report.detail.cstool');
    Route::get('/exportExcel', 'ExportController@exportExcel')->name('export.excel');
});

Route::prefix('dashboard')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/country', 'Dashboard_V2_Controller@index')->name('dashboard.country.tesing');
    Route::get('/operator', 'Dashboard_V2_Controller@operatorDashboard')->name('dashboard.operator');
    Route::get('/company', 'Dashboard_V2_Controller@companyDashboard')->name('dashboard.company');
    Route::get('/business', 'Dashboard_V2_Controller@businessDashboard')->name('dashboard.business');
    /*Route::get('/country', 'DashboardController@index')->name('dashboard.country');
    Route::get('/operator','DashboardController@operatorDashboard')->name('dashboard.operator');
    Route::get('/company', 'DashboardController@companyDashboard')->name('dashboard.company');*/
    Route::post('/getsummarygraphdata', 'Controller@Getsummarygraphdata');
    Route::post('/getmixedgraphaxesdata', 'Controller@Getmixedgraphaxesdata');
    Route::post('/getmixedgraphdata', 'Controller@Getmixedgraphdata');
});

Route::prefix('analytic')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/adsMonitoring/operator', 'AnalyticController@adsMonitoring')->name('analytic.adsMonitoring.operator');
    Route::get('/adsMonitoring/country', 'AnalyticController@countryWiseAdsMonitoring')->name('analytic.adsMonitoring.country');
    Route::get('/adsMonitoring/company', 'AnalyticController@companyWiseAdsMonitoring')->name('analytic.adsMonitoring.company');
    Route::get('/adsMonitoring/business', 'AnalyticController@businessWiseAdsMonitoring')->name('analytic.adsMonitoring.business');
    Route::get('/adsMonitoring/details', 'AnalyticController@detailsAdsMonitoring')->name('analytic.adsMonitoring.details');

    Route::get('/revenueMonitoring/operator', 'AnalyticController@revenueMonitoring')->name('analytic.revenueMonitoring');
    Route::get('/revenueMonitoring/country', 'AnalyticController@revenueMonitoringCountryWise')->name('analytic.revenueMonitoring.country');
    Route::get('/revenueMonitoring/company', 'AnalyticController@revenueMonitoringCompanyWise')->name('analytic.revenueMonitoring.company');
    Route::get('/revenueMonitoring/business', 'AnalyticController@revenueMonitoringBusinessWise')->name('analytic.revenueMonitoring.business');
    Route::get('/revenueMonitoring/operator/monthly', 'MonthlyAnalyticController@revenueMonitoringMonthly')->name('analytic.revenueMonitoringMonthly');
    Route::get('/revenueMonitoring/country/monthly', 'MonthlyAnalyticController@revenueMonitoringCountryWiseMonthly')->name('analytic.revenueMonitoring.countryMonthly');
    Route::get('/revenueMonitoring/company/monthly', 'MonthlyAnalyticController@revenueMonitoringCompanyWiseMonthly')->name('analytic.revenueMonitoring.companyMonthly');
    Route::get('/revenueMonitoring/business/monthly', 'MonthlyAnalyticController@revenueMonitoringBusinessWiseMonthly')->name('analytic.revenueMonitoring.businessMonthly');
    Route::get('/revenue/alert', 'AnalyticController@revenueAlert')->name('analytic.revenueAlert');

    Route::get('/roi', 'AnalyticController@roi')->name('analytic.roi');
    Route::get('/roi/createNewWeeklyCaps', 'AnalyticController@createNewWeeklyCaps')->name('analytic.roi.createNewWeeklyCaps');
    Route::post('/roi/storeNewWeeklyCaps', 'AnalyticController@storeNewWeeklyCaps')->name('analytic.roi.storeNewWeeklyCaps');

    Route::get('/logPerformance', 'AnalyticController@logPerformance')->name('analytic.logPerformance');

     //report monitor
     Route::get('/monitor/operator', 'AnalyticController@ReportMonitorOperatorWise')->name('analytic.monitor.daily.operator');
     Route::get('/monitor/country', 'AnalyticController@ReportMonitorCountryWise')->name('analytic.monitor.daily.country');
     Route::get('/monitor/operator/monthly', 'MonthlyAnalyticController@ReportMonitorOperatorWise')->name('analytic.monitor.monthly.operator');
     Route::get('/monitor/country/monthly', 'MonthlyAnalyticController@ReportMonitorCountryWise')->name('analytic.monitor.monthly.country');
});


Route::prefix('management')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/user', 'ManagementController@userManagement')->name('management.user');
    Route::get('/revShare', 'ManagementController@revShareManagement')->name('management.revShare');
    Route::get('/users/{id}/operator/service', 'ManagementController@showUserOperator')->name('users.show.operator');
    Route::post('/user/operator/store', 'ManagementController@userOperatorStore')->name('management.user.operator.store');
    Route::get('/companyAssign', 'ManagementController@companyAssign')->name('management.companyAssign');
    // Route::get('/company', 'ManagementController@companyManagement')->name('management.company');
    Route::get('/company', 'CompanyController@index')->name('management.company');
    Route::get('/add-company/', 'ManagementController@addCompany')->name('management.add-company');
    // Route::get('/edit-company/{id}', 'ManagementController@editCompany')->name('management.edit-company');
    Route::get('/currency', 'ManagementController@currencyManagement')->name('management.currency');
    Route::get('/add-currency/', 'ManagementController@addCurrency')->name('management.add-currency');
    Route::get('/edit-currency/{id}', 'ManagementController@editCurrency')->name('management.edit-currency');
    Route::get('/operator', 'ManagementController@operatorManagement')->name('management.operator');
    Route::get('/operator/{id}/edit', 'ManagementController@operatorNameEdit')->name('management.operator.edit');
    Route::post('/operator/name/update', 'ManagementController@operatorNameUpdate')->name('operator.name.update');
    Route::post('/update_operator', 'ManagementController@update_operator');
    Route::get('/company-operator/{id}', 'CompanyController@addOperator')->name('management.company-operator');
    Route::post('/operator/store', 'CompanyController@store_operator');
    Route::get('/edit-company/{id}', 'CompanyController@editCompany')->name('management.edit-company');
    Route::get('/edit-operator/{id}', 'CompanyController@editOperator')->name('management.edit-operator');
    Route::get('/view-operators/{id}', 'CompanyController@all_com_operators')->name('management.view-operators');
    Route::get('/view-unknown-company', 'CompanyController@all_unknown_operators')->name('management.view-unknown-company');
    // Route::get('/operator', 'OperatorController@index')->name('management.operator');
    Route::get('/rev_share/{id}', 'OperatorController@create_rev_share')->name('management.rev-share');
    Route::post('/updateRev/{id}', ['as' => 'operator.updateRev', 'uses' => 'OperatorController@updateRev_Share',]);
    Route::get('/date/rev/share/{id}', 'OperatorController@createRevshareByDate')->name('management.revShare.date');
    Route::post('/update/rev/date', 'OperatorController@updateRevshareByDate')->name('management.revShare.update.date');
    Route::get('/project', 'ManagementController@projectManagement')->name('project.management');
    Route::get('/date/vat-wht/{id}', 'OperatorController@createVatWhtByDate')->name('management.VatWht.date');
    Route::post('/update/vat-wht/date', 'OperatorController@updateVatWhtByDate')->name('management.vatwht.update.date');
    // Route::get('/pmo-statistic', 'ManagementController@pmoStatistic')->name('pmo.statistic');
});

Route::prefix('finance')->group(function () {
    Route::get('/revenueReconcile', 'FinanceController@revenueReconcile')->name('finance.revenueReconcile')->middleware(['auth', 'XSS',]);
    Route::get('/createRevenueReconcile', 'FinanceController@createRevenueReconcile')->name('finance.createRevenueReconcile')->middleware(['auth', 'XSS',]);
    Route::post('/storeRevenueReconcile', 'FinanceController@storeRevenueReconcile')->name('finance.storeRevenueReconcile')->middleware(['auth', 'XSS',]);
    Route::get('/popup', 'FinanceController@popup')->name('finance.popup');
    Route::post('/importRevenueReconcile', 'FinanceController@storeRevenueReconcileExcel')->name('finance.importRevenueReconcile')->middleware(['auth', 'XSS',]);
    Route::get('/serviceReconcileData/{id}', 'FinanceController@serviceReconcileData')->name('finance.serviceReconcileData');

    Route::get('/targetRevenue', 'FinanceController@targetRevenue')->name('finance.targetRevenue')->middleware(['auth', 'XSS',]);
    Route::get('/targetRevenue/company', 'FinanceController@targetRevenueCompany')->name('finance.targetRevenue.company')->middleware(['auth', 'XSS',]);
    Route::get('/createTargetRevenueReconcile', 'FinanceController@createTargetRevenueReconcile')->name('finance.createTargetRevenueReconcile')->middleware(['auth', 'XSS',]);
    Route::post('/storeTargetRevenueReconcile', 'FinanceController@storeTargetRevenueReconcile')->name('finance.storeTargetRevenueReconcile')->middleware(['auth', 'XSS',]);
    Route::post('/importTargetRevenue', 'FinanceController@storeTargetRevenueExcel')->name('finance.importTargetRevenue')->middleware(['auth', 'XSS',]);
    Route::get('/serviceTargetData/{id}', 'FinanceController@serviceTargetData')->name('finance.serviceTargetData');
    Route::get('/createTargetOpex', 'FinanceController@createTargetOpex')->name('finance.createTargetOpex')->middleware(['auth', 'XSS',]);
    Route::post('/storeTargetOpex', 'FinanceController@storeTargetOpex')->name('finance.storeTargetOpex')->middleware(['auth', 'XSS',]);
    Route::post('/importTargetOpex', 'FinanceController@storeTargetOpexExcel')->name('finance.importTargetOpex')->middleware(['auth', 'XSS',]);

    Route::get('/financeCostReport', 'FinanceController@financeCostReport')->name('finance.financeCostReport')->middleware(['auth', 'XSS',]);
    Route::get('/createFinanceCostReport', 'FinanceController@createFinanceCostReport')->name('finance.createFinanceCostReport')->middleware(['auth', 'XSS',]);
    Route::post('/storeFinanceCostReport', 'FinanceController@storeFinanceCostReport')->name('finance.storeFinanceCostReport')->middleware(['auth', 'XSS',]);
    Route::post('/importFinanceCostReport', 'FinanceController@storeFinanceCostReportExcel')->name('finance.importFinanceCostReport')->middleware(['auth', 'XSS',]);
    Route::get('/serviceCostData/{id}', 'FinanceController@serviceCostData')->name('finance.serviceCostData');

    //reconcialiation media
    Route::get('/reconcialiation/operator','ReportController@DailyOperatorReconcialiation')->name('finance.reconcialiation.daily.operator');
    Route::get('/reconcialiation/country','ReportController@DailyCountryReconcialiation')->name('finance.reconcialiation.daily.country');
    // Route::get('/reconcialiation/operator/monthly', 'MonthlyReportController@ReportMonitorOperatorWise')->name('finance.reconcialiation.monthly.operator');
    // Route::get('/reconcialiation/country/monthly', 'MonthlyReportController@ReportMonitorCountryWise')->name('finance.reconcialiation.monthly.country');
    Route::get('/createReconcialiation', 'ReportController@createReconcialiation')->name('finance.createReconcialiation')->middleware(['auth', 'XSS',]);
    Route::post('/importReconcialiation', 'ReportController@storeReconcialiationExcel')->name('finance.importReconcialiation')->middleware(['auth', 'XSS',]);

    Route::post('/user/filter/country', 'FinanceController@userFilterCountry')->name('finance.user.filter.country');
    Route::post('/user/filter/operator', 'FinanceController@userFilterOperator')->name('finance.user.filter.operator');
    Route::post('/user/filter/business/operator', 'FinanceController@userFilterBusinessManagerOperator')->name('finance.user.filter.business.operator');

    Route::get('/downloadFile', 'FinanceController@downloadFile')->name('finance.downloadFile')->middleware(['auth', 'XSS',]);
});

Route::prefix('activity')->group(function () {
    Route::get('/user', 'ActivityLogController@user')->name('activity.user')->middleware(['auth', 'XSS',]);
    Route::get('/system', 'ActivityLogController@system')->name('activity.system')->middleware(['auth', 'XSS',]);
});

Route::prefix('report-log')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/pageLoad', 'LogFileController@pageLoad')->name('logfile.pageLoad');
    Route::get('/dataUpdate', 'LogFileController@dataUpdate')->name('logfile.dataUpdate');
    Route::get('/query', 'LogFileController@query')->name('logfile.query');
    Route::get('/currencyExchange', 'LogFileController@currencyExchange')->name('logfile.currencyExchange');
    Route::get('/list', 'LogFileController@cron')->name('logfile.cron');
    Route::get('/deleteCron', 'LogFileController@deleteCron')->name('logfile.deleteCron');
    Route::get('/downloadCron/{path?}/{foldername?}', 'LogFileController@downloadCron')->name('logfile.downloadCron');
});
Route::prefix('arpu')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/summary', 'ArpuLogsController@summary')->name('arpu.summary');
    Route::get('/detail-operator/{country}/{operator}', 'ArpuLogsController@detailOperator');
});

Route::prefix('service')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/create', 'ServiceCatalogController@create')->name('report.create');
    Route::post('/store', 'ServiceCatalogController@store')->name('report.store');
    Route::get('/list', 'ServiceCatalogController@list')->name('report.list');
    Route::get('/edit/{id}', 'ServiceCatalogController@edit')->name('report.edit');
    Route::post('/update', 'ServiceCatalogController@update')->name('report.update');
    Route::post('/operator/create', 'ServiceCatalogController@operatorCreate')->name('report.operatorCreate');
    Route::get('/detail/{id}', 'ServiceCatalogController@detail')->name('report.detail');
    Route::post('/update/status/{id}', 'ServiceCatalogController@statusChange')->name('service.updatestatus');
    Route::post('/draft/store', 'ServiceCatalogController@draftstore')->name('draft.store');
    Route::post('/draft/update', 'ServiceCatalogController@draftupdate')->name('draft.update');
    Route::post('/golive', 'ServiceCatalogController@golive')->name('service.golive');
    Route::get('/checklist/{id}', 'ServiceCatalogController@checklist')->name('service.checklist');
    Route::post('/checklist/update', 'ServiceCatalogController@checklistupdate')->name('checklist.update');

});

Route::prefix('progress')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/create/{id}', 'ServiceCatalogController@progressCreate')->name('report.progress.create');
    Route::get('/reoprt/{id}', 'ServiceCatalogController@progressReport')->name('report.progress.reoprt');
    Route::post('/update', 'ServiceCatalogController@progressUpdate')->name('report.progress.update');
});

Route::prefix('tools')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/show', 'CsToolsController@show')->name('report.tools.show');
    // Route::get('/reoprt/{id}', 'ServiceCatalogController@progressReport')->name('report.progress.reoprt');
    Route::post('/update', 'CsToolsController@Update')->name('report.tools.update');
    Route::delete('/unsubs', 'CsToolsController@unsubs')->name('unsubs');
    Route::delete('/blacklist', 'CsToolsController@blacklist')->name('blacklist');
    Route::post('/blacklist', 'CsToolsController@blacklist')->name('report.tools.blacklist');
    Route::post('/cs-activity', 'CsToolsController@updateCsActivity')->name('update.cs.activity');
});
Route::prefix('notification')->middleware(['auth'])->group(function () {

    Route::get('/notification/{countryId}', 'NotificationController@details')->name('details.notification');
    Route::get('/list', 'NotificationController@index')->name('notification.report.index');
    Route::get('/detail-deployment/{id}', 'NotificationController@detailDeployment')->name('notification.report.detail.deployment');
    Route::get('/detail-incident/{id}', 'NotificationController@detailIncident')->name('notification.report.detail.incident');
    Route::get('/create', 'NotificationController@create')->name('notification.report.create');
    Route::post('/create', 'NotificationController@addNotification')->name('add.notification.deployment');
    Route::post('/update-deployment', 'NotificationController@updateNotificationDeploy')->name('update.notification.deployment');
    Route::post('/update-incident', 'NotificationController@updateNotificationIncident')->name('update.notification.incident');
    Route::post('/create-incident', 'NotificationController@addNotificationIncident')->name('add.notification.incident');
    Route::delete('/delete-incident', 'NotificationController@deleteIncident')->name('delete.notification.incident');
    Route::delete('/delete-deployment', 'NotificationController@deleteDeployment')->name('delete.notification.deployment');
});
Route::prefix('pivot')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/summary', 'PivotReportSummery@pivotsummary')->name('report.pivot.summary');
    Route::get('/summary/operator', 'PivotReportSummery@DailyPivotReportOperator')->name('report.pivotsummary.daily.operator');
    Route::get('/summary/country', 'PivotReportSummery@DailyPivotReportCountry')->name('report.pivotsummary.daily.country');
    Route::get('/summary/company', 'PivotReportSummery@DailyPivotReportCompany')->name('report.pivotsummary.daily.company');
    Route::get('/summary/manager', 'PivotReportSummery@DailyPivotReportManager')->name('report.pivotsummary.daily.manager');
});

Route::prefix('product')->middleware(['auth', 'XSS',])->group(function () {
    Route::get('/', 'ProductController@index')->name('report.product.index');
    Route::post('/store', 'ProductController@store')->name('report.product.store');
    Route::get('/edit/{id}', 'ProductController@edit')->name('report.product.edit');
    Route::post('/update', 'ProductController@update')->name('report.product.update');
    Route::get('/list', 'ProductController@list')->name('report.product.list');
});

Route::get('/error', 'UserController@error')->name('error');

/* Clear application cache: */
Route::get('/clear-cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return 'Application cache has been cleared';
});

/* Clear route cache: */
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return 'Routes cache has been cleared';
});

/* Clear config cache: */
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return 'Config cache has been cleared';
});

/* Clear view cache: */
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return 'View cache has been cleared';
});

/* Clear optimize */
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize:clear');
    return 'Configuration & Route cache cleared successfully';
});

/* Clear permission cache */
Route::get('/permission-clear', function () {
    $exitCode = Artisan::call('permission:cache-reset');
    return 'Permission cache cleared successfully';
});


