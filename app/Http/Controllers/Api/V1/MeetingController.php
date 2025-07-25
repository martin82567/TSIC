<?php 
/***********Mentee***********/
namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\DB;
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
	private $timezone; 
	public function __construct(Request $request)
    {
        $header = $request->header('Authorizations');        
        if(empty($header)){
            response()->json(array('status'=>false,'message'=>"Authentication failed"))->send();
            exit();             
        }        
        if(substr($header, 0, 7) != "Bearer "){
            response()->json(array('status'=>false,'message'=>"Please add prefix and a space before the token"))->send(); 
            exit();            
        }
        try{
            $this->header_str = substr($header,7,strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);   

            $chk_user = DB::table('mentee')->select('*')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit(); 
            }

            if(!empty($chk_user->is_logged_out)){
                response()->json(array('status'=>false,'message'=>"Logged Out"))->send();
                exit(); 
            }

            mentee_last_activity($this->user_id);

            $affiliate_id = !empty($chk_user->assigned_by)?$chk_user->assigned_by:'';

            $affiliate_data = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftjoin('timezones', 'timezones.value','admins.timezone')->where('admins.id',$affiliate_id)->first();

            $aff_timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';


            $this->timezone = $aff_timezone;

            $assign_mentee = DB::table('assign_mentee')->where('mentee_id', $this->user_id)->first();
            $this->assigned_by = $assign_mentee->assigned_by;  /*  Mentor Id */

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function list(Request $request)
    {  

    	if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
        }else{
            $cur_date = date('Y-m-d H:i:s');
        }

        $page = !empty($request->page)?$request->page:0;
        $take = !empty($request->take)?$request->take:100;
        $skip = ($page*$take);

        $data = array();      
        // $data = DB::table('meeting_users')
        //                         ->select('meeting_users.user_id','meeting_users.type','meeting_users.status AS meeting_users_status','meeting.*','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')
        //                         ->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')
        //                         ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                         ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                         ->where('meeting_users.type', 'mentee')
        //                         ->where('meeting_users.user_id', $this->user_id)
        //                         ->where('meeting.schedule_time' ,'>=' , $cur_date)
        //                         ->whereIn('meeting_status.status', [0,3] )
        //                         // ->where('meeting_users.status', 0)
        //                         ->orderBy('meeting.id', 'desc')
        //                         // ->orderBy('meeting.schedule_time', 'asc')
        //                         ->get()->toarray();

        $data = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value',DB::raw("(SELECT COUNT(*) FROM meeting_requests WHERE meeting_requests.meeting_id = meeting.id) AS is_requested"))->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,IFNULL(meeting_web_status.web_status, 1) AS web_status,IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done,IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name,IFNULL(meeting_requests.note,'') AS note")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->take($take)->skip($skip)->get()->toarray();

        $count_data = DB::table('meeting')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->count();

        if(!empty($data)){
            foreach($data as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];
                // $m->time = date('h:i A', strtotime($date_time[1]));

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                    $agency = DB::table('admins')->where('id',$m->agency_id)->first();
                    // $m->creator_name = $agency->name;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                    $mentor = DB::table('mentor')->where('id',$m->created_by)->first();
                    // $m->creator_name = $mentor->firstname.' '.$mentor->middlename.' '.$mentor->lastname;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$m->id)->first();
                if(!empty($meeting_requests)){
                    $m->is_request_sent = true;
                }else{
                    $m->is_request_sent = false;
                }

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';               
                
            }
        }

        DB::table(MEETING_NOTIFICATION)->where('user_type','mentee')->where('user_id',$this->user_id)->update(['is_read'=>1]);

        return response()->json(['status'=>true, 'message'=>"Here is the list" , 'count_data' => $count_data , 'data'=>array('meeting'=>$data)]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function saverequest(Request $request)
    {
    	$input = $request->all();
    	$meeting_id = !empty($input['meeting_id'])?$input['meeting_id']:'';
    	$note = !empty($input['note'])?$input['note']:'';

    	if(empty($meeting_id))
    		return response()->json(['status'=>false, 'message'=>"Meeting is required" , 'data'=>array()]);

    	if(empty($note))
    		return response()->json(['status'=>false, 'message'=>"Please add note" , 'data'=>array()]);

    	if(strlen($note) > 1000)
    		return response()->json(['status'=>false, 'message'=>"Too large message" , 'data'=>array()]);

    	$is_mentee_exist = DB::table('meeting_users')->select('meeting_users.*')->join('meeting', 'meeting.id','meeting_users.meeting_id')->where('meeting_users.meeting_id',$meeting_id)->where('meeting_users.user_id', $this->user_id)->where('meeting_users.type','mentee')->first();

    	if(empty($is_mentee_exist))
    		return response()->json(['status'=>false, 'message'=>"You are not assigned in this meeting" , 'data'=>array()]);

    	$is_exist = DB::table('meeting_requests')->where('meeting_id',$meeting_id)->first();

    	if(empty($is_exist)){
    		$id = DB::table('meeting_requests')->insertGetId(['meeting_id'=>$meeting_id, 'mentor_id'=> $this->assigned_by , 'mentee_id'=> $this->user_id , 'note' => $note ]);

	    	$data = DB::table('meeting_requests')->select('meeting_requests.*')->where('meeting_requests.id',$id)->first();

            /*+++++++Notification+++++++*/
            $meeting_mentor = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentor')->first();
            $mentor_id = $meeting_mentor->user_id;
            if(!empty($this->timezone)){
                date_default_timezone_set($this->timezone);
                $created_date = date('Y-m-d H:i:s');
            }else{
                $created_date = date('Y-m-d H:i:s');
            }

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'reschedule_request','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);

            $notification_response = meeting_event_notification('mentor',$mentor_id,$meeting_id,'reschedule_request');
            
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
            
            /*++++++++++++++*/


	    	return response()->json(['status'=>true, 'message'=>"Your request for schedule has been added successfully" , 'data'=>array()]);

    	}else{
    		return response()->json(['status'=>false, 'message'=>"A request is already there in this meeting" , 'data'=>array()]);
    	}
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function make_accept_web_meeting(Request $request)
    {
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';
        $web_status = !empty($request->web_status)?$request->web_status:'';

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message'=>"Meeting is required" , 'data'=>array()]);

        if(empty($web_status))
            return response()->json(['status'=>false, 'message'=>"Please give status" , 'data'=>array()]);

        if(!is_numeric($web_status))
            return response()->json(['status'=>false, 'message'=>"Status value should be numeric" , 'data'=>array()]);

        if($web_status != 1 && $web_status != 2)
            return response()->json(['status'=>false, 'message'=>"Unknown status value" , 'data'=>array()]);

        $is_exist = DB::table('meeting')->where('id',$meeting_id)->where('mentee_id', $this->user_id)->first();

        if(empty($is_exist))
            return response()->json(['status'=>false, 'message'=>"No meeting assigned to you" , 'data'=>array()]);

        $is_web_meeting = DB::table('meeting')->where('id',$meeting_id)->where('created_from','affiliate_portal')->first();

        if(empty($is_web_meeting))
            return response()->json(['status'=>false, 'message'=>"No web meeting found" , 'data'=>array()]);

        $is_status_changed = DB::table('meeting_web_status')->where('meeting_id',$meeting_id)->where('is_web_status_done', 1)->first();

        if(empty($is_status_changed)){
            DB::table('meeting_web_status')->where('meeting_id',$meeting_id)->update(['web_status' => $web_status,'is_web_status_done'=>1,'web_status_date'=>date('Y-m-d H:i:s')]);

            DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status'=>1 , 'accept_date'=>date('Y-m-d H:i:s')]);

            /*+++++++Notification+++++++*/
            $meeting_mentor = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentor')->first();
            $mentor_id = $meeting_mentor->user_id;
            if(!empty($this->timezone)){
                date_default_timezone_set($this->timezone);
                $created_date = date('Y-m-d H:i:s');
            }else{
                $created_date = date('Y-m-d H:i:s');
            }

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'accept','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);
            $notification_response = meeting_event_notification('mentor',$mentor_id,$meeting_id,'accept');
            
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => $notification_response]);
            
            /*++++++++++++++*/


            return response()->json(['status'=>true, 'message'=>"Status changed successfully" , 'data'=>array()]);

        }else{
            return response()->json(['status'=>false, 'message'=>"You have already changed the status" , 'data'=>array()]);
        }
    }
    public function make_accept_web_meeting_bkp(Request $request)
    {
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';
        $web_status = !empty($request->web_status)?$request->web_status:'';

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message'=>"Meeting is required" , 'data'=>array()]);

        if(empty($web_status))
            return response()->json(['status'=>false, 'message'=>"Please give status" , 'data'=>array()]);

        if(!is_numeric($web_status))
            return response()->json(['status'=>false, 'message'=>"Status value should be numeric" , 'data'=>array()]);

        if($web_status != 1 && $web_status != 2)
            return response()->json(['status'=>false, 'message'=>"Unknown status value" , 'data'=>array()]);

        $is_exist = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id', $this->user_id)->first();

        if(empty($is_exist))
            return response()->json(['status'=>false, 'message'=>"No meeting assigned to you" , 'data'=>array()]);

        $is_web_meeting = DB::table('meeting')->where('id',$meeting_id)->where('created_by_type','')->first();

        if(empty($is_web_meeting))
            return response()->json(['status'=>false, 'message'=>"No web meeting found" , 'data'=>array()]);

        $is_status_changed = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id',$this->user_id)->where('is_web_status_done', 1)->first();

        if(empty($is_status_changed)){
            DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id', $this->user_id)->update(['web_status' => $web_status,'is_web_status_done'=>1,'web_status_date'=>date('Y-m-d H:i:s')]);

            DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status'=>1 , 'accept_date'=>date('Y-m-d H:i:s')]);

            /*+++++++Notification+++++++*/
            $meeting_mentor = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentor')->first();
            $mentor_id = $meeting_mentor->user_id;
            if(!empty($this->timezone)){
                date_default_timezone_set($this->timezone);
                $created_date = date('Y-m-d H:i:s');
            }else{
                $created_date = date('Y-m-d H:i:s');
            }

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'accept','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);
            $notification_response = meeting_event_notification('mentor',$mentor_id,$meeting_id,'accept');
            
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => $notification_response]);
            
            /*++++++++++++++*/


            return response()->json(['status'=>true, 'message'=>"Status changed successfully" , 'data'=>array()]);

        }else{
            return response()->json(['status'=>false, 'message'=>"You have already changed the status" , 'data'=>array()]);
        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function make_accepted(Request $request)
    {
        $meeting_id = !empty($request->meeting_id)?$request->meeting_id:'';
        $status_id = !empty($request->status_id)?$request->status_id:'';

        /* 1 => accepted */

        if(empty($meeting_id))
            return response()->json(['status'=>false, 'message'=>"Meeting is required" , 'data'=>array()]); 

        if(empty($status_id))
            return response()->json(['status'=>false, 'message'=>"Status is required" , 'data'=>array()]);

        if(!is_numeric($status_id))
            return response()->json(['status'=>false, 'message'=>"Status value should be numeric" , 'data'=>array()]);

        if($status_id != 1)
            return response()->json(['status'=>false, 'message'=>"Incorrect status value" , 'data'=>array()]);

        $is_exist = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->where('user_id', $this->user_id)->first();

        if(empty($is_exist))
            return response()->json(['status'=>false, 'message'=>"No meeting found" , 'data'=>array()]);



        $is_accepted = DB::table('meeting_status')->where('meeting_id',$meeting_id)->where('status', 1)->first();

        if(empty($is_accepted)){
            DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);

            DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->where('user_id', $this->user_id)->update(['status' => 1]);

            /*+++++++Notification+++++++*/
            $meeting_mentor = DB::table(MEETING_USERS)->where('meeting_id',$meeting_id)->where('type','mentor')->first();
            $mentor_id = $meeting_mentor->user_id;
            if(!empty($this->timezone)){
                date_default_timezone_set($this->timezone);
                $created_date = date('Y-m-d H:i:s');
            }else{
                $created_date = date('Y-m-d H:i:s');
            }

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'accept','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);

            $notification_response = meeting_event_notification('mentor',$mentor_id,$meeting_id,'accept');
            
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
            /*++++++++++++++*/

            return response()->json(['status'=>true, 'message'=>"You have accepted this meeting successfully" , 'data'=>array()]);

        }else{
            return response()->json(['status'=>false, 'message'=>"You have already accepted this meeting" , 'data'=>array()]);
        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function upcoming(Request $request)
    {
    	if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');            
        }else{
            $cur_date = date('Y-m-d H:i:s');            
        }

        $page = !empty($request->page)?$request->page:0;
        $take = !empty($request->take)?$request->take:100;
        $skip = ($page*$take);

        $data = array();  
        DB::enableQueryLog();

        // $data = DB::table('meeting_users')
        //                             ->select('meeting_users.user_id','meeting_users.type','meeting.*','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')
        //                             ->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
        //                             ->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')
        //                             ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                             ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                             ->where('meeting_users.type', 'mentee')
        //                             ->where('meeting_users.user_id', $this->user_id)
        //                             ->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")
        //                             // ->where('meeting.schedule_time' ,'>=' , $cur_date)
        //                             ->where('meeting_status.status', 1)
        //                             ->orderBy('meeting.schedule_time', 'asc')
        //                             ->get()->toarray();

        $data = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value') ->selectRaw("IFNULL(school.name,'') AS school_name,date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name,IFNULL(meeting_web_status.web_status, 1) AS web_status,IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->take($take)->skip($skip)->get()->toarray(); 

        $count_data = DB::table('meeting')->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->count();        
        

        if(!empty($data)){
            foreach($data as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));;
                // $m->time = $date_time[1];
                // $m->time = date('h:i A', strtotime($date_time[1]));

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                    $agency = DB::table('admins')->where('id',$m->agency_id)->first();
                    // $m->creator_name = $agency->name;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                    $mentor = DB::table('mentor')->where('id',$m->created_by)->first();
                    // $m->creator_name = $mentor->firstname.' '.$mentor->middlename.' '.$mentor->lastname;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$m->id)->first();
                if(!empty($meeting_requests)){
                    $m->is_request_sent = true;
                }else{
                    $m->is_request_sent = false;
                }

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        // echo '<pre>'; print_r($data); die;

        return response()->json(['status'=>true, 'message'=>"Here is the list" , 'cur_date' => $cur_date, 'count_data' => $count_data , 'data'=>array('meeting'=>$data)]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function past(Request $request)
    {
    	if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
            
        }else{
            $cur_date = date('Y-m-d H:i:s');
            
        }

        DB::enableQueryLog();

        $data = array();  
        // $data = DB::table('meeting_users')
        //                             ->select('meeting_users.user_id','meeting_users.type','meeting.*','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')
        //                             ->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
        //                             ->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')
        //                             ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                             ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                             ->where('meeting_users.type', 'mentee')
        //                             ->where('meeting_users.user_id', $this->user_id)
        //                             // ->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."') ")
        //                             ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."') OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'))")
        //                             // ->where('meeting.schedule_time' ,'<=' , $cur_date)
        //                             ->orderBy('meeting.schedule_time', 'desc')
        //                             ->take(100)->get()->toarray();

        $data = DB::table('meeting')->select('meeting.*','meeting_users.user_id','meeting_users.type','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
                                    ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
                                    ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
                                    ->leftJoin('meeting_users', function ($join) {
                                        $join->on('meeting.id', '=', 'meeting_users.meeting_id')->where('meeting_users.type', '=', 'mentee')->where('meeting_users.user_id', $this->user_id);
                                    })                                    
                                    ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."') OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'))")
                                    ->orderBy('meeting.schedule_time', 'desc')
                                    ->take(100)->get()->toarray();

        // echo '<pre>'; print_r(DB::getQueryLog($data)); die;

        if(!empty($data)){
            foreach($data as $m){
                $date_time = explode(" ", $m->schedule_time);
                $m->date = date('m-d-Y',strtotime($date_time[0]));;
                $m->time = $date_time[1];
                $m->time = date('h:i A', strtotime($date_time[1]));

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                    $agency = DB::table('admins')->where('id',$m->agency_id)->first();
                    $m->creator_name = $agency->name;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                    $mentor = DB::table('mentor')->where('id',$m->created_by)->first();
                    $m->creator_name = $mentor->firstname.' '.$mentor->middlename.' '.$mentor->lastname;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$m->id)->first();
                if(!empty($meeting_requests)){
                    $m->is_request_sent = true;
                }else{
                    $m->is_request_sent = false;
                }

                /* School Data*/

                $school_data = DB::table('school')->where('id',$m->school_id)->first();
                $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        // echo '<pre>'; print_r($data); die;

        return response()->json(['status'=>true, 'message'=>"Here is the list" , 'cur_date' => $cur_date, 'data'=>array('meeting'=>$data)]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 
    public function all(Request $request)
    {
        # code...
        if(!empty($this->timezone)){
            date_default_timezone_set($this->timezone);
            $cur_date = date('Y-m-d H:i:s');
            
        }else{
            $cur_date = date('Y-m-d H:i:s');
            
        }

        // $requested = DB::table('meeting_users')
        //                         ->select('meeting_users.user_id','meeting_users.type','meeting_users.status AS meeting_users_status','meeting.*','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')
        //                         ->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')
        //                         ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                         ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                         ->where('meeting_users.type', 'mentee')
        //                         ->where('meeting_users.user_id', $this->user_id)
        //                         ->where('meeting.schedule_time' ,'>=' , $cur_date)
        //                         ->whereIn('meeting_status.status', [0,3] )
        //                         ->orderBy('meeting.id', 'desc')
        //                         ->take(20)->get()->toarray();

        $requested = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value',DB::raw("(SELECT COUNT(*) FROM meeting_requests WHERE meeting_requests.meeting_id = meeting.id) AS is_requested"))->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,IFNULL(meeting_web_status.web_status, 1) AS web_status,IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done,IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->where('meeting.schedule_time' ,'>=' , $cur_date)->whereIn('meeting_status.status', [0,3])->orderBy('meeting.id','desc')->take(20)->get()->toarray();

        if(!empty($requested)){
            foreach($requested as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));
                // $m->time = $date_time[1];
                // $m->time = date('h:i A', strtotime($date_time[1]));

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                    $agency = DB::table('admins')->where('id',$m->agency_id)->first();
                    // $m->creator_name = $agency->name;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                    $mentor = DB::table('mentor')->where('id',$m->created_by)->first();
                    // $m->creator_name = $mentor->firstname.' '.$mentor->middlename.' '.$mentor->lastname;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$m->id)->first();
                if(!empty($meeting_requests)){
                    $m->is_request_sent = true;
                }else{
                    $m->is_request_sent = false;
                }

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        // $upcoming = DB::table('meeting_users')
        //                             ->select('meeting_users.user_id','meeting_users.type','meeting.*','meeting_status.status','meeting_users.web_status','meeting_users.is_web_status_done','meeting_users.web_status_date','session_method_location.method_value')
        //                             ->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time")
        //                             ->leftJoin('meeting', 'meeting.id', 'meeting_users.meeting_id')
        //                             ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
        //                             ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
        //                             ->where('meeting_users.type', 'mentee')
        //                             ->where('meeting_users.user_id', $this->user_id)
        //                             ->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")
        //                             // ->where('meeting.schedule_time' ,'>=' , $cur_date)
        //                             ->where('meeting_status.status', 1)
        //                             ->orderBy('meeting.schedule_time', 'asc')
        //                             ->take(20)->get()->toarray();

        $upcoming = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value') ->selectRaw("IFNULL(school.name,'') AS school_name,date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name,IFNULL(meeting_web_status.web_status, 1) AS web_status,IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')->where('meeting.mentee_id', $this->user_id)->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc')->take(20)->get()->toarray(); 

        if(!empty($upcoming)){
            foreach($upcoming as $m){
                // $date_time = explode(" ", $m->schedule_time);
                // $m->date = date('m-d-Y',strtotime($date_time[0]));;
                // $m->time = $date_time[1];
                // $m->time = date('h:i A', strtotime($date_time[1]));

                if($m->created_by_type == ''){
                    $m->is_mentor_created = false;
                    $agency = DB::table('admins')->where('id',$m->agency_id)->first();
                    // $m->creator_name = $agency->name;
                }else if($m->created_by_type == 'mentor'){
                    $m->is_mentor_created = true;
                    $mentor = DB::table('mentor')->where('id',$m->created_by)->first();
                    // $m->creator_name = $mentor->firstname.' '.$mentor->middlename.' '.$mentor->lastname;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$m->id)->first();
                if(!empty($meeting_requests)){
                    $m->is_request_sent = true;
                }else{
                    $m->is_request_sent = false;
                }

                /* School Data*/

                // $school_data = DB::table('school')->where('id',$m->school_id)->first();
                // $m->school_name = !empty($school_data->name)?$school_data->name:'';
            }
        }

        DB::table(MEETING_NOTIFICATION)->where('user_type','mentee')->where('user_id',$this->user_id)->update(['is_read'=>1]);

        return response()->json(['status'=>true, 'message' => "Here is your data", 'data' => array('requested'=>$requested , 'upcoming' => $upcoming ) ]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/ 

}