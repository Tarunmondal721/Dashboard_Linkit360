<?php

namespace App\Http\Controllers;

use App\Mail\EmailTest;
use App\Models\Settings;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\common\Utility as UtilityMiddleware;
use App\Models\Configuration;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Stmt\TryCatch;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();

        if ($user->can('System Settings')) {
            $settings = Utility::settings();
            $globalConfig = \config('globalconfig');
            $url = $globalConfig['middleware'];
            // $payment = Utility::payment_settings();
            // $utility = new Utility;
            $resultIp = UtilityMiddleware::GetResponseFromUrlMiddleware($url . "client");
            $listIp = $resultIp['data'] ?? [];

            return view('users.system_settings', compact('settings', 'listIp'));
        } else {
            return redirect()->back()->with('error', __('Invalid User.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('System Settings')) {
            $post = $request->all();
            unset($post['_token']);

            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->ownerId(),
                    ]
                );
            }



            return redirect()->back()->with('success', __('Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Invalid User.'));
        }
    }

    public function site_setting(Request $request)
    {
        if ($request->favicon) {
            $request->validate(['favicon' => 'required|image|mimes:png|max:1024']);
            $faviconName = 'favicon.png';
            $request->favicon->storeAs('logo', $faviconName);
        }
        if ($request->full_logo) {
            $request->validate(['full_logo' => 'required|image|mimes:png|max:1024']);
            $logoName = 'logo-full.png';
            $request->full_logo->storeAs('logo', $logoName);
            $authlogoName = 'auth-logo.png';
            $request->full_logo->storeAs('logo', $authlogoName);
        }
        if ($request->landing_logo) {
            $request->validate(['landing_logo' => 'required|image|mimes:png|max:1024']);
            $landing_logo = 'landing_logo.png';
            $request->landing_logo->storeAs('logo', $landing_logo);
        }

        $rules = [
            'mail_driver' => 'required|string|max:255',
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|string|max:255',
            'mail_username' => 'required|string|max:255',
            'mail_password' => 'required|string|max:255',
            'mail_from_address' => 'required|string|max:255',
            'mail_from_name' => 'required|string|max:255',
            'mail_encryption' => 'required|string|max:255',

        ];



        if (!empty($request->enable_landing) || !empty($request->gdpr_cookie)) {

            $post = $request->all();
            if (!isset($request->enable_landing)) {
                $post['enable_landing'] = 'no';
            }
            if (!isset($request->gdpr_cookie)) {
                $post['gdpr_cookie'] = 'off';
            }
            unset($post['_token'], $post['logo'], $post['small_logo'], $post['favicon'], $post['SITE_RTL']);
            foreach ($post as $key => $data) {
                \DB::insert(
                    'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $data,
                        $key,
                        \Auth::user()->ownerId(),
                    ]
                );
            }
        }
        // End Landing Page Setting

        if (isset($request->enable_chat) && $request->enable_chat == 'yes') {
            $rules['pusher_app_id']      = 'required';
            $rules['pusher_app_key']     = 'required';
            $rules['pusher_app_secret']  = 'required';
            $rules['pusher_app_cluster'] = 'required';
        }

        $request->validate($rules);

        $arrEnv = [
            'MAIL_MAILER' => $request->mail_driver,
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_PASSWORD' => $request->mail_password,
            'MAIL_ENCRYPTION' => $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => $request->mail_from_name,
            'CHAT_MODULE' => $request->enable_chat,
            'PUSHER_APP_ID' => $request->pusher_app_id,
            'PUSHER_APP_KEY' => $request->pusher_app_key,
            'PUSHER_APP_SECRET' => $request->pusher_app_secret,
            'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
            'SITE_RTL' => !isset($request->SITE_RTL) ? 'off' : 'on',
            'THEME_COLOR' => $request->color,
        ];

        $env = Utility::setEnvironmentValue($arrEnv);
        if ($env) {
            return redirect()->back()->with('success', __('Site Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function testEmail()
    {
        return view('users.test_email');
    }
    public function addIp()
    {

        return view('users.add_ip_settings');
    }
    public function storeIp(Request $request)
    {

        $rules = [
            'ip' => 'required',
            'name' => 'required'
        ];
        $request->validate($rules);

        $postData = [
            'ip_address' => $request->ip,
            'name' => $request->name
        ];
        $globalConfig = \config('globalconfig');
        $url = $globalConfig['middleware'];
        $resultIp = UtilityMiddleware::storeIpClient($url . "client", $postData);
        // dd($resultIp);
        if (isset($resultIp['error']) || isset($resultIp['errors'])) {
            return redirect()->back()->with('error', __($resultIp['message']));
        } else {
            return redirect()->back()->with('success', __($resultIp['message']));
        }
    }
    public function updateIp($id)
    {
        $globalConfig = \config('globalconfig');
        $url = $globalConfig['middleware'];
        // $payment = Utility::payment_settings();
        // $utility = new Utility;
        $resultIp = UtilityMiddleware::GetResponseFromUrlMiddleware($url . "client/" . $id);

        return view('users.update_ip_settings', [
            'ip' => $resultIp['data'] ?? []
        ]);
    }
    public function deleteIp(Request $request)
    {
        $globalConfig = \config('globalconfig');
        $url = $globalConfig['middleware'];
        // dd($url . $request->id);
        $resultIp = UtilityMiddleware::deleteIpClient($url . "client/" . $request->id);
        if (isset($resultIp['error']) || isset($resultIp['errors'])) {
            return redirect()->back()->with('error', __($resultIp['message']));
        } else {
            return redirect()->back()->with('success', __($resultIp['message']));
        }
    }
    public function saveUpdateIp(Request $request)
    {

        $rules = [
            'ip' => 'required',
            'name' => 'required',
            'status' => 'required'
        ];
        $request->validate($rules);

        $postData = [
            'ip_address' => $request->ip,
            'name' => $request->name,
            'status' => $request->status == 'active' ? true : false
        ];
        // dd($postData);
        $globalConfig = \config('globalconfig');
        $url = $globalConfig['middleware'];
        $resultIp = UtilityMiddleware::updateIpClient($url . "client/" . $request->id, $postData);
        if (isset($resultIp['error']) || isset($resultIp['errors'])) {
            return redirect()->back()->with('error', __($resultIp['message']));
        } else {
            return redirect()->back()->with('success', __($resultIp['message']));
        }
        return redirect()->back()->with('success', __("success"));
    }
    public function testEmailSend(Request $request)
    {
        // $rules = [
        //     'mail_driver' => 'required|string|max:255',
        //     'mail_host' => 'required|string|max:255',
        //     'mail_port' => 'required|string|max:255',
        //     'mail_username' => 'required|string|max:255',
        //     'mail_password' => 'required|string|max:255',
        //     'mail_from_address' => 'required|string|max:255',
        //     'mail_from_name' => 'required|string|max:255',
        //     'mail_encryption' => 'required|string|max:255',

        // ];
        // $request->validate($rules);

        // $arrEnv = [
        //     'MAIL_MAILER' => $request->mail_driver,
        //     'MAIL_HOST' => $request->mail_host,
        //     'MAIL_PORT' => $request->mail_port,
        //     'MAIL_USERNAME' => $request->mail_username,
        //     'MAIL_PASSWORD' => $request->mail_password,
        //     'MAIL_ENCRYPTION' => $request->mail_encryption,
        //     'MAIL_FROM_ADDRESS' => $request->mail_from_address,
        //     'MAIL_FROM_NAME' => $request->mail_from_name
        // ];

        // // $env = Utility::setEnvironmentValue($arrEnv);



        $validator = \Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try {
            Mail::to($request->email)->send(new EmailTest());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', __('Email send Successfully'));
    }

    public function savePaymentSettings(Request $request)
    {
        $validatorArray = [];

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $post = $request->all();
        unset($post['_token']);

        $stripe_status = $request->site_enable_stripe ?? 'off';
        $paypal_status = $request->site_enable_paypal ?? 'off';

        if ($stripe_status == 'on') {
            $validatorArray['site_stripe_key']    = 'required|string|max:255';
            $validatorArray['site_stripe_secret'] = 'required|string|max:255';
        }
        if ($paypal_status == 'on') {
            $validatorArray['site_paypal_client_id']  = 'required|string|max:255';
            $validatorArray['site_paypal_secret_key'] = 'required|string|max:255';
        }

        $validator = \Validator::make(
            $request->all(),
            $validatorArray
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $post['site_enable_stripe'] = $stripe_status;
        $post['site_enable_paypal'] = $paypal_status;

        foreach ($post as $key => $data) {
            \DB::insert(
                'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->ownerId(),
                    $created_at,
                    $updated_at,
                ]
            );
        }

        return redirect()->back()->with('success', __('Settings updated successfully.'));
    }

    public function saveTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

        if (isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color']))) {
            $post['invoice_color'] = "ffffff";
        }

        if (isset($post['estimation_template']) && (!isset($post['estimation_color']) || empty($post['estimation_color']))) {
            $post['estimation_color'] = "ffffff";
        }

        if (isset($post['mdf_template']) && (!isset($post['mdf_color']) || empty($post['mdf_color']))) {
            $post['mdf_color'] = "ffffff";
        }

        if ($request->invoice_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'invoice_logo' => 'image|mimes:png|max:2048',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo         = $user->id . '_invoice_logo.png';
            $path                 = $request->file('invoice_logo')->storeAs('invoice_logo', $invoice_logo);
            $post['invoice_logo'] = $invoice_logo;
        }

        if ($request->estimation_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'estimation_logo' => 'image|mimes:png|max:2048',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $estimation_logo         = $user->id . '_estimation_logo.png';
            $path                    = $request->file('estimation_logo')->storeAs('estimation_logo', $estimation_logo);
            $post['estimation_logo'] = $estimation_logo;
        }

        if ($request->mdf_logo) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'mdf_logo' => 'image|mimes:png|max:2048',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $mdf_logo         = $user->id . '_mdf_logo.png';
            $path             = $request->file('mdf_logo')->storeAs('mdf_logo', $mdf_logo);
            $post['mdf_logo'] = $mdf_logo;
        }

        foreach ($post as $key => $data) {
            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $data,
                    $key,
                    \Auth::user()->ownerId(),
                ]
            );
        }

        if (isset($post['invoice_template'])) {
            return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
        }

        if (isset($post['estimation_template'])) {
            return redirect()->back()->with('success', __('Estimation Setting updated successfully'));
        }

        if (isset($post['mdf_template'])) {
            return redirect()->back()->with('success', __('MDF Setting updated successfully'));
        }
    }

    public function adminPaymentSettings(Request $request)
    {
        $user = \Auth::user();

        $validator = \Validator::make(
            $request->all(),
            [
                'currency' => 'required|string|max:255',
                'currency_symbol' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        } else {

            if ($user->type == 'Super Admin') {
                $arrEnv['CURRENCY_SYMBOL'] = $request->currency_symbol;
                $arrEnv['CURRENCY'] = $request->currency;

                $env = Utility::setEnvironmentValue($arrEnv);
            }

            $post['currency_symbol'] = $request->currency_symbol;
            $post['currency'] = $request->currency;
        }

        if (isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on') {
            $validator = \Validator::make(
                $request->all(),
                [
                    'stripe_key' => 'required|string',
                    'stripe_secret' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_stripe_enabled']     = $request->is_stripe_enabled;
            $post['stripe_secret']         = $request->stripe_secret;
            $post['stripe_key']            = $request->stripe_key;
            $post['stripe_webhook_secret'] = $request->stripe_webhook_secret;
        } else {
            $post['is_stripe_enabled'] = 'off';
        }


        if (isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on') {
            $validator = \Validator::make(
                $request->all(),
                [
                    'paypal_mode' => 'required|string',
                    'paypal_client_id' => 'required|string',
                    'paypal_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paypal_enabled'] = $request->is_paypal_enabled;
            $post['paypal_mode']       = $request->paypal_mode;
            $post['paypal_client_id']  = $request->paypal_client_id;
            $post['paypal_secret_key'] = $request->paypal_secret_key;
        } else {
            $post['is_paypal_enabled'] = 'off';
        }

        if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'paystack_public_key' => 'required|string',
                    'paystack_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paystack_enabled'] = $request->is_paystack_enabled;
            $post['paystack_public_key'] = $request->paystack_public_key;
            $post['paystack_secret_key'] = $request->paystack_secret_key;
        } else {
            $post['is_paystack_enabled'] = 'off';
        }

        if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'flutterwave_public_key' => 'required|string',
                    'flutterwave_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
            $post['flutterwave_public_key'] = $request->flutterwave_public_key;
            $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
        } else {
            $post['is_flutterwave_enabled'] = 'off';
        }

        if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'razorpay_public_key' => 'required|string',
                    'razorpay_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
            $post['razorpay_public_key'] = $request->razorpay_public_key;
            $post['razorpay_secret_key'] = $request->razorpay_secret_key;
        } else {
            $post['is_razorpay_enabled'] = 'off';
        }

        if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'mercado_app_id' => 'required|string',
                    'mercado_secret_key' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_mercado_enabled'] = $request->is_mercado_enabled;
            $post['mercado_app_id']     = $request->mercado_app_id;
            $post['mercado_secret_key'] = $request->mercado_secret_key;
        } else {
            $post['is_mercado_enabled'] = 'off';
        }

        if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {

            $validator = \Validator::make(
                $request->all(),
                [
                    'paytm_mode' => 'required',
                    'paytm_merchant_id' => 'required|string',
                    'paytm_merchant_key' => 'required|string',
                    'paytm_industry_type' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paytm_enabled']    = $request->is_paytm_enabled;
            $post['paytm_mode']          = $request->paytm_mode;
            $post['paytm_merchant_id']   = $request->paytm_merchant_id;
            $post['paytm_merchant_key']  = $request->paytm_merchant_key;
            $post['paytm_industry_type'] = $request->paytm_industry_type;
        } else {
            $post['is_paytm_enabled'] = 'off';
        }

        if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {


            $validator = \Validator::make(
                $request->all(),
                [
                    'mollie_api_key' => 'required|string',
                    'mollie_profile_id' => 'required|string',
                    'mollie_partner_id' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_mollie_enabled'] = $request->is_mollie_enabled;
            $post['mollie_api_key']    = $request->mollie_api_key;
            $post['mollie_profile_id'] = $request->mollie_profile_id;
            $post['mollie_partner_id'] = $request->mollie_partner_id;
        } else {
            $post['is_mollie_enabled'] = 'off';
        }

        if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {



            $validator = \Validator::make(
                $request->all(),
                [
                    'skrill_email' => 'required|email',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_skrill_enabled'] = $request->is_skrill_enabled;
            $post['skrill_email']      = $request->skrill_email;
        } else {
            $post['is_skrill_enabled'] = 'off';
        }

        if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {


            $validator = \Validator::make(
                $request->all(),
                [
                    'coingate_mode' => 'required|string',
                    'coingate_auth_token' => 'required|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_coingate_enabled'] = $request->is_coingate_enabled;
            $post['coingate_mode']       = $request->coingate_mode;
            $post['coingate_auth_token'] = $request->coingate_auth_token;
        } else {
            $post['is_coingate_enabled'] = 'off';
        }

        foreach ($post as $key => $data) {
            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            $insert_payment_setting = \DB::insert(
                'insert into admin_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                $arr
            );
        }

        return redirect()->back()->with('success', __('Settings updated successfully.'));
    }
    public function middlewareUpdate(Request $request)
    {
        // Configuration
        $request->validate([
            'middleware_url_api' => 'required',
            'timeout_settings' => 'required',
        ]);
        if (substr($request->middleware_url_api, -1) != '/') {
            //Add your condition here
            $middlewareApi = $request->middleware_url_api . "/";
            // dd($middlewareApi);
        } else {
            $middlewareApi = $request->middleware_url_api;
        }

        //set config middlewareapi
        $configMiddlewareUrlApi = Configuration::where('key', 'middleware_url_api')->first();
        $configMiddlewareUrlApi->value = $middlewareApi;
        $configMiddlewareUrlApi->update();

        //set config timeoutsettings
        $configTimeoutApi = Configuration::where('key', 'timeout_settings')->first();
        $configTimeoutApi->value = $request->timeout_settings;
        $configTimeoutApi->update();
        return redirect()->back()->with('success', __('Middleware settings updated successfully.'));
    }
}
