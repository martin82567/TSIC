<?php
/***********Mentor***********/
namespace App\Http\Controllers\Api\V1\mentor;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use File;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
class MeetingController extends Controller
{

    private $assigned_by;
    private $timezone;
    private $fcmApiKey;

    public function __construct(Request $request)
    {
        $this->fcmApiKey = config('app.fcmApiKey');

        $header = $request->header('Authorizations');
        if(empty($header)){
            response()->json(array('status'=>false,'message'=>"Authentication failed"))->send();
            exit();
        }
        if(substr($header, 0, 7) != "Bearer "){
            response()->json(array('status'=>false,'message'=>"Please add p refix and a space before the token"))->send();
            exit();
        }
        try{
            $this->header_str = substr($header,7,strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);

            $chk_user = DB::table('mentor')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit();
            }

            if(!empty($chk_user->is_logged_out)){
                response()->json(array('status'=>false,'message'=>"Logged Out"))->send();
                exit();
            }

            $this->assigned_by = $chk_user->assigned_by;


            $affiliate_data = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftjoin('timezones', 'timezones.value','admins.timezone')->where('admins.id',$this->assigned_by)->first();

            $aff_timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';

            $this->timezone = $aff_timezone;

            mentor_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit();
        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function add(Request $request)
    {
        $fcmApiKey = $this->fcmApiKey;

        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        $input = $request->all();
        $id = !empty($input['id'])?$input['id']:'';
        $title = !empty($input['title'])?$input['title']:'';
        $description = !empty($input['description'])?$input['description']:'';

        $date = !empty($input['date'])?$input['date']:'';
        $time = !empty($input['time'])?$input['time']:'';
        $mentee_id = !empty($input['mentee_id'])?$input['mentee_id']:'';

        $school_id = !empty($input['school_id'])?$input['school_id']:0;
        $school_type = !empty($input['school_type'])?$input['school_type']:'';
        $school_space = !empty($input['school_space'])?$input['school_space']:'';
        // $school_location = !empty($input['school_location'])?$input['school_location']:'';
        $session_method_location_id = !empty($request->session_method_location_id)?$request->session_method_location_id:'';


        if(empty($input))
            return response()->json(['status'=>false, 'message' => "Please give inputs", 'data' => array() ]);

        if(empty($id)){
            /*if(empty($title))
                return response()->json(['status'=>false, 'message' => "Please give agenda", 'data' => array() ]);*/
            /*if(empty($description))
                return response()->json(['status'=>false, 'message' => "Please give description", 'data' => array() ]);*/

            if(empty($date))
                return response()->json(['status'=>false, 'message' => "Please give date", 'data' => array() ]);

            if(empty($time))
                return response()->json(['status'=>false, 'message' => "Please give time", 'data' => array() ]);

            if(empty($mentee_id))
                return response()->json(['status'=>false, 'message' => "Please add mentee", 'data' => array() ]);


            if (empty($session_method_location_id)) {
                return response()->json(['status'=>false, 'message' => "Please add the method/location", 'data' => array()]);
            }


            // if(empty($school_space))
            //     return response()->json(['status'=>false, 'message' => "Please add school space", 'data' => array() ]);

            if(empty($school_space)){
                $school_space = '';
            }

            if(!empty($school_id)){
                $school_data = DB::table('school')->where('id', $school_id)->first();
                $address = !empty($school_data->address)?$school_data->address:'';
                $latitude = !empty($school_data->latitude)?$school_data->latitude:'';
                $longitude = !empty($school_data->longitude)?$school_data->longitude:'';
                $school_type = '';
            }else{
                $address = '';
                $latitude = '';
                $longitude = '';

                if(empty($school_type))
                    return response()->json(['status'=>false, 'message' => "Please mention school type", 'data' => array() ]);
            }



            $geboortedatum = $date;
            $geboortedatum = date("Y-m-d", strtotime($geboortedatum));
            list($y, $m, $d) = explode("-", $geboortedatum);

            $date = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');

            $myTime = preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $time);

            if ( $myTime != 1 )
            {
                return response()->json(['status'=>false, 'message' => "Time is incorrect", 'data' => array() ]);
            }

            $schedule_time = $date.' '.$time;

            if($schedule_time <= $created_date){
                return response()->json(['status'=>false, 'message' => "Invalid date and time", 'data' => array() ]);
            }

            $id = DB::table('meeting')->insertGetId(['title'=>$title , 'description'=>$description,'agency_id'=>$this->assigned_by , 'created_by'=>$this->user_id , 'created_by_type'=>'mentor' , 'schedule_time' => $schedule_time , 'address' => $address , 'latitude' => $latitude , 'longitude' => $longitude , 'school_id' => $school_id , 'school_type' => $school_type , 'session_method_location_id' => $session_method_location_id , 'school_location' => $school_space ,'created_date' => $created_date , 'mentor_id' => $this->user_id , 'mentee_id' => $mentee_id , 'created_from' => 'mobile_app' ]);

            DB::table('meeting_users')->insert(['meeting_id'=>$id , 'type' => 'mentor' , 'user_id' => $this->user_id  ]);
            DB::table('meeting_users')->insert(['meeting_id'=>$id , 'type' => 'mentee' , 'user_id' => $mentee_id  ]);
            DB::table('meeting_status')->insert(['meeting_id'=>$id , 'status' => 0 ]);

            $meeting = DB::table('meeting')->where('id',$id)->first();

            if(!empty($meeting->schedule_time)){
                $date_time = explode(" ", $meeting->schedule_time);
                $meeting->date = date('m-d-Y',strtotime($date_time[0]));;
                $meeting->time = $date_time[1];
                $meeting->mentee_id = $mentee_id;
            }

            /* ++++++++Mentee push notification ++++++++++ */
            $meeting_id = $meeting->id;
            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'reschedule_request','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);

            $notification_response = meeting_event_notification('mentee',$mentee_id,$meeting_id,'create');

            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

           /* $url = 'https://fcm.googleapis.com/fcm/send';

            $mentee_data = DB::table('mentee')->select('id','firebase_id','device_type')->where('id',$mentee_id)->first();

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'assign_meeting','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);


            if(!empty($mentee_data->device_type) && !empty($mentee_data->firebase_id)){


                $schedule_session_count = schedule_session_count('mentee',$mentee_id);
                $user_total_chat_count = user_total_chat_count('mentee',$mentee_id);
                $user_total_unread_goaltask_count = user_total_unread_goaltask_count('mentee',$mentee_id);

                $send_data = array('title' => $title,'type' => 'meeting_notification' , 'meeting_id' => $id,'message'=>$message,'firebase_token' => $mentee_data->firebase_id , 'unread_chat'=> $user_total_chat_count, 'unread_task' => ($schedule_session_count+$user_total_unread_goaltask_count));
                $data_arr = array('meeting_data' => $send_data);

                if($mentee_data->device_type == "iOS"){

                    $msg = array('message' => $message,'title' => $title, 'sound'=>"default", 'badge' => ($schedule_session_count+$user_total_chat_count+$user_total_unread_goaltask_count)  );
                    $fields = array('to' => $mentee_data->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

                }else if($mentee_data->device_type == "android"){

                    $fields = array('to' => $mentee_data->firebase_id,'data' => $send_data ); // For Android
                }

                $headers = array(
                    'Authorization: key=' . $fcmApiKey,
                    'Content-Type: application/json'
                );

                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, $url );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch );

                if ($result === FALSE) {
                    die('Curl failed: ' . curl_error($ch));
                }
                // Close connection
                curl_close($ch);

                $result_arr = json_decode($result);

                if(!empty($result_arr->success)){
                    DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => json_encode($result)]);

                    // DB::table('mentee_meeting_notification')->insert(['meeting_id'=>$id,'mentee_id'=>$mentee_id,'notification_type'=>'push','device_type'=>$mentee_data->device_type,'firebase_token'=>$mentee_data->firebase_id,'notification_response'=>json_encode($result) ]);
                }
            }*/

            /* ++++++++Mentee email notification ++++++++++ */
            $message = "A new Mentor Session has been scheduled for you. Please review and confirm the mentor session invite in the 'Schedule A Session' navigation on the Take Stock App profile screen.";

            if(!empty($mentee_data->email)){
                email_send($mentee_data->email,$title,$message);
            }


            return response()->json(['status'=>true, 'message' => "Session created successfully", 'data' => array('meeting'=>$meeting) ]);

        }else{
            $meeting = DB::table('meeting')->where('id',$id)->first();
            $data['title'] = !empty($title)?$title:$meeting->title;
            $data['description'] = !empty($description)?$description:$meeting->description;
            // $data['address'] = !empty($address)?$address:$meeting->address;
            // $data['latitude'] = !empty($latitude)?$latitude:$meeting->latitude;
            // $data['longitude'] = !empty($longitude)?$longitude:$meeting->longitude;
            $data['school_id'] = !empty($school_id)?$school_id:$meeting->school_id;
            $data['school_location'] = !empty($school_location)?$school_location:$meeting->school_location;

            $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$id)->where('status', 0)->first();

            if(!empty($date)){
                $geboortedatum = $date;
                $geboortedatum = date("Y-m-d", strtotime($geboortedatum));
                list($y, $m, $d) = explode("-", $geboortedatum);

                // var_dump(checkdate($d,$m,$y));
                // if(!checkdate($d, $m, $y)) {
                //     return response()->json(['status'=>false, 'message' => "Date is incorrect", 'data' => array() ]);
                // }
                // else {

                    $date = DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');
                    $edited_date = $date;


                // }
            }else{
                $schedule_time = $meeting->schedule_time;
                $date_time = explode(" ", $schedule_time);
                // $edited_date = date('m-d-Y',strtotime($date_time[0]));
                $edited_date = $date_time[0];
            }

            if(!empty($time)){

                $myTime = preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $time);

                if ( $myTime != 1 )
                {
                    return response()->json(['status'=>false, 'message' => "Time is incorrect", 'data' => array() ]);
                }else{
                    $edited_time = $time;
                }
            }else{
                $schedule_time = $meeting->schedule_time;
                $date_time = explode(" ", $schedule_time);
                // $edited_time = date('m-d-Y',strtotime($date_time[0]));
                $edited_time = $date_time[1];
            }

