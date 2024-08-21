<?php

namespace App\Http\Controllers;

use App\Mail\NotitificationAlertMail;
use App\Models\Country;
use App\Models\Notification;
use App\Models\NotificationDeployment;
use App\Models\NotificationIncident;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {

        $notificationDeployment = DB::table('notification_deployment')
            ->select('notification_deployment.*', 'countries.country as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'notification_deployment.country_id')
            ->get();
        $notificationIncident = DB::table('notification_incident')
            ->select('notification_incident.*', 'countries.country as country_name')
            ->leftJoin('countries', 'countries.id', '=', 'notification_incident.country_id')
            ->get();

        return view('notification.notification-list', compact('notificationDeployment', 'notificationIncident'));
    }
    public function create()
    {
        $countrys = Operator::select('country_name', 'country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
        // dd($countrys);
        return view('notification.notification-create', compact('countrys'));
    }

    public function details($countryId)
    {
        $time = date('Y-m-d H:i:s');
        $notificationsDeployment = NotificationDeployment::where('country_id', $countryId)->orderByDesc('maintenance_end')->limit(5)->get();

        $notificationsIncident = NotificationIncident::where('country_id', $countryId)->orderByDesc('time_incident')->limit(5)->get();
        return view('admin.details-notification', [
            'notificationsDeployment' => $notificationsDeployment,
            'notificationsIncident' => $notificationsIncident,
            'time' => $time
        ]);
    }
    public function addNotification(Request $request)
    {
        $data = [
            'country_id' => $request->country,
            // 'operator_id' => $request->operator,
            'category' => $request->category,
            'subject' => $request->subjectDeployment,
            'message' => $request->message,
            'activity_name' => $request->activity_name,
            'maintenance_detail' => $request->maintenance_detail,
            'objective' => $request->objective,
            'service_impact' => $request->service_impact,
            'downtime' => $request->downtime,
            'email' => $request->email
        ];
        $explodeMaintenanceSchedule = explode(' | ', $request->maintenance_schedule);

        if (isset($explodeMaintenanceSchedule[0])) {
            $data['maintenance_start'] = $explodeMaintenanceSchedule[0];
        } else {
            $data['maintenance_start'] = null;
        }
        if (isset($explodeMaintenanceSchedule[1])) {
            $data['maintenance_end'] = $explodeMaintenanceSchedule[1];
        } else {
            $data['maintenance_end'] = null;
        }



        $notification = NotificationDeployment::create($data);
        Mail::to([$request->email])->send(new NotitificationAlertMail($notification, $request->subjectDeployment, 'deployment'));

        return redirect()->route('notification.report.index')->with(
            'success',
            __('Notification Deployment Add Successfully!')
        );
    }
    public function addNotificationIncident(Request $request)
    {
        $data = [
            'country_id' => $request->country,
            'status' => "Not Yet Solve",
            'category' => $request->category,
            'subject' => $request->subject,
            'number_ticket' => $request->number_ticket,
            'created_by' => "admin",
            'classification' => $request->classification,
            'severty' => $request->severty,
            'details' => $request->details,
            'time_incident' => $request->time_incident,
            'email' => $request->email

        ];

        NotificationIncident::create($data);

        Mail::to([$request->email])->send(new NotitificationAlertMail($data, $request->subject, 'incident'));

        return redirect()->route('notification.report.index')->with(
            'success',
            __('Notification Incident Add Successfully!')
        );
    }
    public function detailDeployment($id)
    {
        $countrys = Operator::select('country_name', 'country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
        $notification = NotificationDeployment::find($id);
        $country = Country::find($notification->country_id);
        $operator = Operator::find($notification->operator_id);
        return view('notification.notification-deployment-update', compact('countrys', 'notification', 'country', 'operator'));
    }
    public function detailIncident($id)
    { {
            $countrys = Operator::select('country_name', 'country_id')->Status(1)->orderBy('country_name', 'ASC')->distinct()->get();
            $notification = NotificationIncident::find($id);
            $country = Country::find($notification->country_id);
            $operator = Operator::find($notification->operator_id);
            return view('notification.notification-incident-update', compact('countrys', 'notification', 'country', 'operator'));
        }
    }
    public function updateNotificationDeploy(Request $request)
    {
        // dd($request->category);
        $data = [
            'country_id' => $request->country,
            // 'operator_id' => $request->operator,
            // 'category' => $request->category,
            'subject' => $request->subjectDeployment,
            'message' => $request->message,
            'activity_name' => $request->activity_name,
            'maintenance_detail' => $request->maintenance_detail,
            'objective' => $request->objective,
            'service_impact' => $request->service_impact,
            'email' => $request->email,
            'downtime' => $request->downtime
        ];
        $explodeMaintenanceSchedule = explode(' | ', $request->maintenance_schedule);

        if (isset($explodeMaintenanceSchedule[0])) {
            $data['maintenance_start'] = $explodeMaintenanceSchedule[0];
        } else {
            $data['maintenance_start'] = null;
        }
        if (isset($explodeMaintenanceSchedule[1])) {
            $data['maintenance_end'] = $explodeMaintenanceSchedule[1];
        } else {
            $data['maintenance_end'] = null;
        }


        $notification = NotificationDeployment::find($request->id);
        $notification->update($data);
        return redirect()->route('notification.report.index')->with(
            'success',
            __('Notification Deployment Updated Successfully!')
        );
    }
    public function updateNotificationIncident(Request $request)
    {
        $data = [
            'country_id' => $request->country,
            'status' => $request->status,
            // 'category' => $request->category,
            'subject' => $request->subject,
            'number_ticket' => $request->number_ticket,
            'created_by' => $request->created_by,
            'classification' => $request->classification,
            'severty' => $request->severty,
            'details' => $request->details,
            'time_incident' => $request->time_incident,

        ];
        $notification = NotificationIncident::find($request->id);
        $notification->update($data);
        return redirect()->route('notification.report.index')->with(
            'success',
            __('Notification Incident Updated Successfully!')
        );
    }
    public function deleteIncident(Request $request)
    {
        NotificationIncident::find($request->id)->delete();
        return back()->with(
            'success',
            __('Notification Incident Deleted Successfully!')
        );
    }
    public function deleteDeployment(Request $request)
    {
        NotificationDeployment::find($request->id)->delete();
        return back()->with(
            'success',
            __('Notification Deployment Deleted Successfully!')
        );
    }
}
