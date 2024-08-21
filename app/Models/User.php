<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Notification;
use App\Models\UsersOperatorsServices;


class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id',
        'fname',
        'mid_name',
        'name',
        'designation',
        'cadre',
        'gender',
        'grade_level',
        'email',
        'password',
        'date_of_current_posting',
        'date_of_MDA_posting',
        'date_of_last_promotion',
        'org_code',
        'org_name',
        'type',
        'avatar',
        'lang',
        'created_by',
        'job_title',
        'plan',
        'plan_expire_date',
        'is_active',
        'user_name',
        'ip',
        'device',
        'last_login',
        'change_password_time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $settings;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public $customField;

    public function ownerId()
    {
        if($this->type == 'Super Admin' || $this->type == 'Owner')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public function estimateNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["estimation_prefix"] . sprintf("%05d", $number);
    }

    public function mdfNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["mdf_prefix"] . sprintf("%05d", $number);
    }

    // public function deals()
    // {
    //     return $this->belongsToMany('App\Models\Deal', 'user_deals', 'user_id', 'deal_id');
    // }


    public function Organization()
    {
        return $this->belongsTo('App\Organization','org_code');
    }

    // public function leads()
    // {
    //     return $this->belongsToMany('App\Models\Lead', 'user_leads', 'user_id', 'lead_id');
    // }

    // public function clientDeals()
    // {
    //     return $this->belongsToMany('App\Models\Deal', 'client_deals', 'client_id', 'deal_id');
    // }

    public function clientEstimations()
    {
        return $this->hasMany('App\Models\Estimation', 'client_id', 'id');
    }

    public function clientContracts()
    {
        return $this->hasMany('App\Models\Contract', 'client_name', 'id');
    }

    // public function getInvoiceCount($id)
    // {
    //     $invoices = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->count();

    //     return $invoices;
    // }

    // public function clientPermission($dealId)
    // {
    //     return ClientPermission::where('client_id', '=', $this->id)->where('deal_id', '=', $dealId)->first();
    // }

    public function user_activity(){
        return $this->hasOne(UserActivity::class,'user_id', 'id');
    }

    public function getUserCount()
    {
        return User::where('created_by', '=', \Auth::user()->id)->count();
    }

    public function getRoleCount()
    {
        return Role::where('created_by', '=', \Auth::user()->id)->count();
    }

    public function getOperatorCount($id)
    {
        return UsersOperatorsServices::where('user_id', '=', $id)->distinct('id_operator')->count();
    }

    public function getServiceCount($id)
    {
        return UsersOperatorsServices::where('user_id', '=', $id)->count();
    }

    public function userDefaultData()
    {

    }

    // For Email template Module
    public function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'New User',

        ];

        foreach($emailTemplate as $eTemp)
        {
            EmailTemplate::create(
                [
                    'name' => $eTemp,
                    'from' => env('APP_NAME'),
                    'created_by' => $this->id,
                ]
            );
        }

        $defaultTemplate = [
            'New User' => [
                'subject' => 'Login Detail',
                'lang' => [
                    'ar' => 'dgdf',

                ],
            ],



        ];




    }

    // End Email template Module

    public function makeEmployeeRole()
    {
        $userRole        = Role::create(
            [
                'name' => 'linkITStaff',
                'created_by' => $this->id,
            ]
        );
        $userPermissions = [
            'Dashboard',

        ];
        foreach($userPermissions as $ap)
        {
            $permission = Permission::findByName($ap);
            $userRole->givePermissionTo($permission);
        }
    }

    // public function notifications()
    // {
    //     return Notification::where('user_id', '=', \Auth::user()->id)->where('is_read', '=', 0)->orderBy('id', 'desc')->get();
    // }


    public function time()
    {
        $today = Carbon::now()->format('Y-m-d H:i:s');
        return $today;
    }

    public function unread()
    {
        return Message::where('from', '=', $this->id)->where('is_read', '=', 0)->count();
    }

    public function mdfs()
    {
        return $this->hasMany('App\Models\Mdf', 'user_id', 'id');
    }

    // public function roles()
    // {
    //     return $this->belongsTo(User::class, 'roles');
    // }

    public static function scopeUid($query,$uid)
    {
        return $query->where("id",$uid);
    }

    public static function scopeType($query,$type)
    {
        return $query->where("type",$type);
    }

    public static function scopeTypes($query,$types)
    {
        return $query->whereNotIn("type",$types);
    }

    public static function scopeActive($query)
    {
        return $query->where("is_active",1);
    }

    public static function WhowAccessAlOperator($type)
    {
        $userTypies = array("Owner","Super Admin","Business Manager","Admin","BOD","Business Owner");

        if(in_array($type, $userTypies))
        {
            return true;
        }

        return false;
    }
}