            $data['schedule_time'] = $edited_date.' '.$edited_time;

            $edit_msg = "Session updated successfully";

            DB::table('meeting')->where('id',$id)->update($data);

            /*+++++++++++++++++*/

            if(!empty($mentee_id)){
                $meeting_users = DB::table('meeting_users')->where('meeting_id',$id)->where('type','mentee')->first();

                // echo '<pre>'; print_r($meeting_users); die;

                if(empty($meeting_users)){
                    $meeting_users_id = DB::table('meeting_users')->insertGetId(['meeting_id'=>$id,'type'=>'mentee','user_id'=>$mentee_id,'status'=>0]);
                }else{
                    DB::table('meeting_users')->where('id',$meeting_users->id)->update(['meeting_id'=>$id,'type'=>'mentee','user_id'=>$mentee_id,'status'=>0]);
                }

            }
            /**/

            if(!empty($meeting_requests)){
                DB::table('meeting_requests')->where('id',$meeting_requests->id)->update(['status' => 1]);
                DB::table('meeting_status')->where('meeting_id',$id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);

                DB::table('meeting_users')->where('meeting_id',$id)->where('type', 'mentee')->update(['status' => 1]);

                $edit_msg = "Session recsheduled successfully";

                /*+++++Notification+++++*/

                $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'reschedule','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);

                $notification_response = meeting_event_notification('mentee',$mentee_id,$id,'reschedule');

                DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

                /*++++++++++*/

            }



