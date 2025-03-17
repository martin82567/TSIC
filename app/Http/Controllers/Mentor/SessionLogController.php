<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SessionLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void  Scheduled Session ...
     */

    public function __construct()
    {
        $this->middleware('auth:mentor');

    }

    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function list(Request $request)
    {
        $data = DB::table('session')
            ->select('session.*', 'mentee.firstname', 'mentee.middlename', 'mentee.lastname', 'session_method_location.method_id', 'session_method_location.method_value')
            ->join('mentee', 'mentee.id', 'session.mentee_id')
            ->leftJoin('session_method_location', 'session_method_location.id', 'session.session_method_location_id')
            ->where('session.mentor_id', Auth::user()->id)
            ->where('session.status', 1)
            ->orderBy('session.schedule_date', 'desc')
            ->paginate(10);

        if (!empty($data)) {
            foreach ($data as $s) {
                // unset($s->date);
                // $s->schedule_date = date('m-d-Y', strtotime($s->schedule_date));
                $s->method_id = !empty($s->method_id) ? $s->method_id : 0;
                $s->method_value = !empty($s->method_value) ? $s->method_value : '';
            }

        }

        return view('mentor.list-session-log')->with('data', $data);
    }

    public function add(Request $request)
    {
        $mentee_list = DB::table('mentee')
            ->join('assign_mentee', 'assign_mentee.mentee_id', 'mentee.id')
            ->join('school', 'school.id', 'mentee.school_id')
            ->leftJoin('student_status', 'student_status.id', 'mentee.status')
            ->select('mentee.id', 'mentee.timezone', 'mentee.firebase_id', 'mentee.firstname', 'mentee.middlename', 'mentee.lastname', 'mentee.email', 'mentee.current_living_details', 'mentee.image', 'mentee.cell_phone_number', 'mentee.school_id', 'school.name as school_name')
            ->where('assign_mentee.assigned_by', Auth::user()->id)
            ->where('student_status.view_in_application', 1)
            // ->where('assign_mentee.is_primary',1)
            ->orderBy('assign_mentee.created_date', 'desc')
            ->get()->toarray();

        $session_method_location = DB::table('session_method_location')->select('*')->where('status', 1)->get()->toarray();

        return view('mentor.add-session-log')->with('mentee_list', $mentee_list)->with('session_method_location', $session_method_location);

    }


    public function save(Request $request)
    {
        $creationmethod = Auth::user()->creationmethod;
        $externalId = Auth::user()->externalId;

        $name = !empty($request->name) ? $request->name : '';
        $schedule_date = !empty($request->schedule_date) ? $request->schedule_date : '';
        $time_duration = !empty($request->time_duration) ? $request->time_duration : '';
        $type = !empty($request->type) ? $request->type : '';
        $session_method_location_id = !empty($request->session_method_location_id) ? $request->session_method_location_id : '';
        $mentee_id = !empty($request->mentee_id) ? $request->mentee_id : '';
        $mentor_id = Auth::user()->id;
        $no_show = !empty($request->no_show) ? $request->no_show : 0;

        // echo '<pre>'; echo $schedule_date;
        if ($no_show) {
            $time_duration = 0;
        }


        if (!empty($schedule_date)) {
            $schedule_date = str_replace("-", "/", $schedule_date);
            $schedule_date = date("Y-m-d", strtotime($schedule_date));
        }

        // echo '<pre>'; echo $schedule_date;

        // die;

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : 'America/New_York';

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        } else {
            $created_date = date('Y-m-d H:i:s');
        }

        $check = DB::table('session')->where('name', $name)->where('schedule_date', $schedule_date)->where('time_duration', $time_duration)->where('type', $type)->where('session_method_location_id', $session_method_location_id)
            ->where('mentee_id', $mentee_id)->where('mentor_id', $mentor_id)->where('no_show', $no_show)->where('is_created_by_app', 0)->first();

        if (empty($check)) {

            $id = DB::table('session')->insertGetId(['name' => $name, 'schedule_date' => $schedule_date, 'time_duration' => $time_duration, 'type' => $type, 'session_method_location_id' => $session_method_location_id, 'mentee_id' => $mentee_id, 'mentor_id' => $mentor_id, 'created_date' => $created_date, 'no_show' => $no_show]);


            $mentee_data = get_single_data_id('mentee', $mentee_id);
            $mentee_externalId = $mentee_data->externalId;

            $session_method_location = DB::table('session_method_location')->where('id', $session_method_location_id)->first();
            $method_id = $session_method_location->method_id;


            /*===========Star DB Insertion============*/
            if ($creationmethod == 'salesforceapi' && !empty($externalId) && !empty($mentee_externalId)) {

                //Get Service ID for Salesforce Sessions
                if ($affiliate_data->externalId) {
                    $fiscalYear = getFiscalYear($schedule_date);
                    $serviceRecord = DB::table('agency_programs')->select('service_id')->where('agency_id', $affiliate_data->id)->where('year', $fiscalYear)->where('active', 1)->first();
                    if ($serviceRecord && $serviceRecord->service_id) {
                        // Create Salesforce session, only if service record exist
                        $data = array(
                            "serviceID" => $serviceRecord->service_id,
                            "studentID" => $mentee_data->externalId,
                            "mentorID" => Auth::user()->externalId,
                            "sessionDate" => $schedule_date,
                            "sessionDuration" => (int)$time_duration,
                            "sessionNote" => $name,
                            "sessionTypeID" => (int)$type,
                            "sessionSourceID" => 2,
                            "sessionLocationID" => $method_id
                        );

                        $loginResponse = loginToSalesforce();
                        $token = $loginResponse->token;
                        $baseURL = $loginResponse->baseURL;

                        $response = createSession($data, $token, $baseURL);

                        DB::table('session')->where('id', $id)->update(['externalId' => $response->id, 'creationmethod' => 'salesforceapi']);
                    }
                }
            }


            /*============Log Count ============================*/
            $exist_log = DB::table('mentor_session_log_count')->where('mentor_id', $mentor_id)->first();
            if (!empty($exist_log)) {
                $exist_count = $exist_log->count;
                $updated_count = ($exist_count + 1);
                DB::table('mentor_session_log_count')->where('id', $exist_log->id)->update(['count' => $updated_count]);
            } else {
                DB::table('mentor_session_log_count')->insert(['mentor_id' => $mentor_id, 'count' => 1]);
            }
            /*========================================*/
            session(['success_message' => "Session logged successfully"]);
        } else {
            session(['error_message' => "Session already exists"]);
        }
        return redirect('/mentor/sessionlog/list');

    }

}
