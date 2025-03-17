<?php


namespace App\Http\Controllers\Api\V1\mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Hash;
use Artisan;
use Config;
use Auth;
use DB;

class SessionController extends Controller
{

    private $user_id;
    private $externalId;
    private $creationmethod;
    private $assigned_by;

    public function __construct(Request $request)
    {
        $header = $request->header('Authorizations');
        if (empty($header)) {
            response()->json(array('status' => false, 'message' => "Authentication failed"))->send();
            exit();
        }
        if (substr($header, 0, 7) != "Bearer ") {
            response()->json(array('status' => false, 'message' => "Please add prefix and a space before the token"))->send();
            exit();
        }
        try {
            $this->header_str = substr($header, 7, strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);

            $chk_user = DB::table('mentor')->where('id', $this->user_id)->first();

            if (empty($chk_user)) {
                response()->json(array('status' => false, 'message' => "User not found"))->send();
                exit();
            }

            $this->creationmethod = $chk_user->creationmethod;
            $this->externalId = $chk_user->externalId;
            $this->assigned_by = $chk_user->assigned_by;

            mentor_last_activity($this->user_id);

        } catch (\Exception $e) {
            response()->json(array('status' => false, 'message' => "Token is invalid"))->send();
            exit();
        }

    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function list(Request $request)
    {
        $page = !empty($request->page) ? $request->page : 0;
        $take = !empty($request->take) ? $request->take : 100;
        $skip = ($page * $take);

        $session_list = DB::table('session')->select('session.*', 'mentee.firstname', 'mentee.middlename', 'mentee.lastname')->selectRaw("DATE_FORMAT(session.schedule_date,'%m-%d-%Y') AS schedule_date,IFNULL(session_method_location.method_id, 0) AS method_id,IFNULL(session_method_location.method_value, '') AS method_value")->leftJoin('mentee', 'mentee.id', 'session.mentee_id')->leftJoin('session_method_location', 'session_method_location.id', 'session.session_method_location_id')->where('session.mentor_id', $this->user_id)->where('session.status', 1)->orderBy('session.schedule_date', 'desc')->take($take)->skip($skip)->get()->toarray();

        $count_data = DB::table('session')->where('session.mentor_id', $this->user_id)->where('session.status', 1)->count();

        return response()->json(['status' => true, 'message' => "Session list", 'count_data' => $count_data, 'data' => $session_list]);
    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function add(Request $request)
    {

        $creationmethod = $this->creationmethod;

        $name = !empty($request->name) ? $request->name : '';
        $schedule_date = !empty($request->schedule_date) ? $request->schedule_date : '';
        $time_duration = !empty($request->time_duration) ? $request->time_duration : 0;
        $type = !empty($request->type) ? $request->type : '';
        $session_method_location_id = !empty($request->session_method_location_id) ? $request->session_method_location_id : '';
        $mentee_id = !empty($request->mentee_id) ? $request->mentee_id : '';
        $meeting_id = !empty($request->meeting_id) ? $request->meeting_id : '';
        $mentor_id = $this->user_id;
        $no_show = !empty($request->no_show) ? $request->no_show : 0;

        if ($no_show) {
            $time_duration = 0;
        }
        // $id = !empty($request->id)?$request->id:'';

        if (empty($name)) {
            return response()->json(['status' => false, 'message' => "Please give Description", 'data' => array()]);
        }


        if (empty($schedule_date)) {
            return response()->json(['status' => false, 'message' => "Please give date", 'data' => array()]);
        }

        if (!empty($schedule_date)) {
            $schedule_date = str_replace("-", "/", $schedule_date);
            $schedule_date = date("Y-m-d", strtotime($schedule_date));
        }

        // echo $schedule_date; die;

        if (empty($session_method_location_id)) {
            return response()->json(['status' => false, 'message' => "Please add the method/location", 'data' => array()]);
        }

        $get_session_method_location = DB::table('session_method_location')->where('id', $session_method_location_id)->first();

        $method_id = $get_session_method_location->method_id;


        if (empty($mentee_id)) {
            return response()->json(['status' => false, 'message' => "Please Choose Mentee", 'data' => array()]);
        }

//        if( empty($time_duration))
//            return response()->json(['status'=>false, 'message' => "Please add time duration", 'data' => array()]);

        if (empty($type))
            return response()->json(['status' => false, 'message' => "Please mention type", 'data' => array()]);

        if ($type != '1' && $type != '2')
            return response()->json(['status' => false, 'message' => "Type must be 1 or 2", 'data' => array()]);

//        if(!is_numeric($time_duration))
//            return response()->json(['status'=>false, 'message' => "Time duration must be numeric", 'data' => array()]);

        if (strlen($time_duration) > 10)
            return response()->json(['status' => false, 'message' => "Time duration maxlength is 10", 'data' => array()]);


        $affiliate_data = DB::table('admins')->select('id', 'timezone', 'externalId')->where('id', $this->assigned_by)->first();

        $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : 'America/New_York';

        if (!empty($timezone)) {
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        } else {
            $created_date = date('Y-m-d H:i:s');
        }

        if (!empty($meeting_id)) {
            $exist_meeting = DB::table('meeting')->find($meeting_id);
            if (empty($exist_meeting)) {
                return response()->json(['status' => false, 'message' => "No schedule session found", 'data' => array()]);
            }
            if (!empty($exist_meeting->is_logged)) {
                return response()->json(['status' => false, 'message' => "Already logged", 'data' => array()]);
            }
        }
        $check = DB::table('session')->where('name', $name)->where('schedule_date', $schedule_date)->where('time_duration', $time_duration)->where('type', $type)->where('session_method_location_id', $session_method_location_id)
            ->where('mentee_id', $mentee_id)->where('mentor_id', $mentor_id)->where('no_show', $no_show)->where('is_created_by_app', 1)->where('meeting_id', $meeting_id)->first();

        if (empty($check)) {

            $id = DB::table('session')->insertGetId(['name' => $name, 'is_created_by_app' => 1, 'schedule_date' => $schedule_date, 'time_duration' => $time_duration, 'type' => $type, 'session_method_location_id' => $session_method_location_id, 'mentee_id' => $mentee_id, 'mentor_id' => $mentor_id, 'created_date' => $created_date, 'meeting_id' => $meeting_id, 'no_show' => $no_show]);

            if (!empty($meeting_id)) {
                DB::table('meeting')->where('id', $meeting_id)->update(['is_logged' => 1]);
            }

            $message = 'Session added successfully';
            $status = true;

            $mentee_data = get_single_data_id('mentee', $mentee_id);
            $mentee_stardbID = $mentee_data->stardbID;


            /*===========Star DB Insertion============*/
            if ($creationmethod == 'salesforceapi' && !empty($this->externalId) && !empty($mentee_data->externalId)) {
                //Get Service ID for Salesforce Sessions
                if ($affiliate_data->externalId) {
                    $fiscalYear = getFiscalYear($schedule_date);
                    $serviceRecord = DB::table('agency_programs')->select('service_id')->where('agency_id', $affiliate_data->id)->where('year', $fiscalYear)->where('active', 1)->first();

                    if ($serviceRecord && $serviceRecord->service_id) {
                        // Create Salesforce session, only if service record exist
                        $data = array(
                            "serviceID" => $serviceRecord->service_id,
                            "studentID" => $mentee_data->externalId,
                            "mentorID" => $this->externalId,
                            "sessionDate" => $schedule_date,
                            "sessionDuration" => (int)$time_duration,
                            "sessionNote" => $name,
                            "sessionTypeID" => (int)$type,
                            "sessionSourceID" => 1,
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
        } else {
            $message = 'Session already exists';
            $status = false;
        }

        return response()->json(['status' => $status, 'message' => $message, 'data' => array()]);


    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function get_session_method_location(Request $request)
    {
        # code...
        $mentee_id = !empty($request->mentee_id) ? $request->mentee_id : '';

        $data = DB::table('session_method_location')->select('*');

        if (!empty($mentee_id)) {
            $mentee_data = get_single_data_id('mentee', $mentee_id);
            $is_chat_video = !empty($mentee_data) ? $mentee_data->is_chat_video : 0;
            if (empty($is_chat_video)) {
                $data = $data->whereRaw("id NOT IN (4)");
            }

        }

        $data = $data->where('status', 1)->get()->toarray();

        return response()->json(['status' => true, 'message' => "Here is the data", 'data' => array('session_method_location' => $data)]);
    }


}