            return response()->json(['status'=>true, 'message' => $edit_msg, 'data' => array() ]);


        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function list(Request $request)
    {
        $type = !empty($request->type)?$request->type:'';
        $page = !empty($request->page)?$request->page:0;
        $take = !empty($request->take)?$request->take:100;
        $skip = ($page*$take);
        if(empty($type))
            return response()->json(['status'=>false, 'message' => "Please add type", 'data' => array() ]);

        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');

        }else{
            $cur_date = date('Y-m-d H:i:s');

        }

        $meeting = array();

        if($type == 'requested'){

            // $meeting = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id', 'meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id',$this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->get()->toarray();

            $meeting = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("IFNULL(school.name,'') AS school_name,IFNULL(meeting_requests.note,'') AS note,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->where('meeting.mentor_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->take($take)->skip($skip)->get()->toarray();

            $count_data = DB::table('meeting')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->where('meeting.mentor_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->count();

            DB::table(MEETING_NOTIFICATION)->where('user_type','mentor')->where('user_id',$this->user_id)->update(['is_read'=>1]);

        }else if($type == 'upcoming'){

            // $meeting = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")->join('meeting', 'meeting.id','meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->get()->toarray();

            $meeting = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->where('meeting.mentor_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->take($take)->skip($skip)->get()->toarray();

            $count_data = DB::table('meeting')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->where('meeting.mentor_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->count();


        }else if($type == 'past'){


            // $meeting = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
            //                         ->leftJoin('meeting_users', function ($join) {
            //                             $join->on('meeting.id', '=', 'meeting_users.meeting_id')->where('meeting_users.type', '=', 'mentor')->where('meeting_users.user_id', $this->user_id);
            //                         })
            //                         ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
            //                         ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
            //                         ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."') OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'))")
            //                         ->orderBy('meeting.schedule_time', 'desc')
            //                         ->take(100)->get()->toarray();
            DB::enableQueryLog();
            $meeting = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."' AND meeting.mentor_id = ".$this->user_id.") OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'  AND meeting.mentor_id = ".$this->user_id."))")->orderBy('meeting.schedule_time', 'desc')->take($take)->skip($skip)->get()->toarray();

            $getQueryLog = DB::getQueryLog($meeting);
            // echo '<pre>'; print_r($getQueryLog); die;

            $count_data = DB::table('meeting')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."' AND meeting.mentor_id = ".$this->user_id.") OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'  AND meeting.mentor_id = ".$this->user_id."))")->count();

        }



        if(!empty($meeting)){
            foreach($meeting as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                /* If requested */

                $meeting_requests = DB::table('meeting_requests')->select('meeting_requests.mentee_id','meeting_requests.note','mentee.firstname','mentee.middlename','mentee.lastname')->leftJoin('mentee', 'mentee.id', 'meeting_requests.mentee_id')->where('meeting_id',$m->id)->first();
                $m->note = !empty($meeting_requests)?$meeting_requests->note:'';
                // $m->mentee_id = !empty($meeting_requests)?$meeting_requests->mentee_id:'';
                // $m->firstname = !empty($meeting_requests)?$meeting_requests->firstname:'';
                // $m->middlename = !empty($meeting_requests)?$meeting_requests->middlename:'';
                // $m->lastname = !empty($meeting_requests)?$meeting_requests->lastname:'';

                /* +++++++++++ */

                /*mentees*/

                $mentees = array();

                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));

                $m->mentees = $mentees;

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';


            }
        }

