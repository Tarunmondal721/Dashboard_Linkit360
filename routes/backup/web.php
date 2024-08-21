<?php

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

	require __DIR__.'/auth.php';


	Route::get('/register', function () {
	    return redirect('/login');
	});

	Route::get('/', function(){
	   return abort(404);
	});

	Route::get('/', ['as' => 'home','uses' => 'HomeController@index',])->middleware(['XSS']);
	Route::get('/home', ['as' => 'home','uses' => 'HomeController@index',])->middleware(['auth','XSS',]);
	Route::get('/check', 'HomeController@check')->middleware(['auth','XSS',]);
	Route::get('/profile', ['as' => 'profile','uses' => 'UserController@profile',])->middleware(['auth','XSS',]);
	Route::post('/profile', ['as' => 'update.profile','uses' => 'UserController@updateProfile',])->middleware(['auth','XSS',]);
	Route::post('/profile/password', ['as' => 'update.password','uses' => 'UserController@updatePassword',])->middleware(['auth','XSS',]);
	Route::delete('/profile', ['as' => 'delete.avatar','uses' => 'UserController@deleteAvatar',])->middleware(['auth','XSS',]);

	Route::get('/users', ['as' => 'users','uses' => 'UserController@index',])->middleware(['auth','XSS',]);
	Route::post('/users', ['as' => 'users.store','uses' => 'UserController@store',])->middleware(['auth','XSS',]);
	Route::get('/users/create', ['as' => 'users.create','uses' => 'UserController@create',])->middleware(['auth','XSS',]);
	Route::get('/users/edit/{id}', ['as' => 'users.edit','uses' => 'UserController@edit',])->middleware(['auth','XSS',]);
	Route::get('/users/{id}',['as' => 'users.show','uses' =>'UserController@show'])->middleware(['auth','XSS']);
	Route::put('/users/{id}', ['as' => 'users.update','uses' => 'UserController@update',])->middleware(['auth','XSS',]);
	Route::delete('/users/{id}', ['as' => 'users.destroy','uses' => 'UserController@destroy',])->middleware(['auth','XSS',]);

	Route::get('/lang/create', ['as' => 'lang.create','uses' => 'UserController@createLang',])->middleware(['auth','XSS',]);
	Route::get('/lang/{lang}', ['as' => 'lang','uses' => 'UserController@lang',])->middleware(['auth','XSS',]);
	Route::post('/lang', ['as' => 'lang.store','uses' => 'UserController@storeLang',])->middleware(['auth','XSS',]);
	Route::post('/lang/data/{lang}', ['as' => 'lang.store.data','uses' => 'UserController@storeLangData',])->middleware(['auth','XSS',]);
	Route::get('/lang/change/{lang}', ['as' => 'lang.change','uses' => 'UserController@changeLang',])->middleware(['auth','XSS',]);
	Route::delete('/lang/{id}',['as' => 'lang.destroy','uses' =>'UserController@destroyLang'])->middleware(['auth','XSS']);

	Route::post('/site-settings', ['as' => 'site.settings.store','uses' => 'SettingsController@site_setting',])->middleware(['auth','XSS',]);
	Route::get('/settings', ['as' => 'settings','uses' => 'SettingsController@index',])->middleware(['auth','XSS',]);
	Route::post('/settings', ['as' => 'settings.store','uses' => 'SettingsController@store',])->middleware(['auth','XSS',]);
	Route::get('/test', ['as' => 'test.email','uses' => 'SettingsController@testEmail',])->middleware(['auth','XSS',]);
	Route::post('/test/send', ['as' => 'test.email.send','uses' => 'SettingsController@testEmailSend',])->middleware(['auth','XSS',]);
	Route::post('/template-setting',['as' => 'template.setting','uses' =>'SettingsController@saveTemplateSettings'])->middleware(['auth','XSS']);
	//Route::post('/payment-settings', 'SettingsController@savePaymentSettings')->name('payment.settings')->middleware(['auth','XSS']);
	Route::post('/payment-settings', 'SettingsController@adminPaymentSettings')->name('payment.settings')->middleware(['auth','XSS']);
	// Deal Module
	Route::post('/deals/user', ['as' => 'deal.user.json','uses' => 'DealController@jsonUser',]);
	Route::post('/deals/order', ['as' => 'deals.order','uses' => 'DealController@order',])->middleware(['auth','XSS',]);
	Route::post('/deals/change-pipeline', ['as' => 'deals.change.pipeline','uses' => 'DealController@changePipeline',])->middleware(['auth','XSS',]);
	Route::post('/deals/change-deal-status/{id}', ['as' => 'deals.change.status','uses' => 'DealController@changeStatus',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/labels', ['as' => 'deals.labels','uses' => 'DealController@labels',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/labels', ['as' => 'deals.labels.store','uses' => 'DealController@labelStore',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/users', ['as' => 'deals.users.edit','uses' => 'DealController@userEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/users', ['as' => 'deals.users.update','uses' => 'DealController@userUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/users/{uid}', ['as' => 'deals.users.destroy','uses' => 'DealController@userDestroy',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/clients', ['as' => 'deals.clients.edit','uses' => 'DealController@clientEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/clients', ['as' => 'deals.clients.update','uses' => 'DealController@clientUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/clients/{uid}', ['as' => 'deals.clients.destroy','uses' => 'DealController@clientDestroy',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/products', ['as' => 'deals.products.edit','uses' => 'DealController@productEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/products', ['as' => 'deals.products.update','uses' => 'DealController@productUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/products/{uid}', ['as' => 'deals.products.destroy','uses' => 'DealController@productDestroy',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/sources', ['as' => 'deals.sources.edit','uses' => 'DealController@sourceEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/sources', ['as' => 'deals.sources.update','uses' => 'DealController@sourceUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/sources/{uid}', ['as' => 'deals.sources.destroy','uses' => 'DealController@sourceDestroy',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/file', ['as' => 'deals.file.upload','uses' => 'DealController@fileUpload',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/file/{fid}', ['as' => 'deals.file.download','uses' => 'DealController@fileDownload',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/file/delete/{fid}', ['as' => 'deals.file.delete','uses' => 'DealController@fileDelete',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/note', ['as' => 'deals.note.store','uses' => 'DealController@noteStore',])->middleware(['auth']);
	Route::get('/deals/{id}/task', ['as' => 'deals.tasks.create','uses' => 'DealController@taskCreate',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/task', ['as' => 'deals.tasks.store','uses' => 'DealController@taskStore',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/task/{tid}/show', ['as' => 'deals.tasks.show','uses' => 'DealController@taskShow',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/task/{tid}/edit', ['as' => 'deals.tasks.edit','uses' => 'DealController@taskEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/task/{tid}', ['as' => 'deals.tasks.update','uses' => 'DealController@taskUpdate',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/task_status/{tid}', ['as' => 'deals.tasks.update_status','uses' => 'DealController@taskUpdateStatus',])->middleware(['auth','XSS',]);
	Route::delete('/deals/{id}/task/{tid}', ['as' => 'deals.tasks.destroy','uses' => 'DealController@taskDestroy',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/discussions', ['as' => 'deals.discussions.create','uses' => 'DealController@discussionCreate',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/discussions', ['as' => 'deals.discussion.store','uses' => 'DealController@discussionStore',])->middleware(['auth','XSS',]);
	Route::get('/deals/{id}/permission/{cid}', ['as' => 'deals.client.permission','uses' => 'DealController@permission',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/permission/{cid}', ['as' => 'deals.client.permissions.store','uses' => 'DealController@permissionStore',])->middleware(['auth','XSS',]);
	Route::get('/deals/list', ['as' => 'deals.list','uses' => 'DealController@deal_list',])->middleware(['auth','XSS',]);

	// Deal Calls
	Route::get('/deals/{id}/call', ['as' => 'deals.calls.create','uses' => 'DealController@callCreate',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/call', ['as' => 'deals.calls.store','uses' => 'DealController@callStore',])->middleware(['auth']);
	Route::get('/deals/{id}/call/{cid}/edit', ['as' => 'deals.calls.edit','uses' => 'DealController@callEdit',])->middleware(['auth','XSS',]);
	Route::put('/deals/{id}/call/{cid}', ['as' => 'deals.calls.update','uses' => 'DealController@callUpdate',])->middleware(['auth']);
	Route::delete('/deals/{id}/call/{cid}', ['as' => 'deals.calls.destroy','uses' => 'DealController@callDestroy',])->middleware(['auth','XSS',]);

	// Deal Email
	Route::get('/deals/{id}/email', ['as' => 'deals.emails.create','uses' => 'DealController@emailCreate',])->middleware(['auth','XSS',]);
	Route::post('/deals/{id}/email', ['as' => 'deals.emails.store','uses' => 'DealController@emailStore',])->middleware(['auth']);
	Route::resource('deals', 'DealController')->middleware(['auth','XSS',]);
	// end Deal Module

	Route::get('/search',['as' => 'search.json','uses' =>'UserController@search']);
	Route::post('/stages/order', ['as' => 'stages.order','uses' => 'StageController@order',]);
	Route::post('/stages/json', ['as' => 'stages.json','uses' => 'StageController@json',]);

	Route::resource('stages', 'StageController');
	Route::resource('pipelines', 'PipelineController');
	Route::resource('labels', 'LabelController');
	Route::resource('sources', 'SourceController');
	Route::resource('payments', 'PaymentController');
	Route::resource('expense_categories', 'ExpenseCategoryController');
	Route::resource('custom_fields', 'CustomFieldController');
	Route::resource('products', 'ProductController');
	Route::resource('expenses', 'ExpenseController');

	Route::get('/invoices/payments', ['as' => 'invoices.payments','uses' => 'InvoiceController@payments',])->middleware(['auth','XSS',]);
	Route::get('/invoices/{id}/products/{pid}', ['as' => 'invoices.products.edit','uses' => 'InvoiceController@productEdit',])->middleware(['auth','XSS',]);
	Route::put('/invoices/{id}/products/{pid}', ['as' => 'invoices.products.update','uses' => 'InvoiceController@productUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/invoices/{id}/products/{pid}', ['as' => 'invoices.products.delete','uses' => 'InvoiceController@productDelete',])->middleware(['auth','XSS',]);
	Route::get('/invoices/{id}/products', ['as' => 'invoices.products.add','uses' => 'InvoiceController@productAdd',])->middleware(['auth','XSS',]);
	Route::post('/invoices/{id}/products', ['as' => 'invoices.products.store','uses' => 'InvoiceController@productStore',])->middleware(['auth','XSS',]);
	Route::get('/invoices/{id}/payments', ['as' => 'invoices.payments.add','uses' => 'InvoiceController@paymentAdd',])->middleware(['auth','XSS',]);
	Route::post('/invoices/{id}/payments', ['as' => 'invoices.payments.store','uses' => 'InvoiceController@paymentStore',])->middleware(['auth','XSS',]);
	Route::get('invoices/{id}/get_invoice', 'InvoiceController@printInvoice')->name('get.invoice')->middleware(['auth','XSS']);
	Route::get('/invoices/preview/{template}/{color}',['as' => 'invoice.preview','uses' =>'InvoiceController@previewInvoice']);

	// Estimation
	// Route::get('/estimations/{id}/products/{pid}',['as' => 'estimations.products.edit','uses' =>'EstimationController@productEdit'])->middleware(['auth','XSS']);
	// Route::put('/estimations/{id}/products/{pid}',['as' => 'estimations.products.update','uses' =>'EstimationController@productUpdate'])->middleware(['auth','XSS']);
	// Route::delete('/estimations/{id}/products/{pid}',['as' => 'estimations.products.delete','uses' =>'EstimationController@productDelete'])->middleware(['auth','XSS']);
	// Route::get('/estimations/{id}/products',['as' => 'estimations.products.add','uses' =>'EstimationController@productAdd'])->middleware(['auth','XSS']);
	// Route::post('/estimations/{id}/products',['as' => 'estimations.products.store','uses' =>'EstimationController@productStore'])->middleware(['auth','XSS']);
	// Route::get('estimations/{id}/get_estimation', 'EstimationController@printEstimation')->name('get.estimation')->middleware(['auth','XSS']);
	// Route::get('/estimations/preview/{template}/{color}',['as' => 'estimations.preview','uses' =>'EstimationController@previewEstimation']);
	// end Estimation

	// Leads Module
	Route::post('/lead_stages/order', ['as' => 'lead_stages.order','uses' => 'LeadStageController@order',]);
	Route::resource('lead_stages', 'LeadStageController');
	Route::post('/leads/json', ['as' => 'leads.json','uses' => 'LeadController@json',]);
	Route::post('/leads/order', ['as' => 'leads.order','uses' => 'LeadController@order',])->middleware(['auth','XSS',]);
	Route::get('/leads/list', ['as' => 'leads.list','uses' => 'LeadController@lead_list',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/file', ['as' => 'leads.file.upload','uses' => 'LeadController@fileUpload',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/file/{fid}', ['as' => 'leads.file.download','uses' => 'LeadController@fileDownload',])->middleware(['auth','XSS',]);
	Route::delete('/leads/{id}/file/delete/{fid}', ['as' => 'leads.file.delete','uses' => 'LeadController@fileDelete',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/note', ['as' => 'leads.note.store','uses' => 'LeadController@noteStore',])->middleware(['auth']);
	Route::get('/leads/{id}/labels', ['as' => 'leads.labels','uses' => 'LeadController@labels',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/labels', ['as' => 'leads.labels.store','uses' => 'LeadController@labelStore',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/users', ['as' => 'leads.users.edit','uses' => 'LeadController@userEdit',])->middleware(['auth','XSS',]);
	Route::put('/leads/{id}/users', ['as' => 'leads.users.update','uses' => 'LeadController@userUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/leads/{id}/users/{uid}', ['as' => 'leads.users.destroy','uses' => 'LeadController@userDestroy',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/products', ['as' => 'leads.products.edit','uses' => 'LeadController@productEdit',])->middleware(['auth','XSS',]);
	Route::put('/leads/{id}/products', ['as' => 'leads.products.update','uses' => 'LeadController@productUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/leads/{id}/products/{uid}', ['as' => 'leads.products.destroy','uses' => 'LeadController@productDestroy',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/sources', ['as' => 'leads.sources.edit','uses' => 'LeadController@sourceEdit',])->middleware(['auth','XSS',]);
	Route::put('/leads/{id}/sources', ['as' => 'leads.sources.update','uses' => 'LeadController@sourceUpdate',])->middleware(['auth','XSS',]);
	Route::delete('/leads/{id}/sources/{uid}', ['as' => 'leads.sources.destroy','uses' => 'LeadController@sourceDestroy',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/discussions', ['as' => 'leads.discussions.create','uses' => 'LeadController@discussionCreate',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/discussions', ['as' => 'leads.discussion.store','uses' => 'LeadController@discussionStore',])->middleware(['auth','XSS',]);
	Route::get('/leads/{id}/show_convert', ['as' => 'leads.convert.deal','uses' => 'LeadController@showConvertToDeal',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/convert', ['as' => 'leads.convert.to.deal','uses' => 'LeadController@convertToDeal',])->middleware(['auth','XSS',]);

	// Lead Calls
	Route::get('/leads/{id}/call', ['as' => 'leads.calls.create','uses' => 'LeadController@callCreate',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/call', ['as' => 'leads.calls.store','uses' => 'LeadController@callStore',])->middleware(['auth']);
	Route::get('/leads/{id}/call/{cid}/edit', ['as' => 'leads.calls.edit','uses' => 'LeadController@callEdit',])->middleware(['auth','XSS',]);
	Route::put('/leads/{id}/call/{cid}', ['as' => 'leads.calls.update','uses' => 'LeadController@callUpdate',])->middleware(['auth']);
	Route::delete('/leads/{id}/call/{cid}', ['as' => 'leads.calls.destroy','uses' => 'LeadController@callDestroy',])->middleware(['auth','XSS',]);

	Route::get('/{uid}/notification/seen',['as' => 'notification.seen','uses' =>'UserController@notificationSeen']);

	// Lead Email
	Route::get('/leads/{id}/email', ['as' => 'leads.emails.create','uses' => 'LeadController@emailCreate',])->middleware(['auth','XSS',]);
	Route::post('/leads/{id}/email', ['as' => 'leads.emails.store','uses' => 'LeadController@emailStore',])->middleware(['auth']);
	Route::resource('leads', 'LeadController')->middleware(['auth','XSS',]);
	// end Leads Module

	// Email Templates
	Route::get('email_template_lang/{id}/{lang?}', 'EmailTemplateController@manageEmailLang')->name('manage.email.language')->middleware(['auth']);
	Route::put('email_template_store/{pid}', 'EmailTemplateController@storeEmailLang')->name('store.email.language')->middleware(['auth']);
	Route::put('email_template_status/{id}', 'EmailTemplateController@updateStatus')->name('status.email.language')->middleware(['auth']);

	Route::resource('email_template', 'EmailTemplateController')->middleware(['auth','XSS',]);
	// End Email Templates

	Route::resource('invoices', 'InvoiceController');
	Route::resource('taxes', 'TaxController');
	Route::resource('clients', 'ClientController');
	Route::resource('roles', 'RoleController');
	Route::resource('permissions', 'PermissionController');
	Route::resource('contract_type', 'ContractTypeController');
	Route::resource('contract', 'ContractController');
	Route::resource('estimations', 'EstimationController');

	Route::post('/invoices/{id}/payment',['as' => 'client.invoice.payment','uses' =>'InvoiceController@addPayment'])->middleware(['auth', 'XSS']);
	Route::post('/{id}/pay-with-paypal',['as' => 'client.pay.with.paypal','uses' =>'PaypalController@clientPayWithPaypal'])->middleware(['auth','XSS']);
	Route::get('/{id}/get-payment-status',['as' => 'client.get.payment.status','uses' =>'PaypalController@clientGetPaymentStatus'])->middleware(['auth','XSS']);

	// Form Builder
	Route::resource('form_builder', 'FormBuilderController')->middleware(['auth','XSS']);

	// Form link base view
	Route::get('/form/{code}',['as' => 'form.view','uses' =>'FormBuilderController@formView'])->middleware(['XSS']);
	Route::post('/form_view_store',['as' => 'form.view.store','uses' =>'FormBuilderController@formViewStore'])->middleware(['XSS']);

	// Form Response
	Route::get('/form_response/{id}',['as' => 'form.response','uses' =>'FormBuilderController@viewResponse'])->middleware(['auth','XSS']);
	Route::get('/response/{id}',['as' => 'response.detail','uses' =>'FormBuilderController@responseDetail'])->middleware(['auth','XSS']);

	// Form Field
	Route::get('/form_builder/{id}/field',['as' => 'form.field.create','uses' =>'FormBuilderController@fieldCreate'])->middleware(['auth','XSS']);
	Route::post('/form_builder/{id}/field',['as' => 'form.field.store','uses' =>'FormBuilderController@fieldStore'])->middleware(['auth','XSS']);
	Route::get('/form_builder/{id}/field/{fid}/show',['as' => 'form.field.show','uses' =>'FormBuilderController@fieldShow'])->middleware(['auth','XSS']);
	Route::get('/form_builder/{id}/field/{fid}/edit',['as' => 'form.field.edit','uses' =>'FormBuilderController@fieldEdit'])->middleware(['auth','XSS']);
	Route::put('/form_builder/{id}/field/{fid}',['as' => 'form.field.update','uses' =>'FormBuilderController@fieldUpdate'])->middleware(['auth','XSS']);
	Route::delete('/form_builder/{id}/field/{fid}',['as' => 'form.field.destroy','uses' =>'FormBuilderController@fieldDestroy'])->middleware(['auth','XSS']);

	// Form Field Bind
	Route::get('/form_field/{id}',['as' => 'form.field.bind','uses' =>'FormBuilderController@formFieldBind'])->middleware(['auth','XSS']);
	Route::post('/form_field_store/{id}',['as' => 'form.bind.store','uses' =>'FormBuilderController@bindStore'])->middleware(['auth','XSS']);
	// end Form Builder

	// MDF Module
	// Route::delete('/mdf/{id}/products/{pid}',['as' => 'mdf.products.delete','uses' =>'MdfController@productDelete'])->middleware(['auth','XSS']);
	// Route::get('/mdf/{id}/products',['as' => 'mdf.products.add','uses' =>'MdfController@productAdd'])->middleware(['auth','XSS']);
	// Route::post('/mdf/{id}/products',['as' => 'mdf.products.store','uses' =>'MdfController@productStore'])->middleware(['auth','XSS']);
	// Route::get('/mdf/approved/{id}/payments/{type}',['as' => 'mdf.payments.approved','uses' =>'MdfController@paymentApproved'])->middleware(['auth','XSS']);
	// Route::post('/mdf/approved/{id}/payments',['as' => 'mdf.payments.approved.store','uses' =>'MdfController@paymentApprovedStore'])->middleware(['auth','XSS']);
	// Route::get('mdf/{id}/get_mdf', 'MdfController@printMDF')->name('get.mdf')->middleware(['auth','XSS']);
	// Route::get('/mdf/preview/{template}/{color}',['as' => 'mdf.preview','uses' =>'MdfController@previewMDF']);
	// Route::get('/mdf/preview/{template}/{color}',['as' => 'mdf.preview','uses' =>'MdfController@previewMDF']);
	// Route::post('/mdf/change-mdf-complete/{id}', ['as' => 'mdf.change.complete','uses' => 'MdfController@changeComplete',])->middleware(['auth','XSS']);

	// Route::resource('mdf', 'MdfController')->middleware(['auth','XSS']);
	// Route::post('/mdf/event_type',['as' => 'mdf.event.json','uses' =>'MdfController@jsonEvent']);
	// Route::resource('mdf_status', 'MdfStatusController')->middleware(['auth','XSS']);
	// Route::resource('mdf_type', 'MdfTypeController')->middleware(['auth','XSS']);
	// Route::resource('mdf_sub_type', 'MdfSubTypeController')->middleware(['auth','XSS']);
	// end MDF Module


	//================================= Custom Landing Page ====================================//

	Route::get('/landingpage', 'LandingPageSectionsController@index')->name('custom_landing_page.index')->middleware(['auth','XSS']);
	Route::get('/LandingPage/show/{id}', 'LandingPageSectionsController@show');
	Route::post('/LandingPage/setConetent', 'LandingPageSectionsController@setConetent')->middleware(['auth','XSS']);
	Route::get('/get_landing_page_section/{name}', function($name) {
	    $plans = [];
	    return view('custom_landing_page.'.$name,compact('plans'));
	});
	Route::post('/LandingPage/removeSection/{id}', 'LandingPageSectionsController@removeSection')->middleware(['auth','XSS']);
	Route::post('/LandingPage/setOrder', 'LandingPageSectionsController@setOrder')->middleware(['auth','XSS']);
	Route::post('/LandingPage/copySection', 'LandingPageSectionsController@copySection')->middleware(['auth','XSS']);


	//========================================================================================//

	/* fake Router */
	Route::post('/message_data', 'SettingsController@savePaymentSettings')->name('message.data')->middleware(['auth','XSS']);

	Route::post('/message_seen', 'SettingsController@savePaymentSettings')->name('message.seen')->middleware(['auth','XSS']);
	//================================= Invoice Payment Gateways  ====================================//

	//================================= Invoice Payment Gateways  ====================================//


	Route::post('/invoice-pay-with-paystack',['as' => 'invoice.pay.with.paystack','uses' =>'PaystackPaymentController@invoicePayWithPaystack'])->middleware(['auth','XSS']);
	Route::get('/invoice/paystack/{pay_id}/{invoice_id}', ['as' => 'invoice.paystack','uses' => 'PaystackPaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-flaterwave',['as' => 'invoice.pay.with.flaterwave','uses' =>'FlutterwavePaymentController@invoicePayWithFlutterwave'])->middleware(['auth','XSS']);
	Route::get('/invoice/flaterwave/{txref}/{invoice_id}', ['as' => 'invoice.flaterwave','uses' => 'FlutterwavePaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-razorpay',['as' => 'invoice.pay.with.razorpay','uses' =>'RazorpayPaymentController@invoicePayWithRazorpay'])->middleware(['auth','XSS']);
	Route::get('/invoice/razorpay/{txref}/{invoice_id}', ['as' => 'invoice.razorpay','uses' => 'RazorpayPaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-paytm',['as' => 'invoice.pay.with.paytm','uses' =>'PaytmPaymentController@invoicePayWithPaytm'])->middleware(['auth','XSS']);
	Route::post('/invoice/paytm/{invoice}', ['as' => 'invoice.paytm','uses' => 'PaytmPaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-mercado',['as' => 'invoice.pay.with.mercado','uses' =>'MercadoPaymentController@invoicePayWithMercado'])->middleware(['auth','XSS']);
	Route::post('/invoice/mercado', ['as' => 'invoice.mercado','uses' => 'MercadoPaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-mollie',['as' => 'invoice.pay.with.mollie','uses' =>'MolliePaymentController@invoicePayWithMollie'])->middleware(['auth','XSS']);
	Route::get('/invoice/mollie/{invoice}', ['as' => 'invoice.mollie','uses' => 'MolliePaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-skrill',['as' => 'invoice.pay.with.skrill','uses' =>'SkrillPaymentController@invoicePayWithSkrill'])->middleware(['auth','XSS']);
	Route::get('/invoice/skrill/{invoice}', ['as' => 'invoice.skrill','uses' => 'SkrillPaymentController@getInvociePaymentStatus']);

	Route::post('/invoice-pay-with-coingate',['as' => 'invoice.pay.with.coingate','uses' =>'CoingatePaymentController@invoicePayWithCoingate'])->middleware(['auth','XSS']);
	Route::get('/invoice/coingate/{invoice}', ['as' => 'invoice.coingate','uses' => 'CoingatePaymentController@getInvociePaymentStatus']);

	Route::get('/stripe-payment-status',['as' => 'stripe.payment.status','uses' =>'StripePaymentController@GetStripePaymentStatus']);

	/* fake Router */
	Route::post('/message_data', 'SettingsController@savePaymentSettings')->name('message.data')->middleware(['auth','XSS']);

	Route::post('/message_seen', 'SettingsController@savePaymentSettings')->name('message.seen')->middleware(['auth','XSS']);
	//================================= Invoice Payment Gateways  ====================================//

	/* fake Router */
	Route::post('/message_data', 'SettingsController@savePaymentSettings')->name('message.data')->middleware(['auth','XSS']);

	Route::post('/message_seen', 'SettingsController@savePaymentSettings')->name('message.seen')->middleware(['auth','XSS']);
	//================================= Invoice Payment Gateways  ====================================//

	/* fake Router */
	Route::post('/message_data', 'SettingsController@savePaymentSettings')->name('message.data')->middleware(['auth','XSS']);

	Route::post('/message_seen', 'SettingsController@savePaymentSettings')->name('message.seen')->middleware(['auth','XSS']);
	//================================= Invoice Payment Gateways  ====================================//
// });


	
	Route::get('/department', ['as' => 'department','uses' => 'DepartmentController@department',])->middleware(['auth','XSS',]);
	
	Route::get('/department/create', ['as' => 'department.create','uses' => 'DepartmentController@create',])->middleware(['auth','XSS',]);

	Route::post('/department/store', ['as' => 'department.store','uses' => 'DepartmentController@store',])->middleware(['auth','XSS',]);

	Route::get('/department/edit/{id}', ['as' => 'department.edit','uses' => 'DepartmentController@edit',])->middleware(['auth','XSS',]);

	Route::put('/department/{id}', ['as' => 'department.update','uses' => 'DepartmentController@update',])->middleware(['auth','XSS',]);
	
	Route::delete('/department/{id}', ['as' => 'department.destroy','uses' => 'DepartmentController@destroy',])->middleware(['auth','XSS',]);