        // echo '<pre>'; print_r($meeting); die;


        return response()->json(['status'=>true, 'message' => "Here is your data", 'count_data' => $count_data , 'data' => $meeting ]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function all(Request $request)
    {
        # show all type meeting last 20...

        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
        }else{
            $cur_date = date('Y-m-d H:i:s');
        }

        $requested = $upcoming = $past = array();

        // $requested = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id', 'meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id',$this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->take(20)->get()->toarray();

        $requested = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("IFNULL(school.name,'') AS school_name,IFNULL(meeting_requests.note,'') AS note,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->where('meeting.mentor_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->take(20)->get()->toarray();

        if(!empty($requested)){
            foreach($requested as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                /* If requested */

                $meeting_requests = DB::table('meeting_requests')->select('meeting_requests.mentee_id','meeting_requests.note','mentee.firstname','mentee.middlename','mentee.lastname')->leftJoin('mentee', 'mentee.id', 'meeting_requests.mentee_id')->where('meeting_id',$m->id)->first();
                $m->note = !empty($meeting_requests)?$meeting_requests->note:'';
                $m->mentee_id = !empty($meeting_requests)?$meeting_requests->mentee_id:'';
                $m->firstname = !empty($meeting_requests)?$meeting_requests->firstname:'';
                $m->middlename = !empty($meeting_requests)?$meeting_requests->middlename:'';
                $m->lastname = !empty($meeting_requests)?$meeting_requests->lastname:'';

                /* +++++++++++ */

                /*mentees*/

                $mentees = array();

                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));

                $m->mentees = $mentees;
                $sessionType = 'requested';

                $m->sessionType = $sessionType;

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        // $upcoming = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")->join('meeting', 'meeting.id','meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->take(20)->get()->toarray();

        $upcoming = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->where('meeting.mentor_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->take(20)->get()->toarray();

        if(!empty($upcoming)){
            foreach($upcoming as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                /* If requested */

                // $meeting_requests = DB::table('meeting_requests')->select('meeting_requests.mentee_id','meeting_requests.note','mentee.firstname','mentee.middlename','mentee.lastname')->leftJoin('mentee', 'mentee.id', 'meeting_requests.mentee_id')->where('meeting_id',$m->id)->first();
                // $m->note = !empty($meeting_requests)?$meeting_requests->note:'';
                // $m->mentee_id = !empty($meeting_requests)?$meeting_requests->mentee_id:'';
                // $m->firstname = !empty($meeting_requests)?$meeting_requests->firstname:'';
                // $m->middlename = !empty($meeting_requests)?$meeting_requests->middlename:'';
                // $m->lastname = !empty($meeting_requests)?$meeting_requests->lastname:'';

                /* +++++++++++ */

                /*mentees*/

                $mentees = array();
                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));
                $m->mentees = $mentees;

                $sessionType = 'upcoming';

                $m->sessionType = $sessionType;

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        // $past = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
        //                             ->leftJoin('meeting_users', function ($join) {
        //                                 $join->on('meeting.id', '=', 'meeting_users.meeting_id')->where('meeting_users.type', '=', 'mentor')->where('meeting_users.user_id', $this->user_id);
        //                             })
        //                             ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                             ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                             ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."') OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'))")
        //                             ->orderBy('meeting.schedule_time', 'desc')
        //                             ->take(20)->get()->toarray();

        $past = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."' AND meeting.mentor_id = ".$this->user_id.") OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'  AND meeting.mentor_id = ".$this->user_id."))")->orderBy('meeting.schedule_time', 'desc')->take(20)->get()->toarray();

        if(!empty($past)){
            foreach($past as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                /* If requested */

                // $meeting_requests = DB::table('meeting_requests')->select('meeting_requests.mentee_id','meeting_requests.note','mentee.firstname','mentee.middlename','mentee.lastname')->leftJoin('mentee', 'mentee.id', 'meeting_requests.mentee_id')->where('meeting_id',$m->id)->first();
                // $m->note = !empty($meeting_requests)?$meeting_requests->note:'';
                // $m->mentee_id = !empty($meeting_requests)?$meeting_requests->mentee_id:'';
                // $m->firstname = !empty($meeting_requests)?$meeting_requests->firstname:'';
                // $m->middlename = !empty($meeting_requests)?$meeting_requests->middlename:'';
                // $m->lastname = !empty($meeting_requests)?$meeting_requests->lastname:'';

                /* +++++++++++ */

                /*mentees*/

                $mentees = array();
                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));
                $m->mentees = $mentees;

                $sessionType = 'past';

                $m->sessionType = $sessionType;

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        DB::table(MEETING_NOTIFICATION)->where('user_type','mentor')->where('user_id',$this->user_id)->update(['is_read'=>1]);


        return response()->json(['status'=>true, 'message' => "Here is your data", 'data' => array('requested'=>$requested , 'upcoming' => $upcoming , 'past' => $past) ]);

    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function no_reschedule(Request $request)
    {

        /* No reschedule is one type accept the meeting and decline the request */
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message' => "Meeting is required", 'data' => array() ]);

        $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$meeting_id)->where('status', 0)->first();

        if(empty($meeting_requests))
            return response()->json(['status'=>false, 'message' => "No pending request found on this meeting", 'data' => array() ]);


        DB::table('meeting_requests')->where('meeting_id',$meeting_id)->update(['status' => 2]);
        DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);
        DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->update(['status' => 1 ]);

        /*+++++++Notification+++++++*/
        $meeting_mentee = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentee')->first();
        $mentee_id = $meeting_mentee->user_id;
        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'deny_reschedule','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);

        $notification_response = meeting_event_notification('mentee',$mentee_id,$meeting_id,'deny_reschedule');

        DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

        /*++++++++++++++*/

        return response()->json(['status'=>true, 'message' => "This meeting has been cancelled", 'data' => array() ]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function cancel(Request $request)
    {
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message' => "Meeting is required", 'data' => array() ]);

        //status "4" for delete session from confirmed session
        DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 3 , 'cancel_date' => date('Y-m-d H:i:s')]);

        /*+++++++Notification+++++++*/
        $meeting_mentee = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentee')->first();
        $mentee_id = $meeting_mentee->user_id;
        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'cancel','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);

        $notification_response = meeting_event_notification('mentee',$mentee_id,$meeting_id,'cancel');
//        $notification_response = sendPushNotification('mentee',$mentee_id,$meeting_id,'cancel');

        DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
        /*++++++++++++++*/

        return response()->json(['status'=>true, 'message' => "Meeting cancelled successfully", 'data' => array() ]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function delete(Request $request)
    {
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message' => "Meeting is required", 'data' => array() ]);

        //status "4" for delete session from confirmed session
        DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 4 , 'cancel_date' => date('Y-m-d H:i:s')]);

        /*+++++++Notification+++++++*/
        $meeting_mentee = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentee')->first();
        $mentee_id = $meeting_mentee->user_id;
        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'cancel','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);

        $notification_response = meeting_event_notification('mentee',$mentee_id,$meeting_id,'delete');

        DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
        /*++++++++++++++*/

        return response()->json(['status'=>true, 'message' => "Meeting cancelled successfully", 'data' => array() ]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function upcoming_accepted_meeting(Request $request)
    {
        $meeting = array();

        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
        }else{
            $cur_date = date('Y-m-d H:i:s');
        }

        $meeting = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->join('meeting', 'meeting.id','meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->where('meeting_status.status', 1)->orderBy('meeting.id', 'desc')->get()->toarray();

        if(!empty($meeting)){
            foreach($meeting as $m){
                $date_time = explode(" ", $m->schedule_time);
                $m->date = date('m-d-Y',strtotime($date_time[0]));
                $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                $mentees = array();

                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));

                $m->mentees = $mentees;
            }
        }

        // echo '<pre>'; print_r($meeting);

        return response()->json(['status'=>true, 'message' => "Here is your data", 'data' => $meeting ]);



    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function todaymeeting(Request $request)
    {
        $meeting = array();

        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d');
        }else{
            $cur_date = date('Y-m-d');
        }

        DB::enableQueryLog();
        $meeting = DB::table('meeting_users')->select('school.name as school_name','meeting.title','meeting.description','meeting.latitude','meeting.longitude','meeting.schedule_time','meeting.id')->join('meeting', 'meeting.id','meeting_users.meeting_id')->join('school', 'school.id','meeting.school_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->whereDate('meeting.schedule_time' , $cur_date)->where('meeting_status.status', 1)->where('meeting.school_id','!=',0)->orderBy('meeting.id', 'desc')->get()->toarray();

        $getQueryLog = DB::getQueryLog($meeting);
        // echo '<pre>'; print_r($getQueryLog); die;
        return response()->json(['status'=>true, 'message' => "Here is your data", 'data' => $meeting ]);
    }
/*++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function logged_meeting(Request $request)
    {
        # code...
        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
        }else{
            $cur_date = date('Y-m-d H:i:s');
        }

        $take = !empty($request->take)?$request->take:10;
        $page = !empty($request->page)?$request->page:0;
        $skip = ($take*$page);
        $meeting = DB::table('meeting_users')->select('meeting.*','meeting_status.status','session_method_location.method_value')->join('meeting', 'meeting.id','meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->where('meeting.is_logged', 1)->where('meeting.schedule_time' ,'<=' , $cur_date)->orderBy('meeting.schedule_time', 'desc')->skip($skip)->take($take)->get()->toarray();

        if(!empty($meeting)){
            foreach($meeting as $m){
                $date_time = explode(" ", $m->schedule_time);
                $m->date = date('m-d-Y',strtotime($date_time[0]));
                $m->time = $date_time[1];

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                }

                /* If requested */

                $meeting_requests = DB::table('meeting_requests')->select('meeting_requests.mentee_id','meeting_requests.note','mentee.firstname','mentee.middlename','mentee.lastname')->leftJoin('mentee', 'mentee.id', 'meeting_requests.mentee_id')->where('meeting_id',$m->id)->first();
                $m->note = !empty($meeting_requests)?$meeting_requests->note:'';
                $m->mentee_id = !empty($meeting_requests)?$meeting_requests->mentee_id:'';
                $m->firstname = !empty($meeting_requests)?$meeting_requests->firstname:'';
                $m->middlename = !empty($meeting_requests)?$meeting_requests->middlename:'';
                $m->lastname = !empty($meeting_requests)?$meeting_requests->lastname:'';

                /* +++++++++++ */

                /*mentees*/

                $mentees = array();

                $mentees = DB::select( DB::raw("SELECT mentee.id,mentee.firstname,mentee.middlename,mentee.lastname,mentee.image FROM `meeting_users` LEFT JOIN mentee ON mentee.id = meeting_users.user_id WHERE `meeting_users`.`meeting_id` = ".$m->id." AND `meeting_users`.`type` = 'mentee'"));

                $m->mentees = $mentees;

                /* School Data*/

                $school_data = DB::table('school')->where('id',$m->school_id)->first();
                $m->school_name = !empty($school_data->name)?$school_data->name:'';


            }
        }

        $total_data = DB::table('meeting_users')->join('meeting', 'meeting.id','meeting_users.meeting_id')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->where('meeting_users.type','mentor')->where('meeting_users.user_id', $this->user_id)->where('meeting.is_logged', 1)->where('meeting.schedule_time' ,'<=' , $cur_date)->count();

        return response()->json(['status'=>true, 'message' => "Passed logged meeting", 'total_data' => $total_data , 'data' => $meeting ]);


    }
/*++++++++++++++++++++++++++++++++++++++++++++++++*/
}
