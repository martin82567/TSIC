<?php
namespace App\Http\Controllers\Mentee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MeetingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void  Scheduled Session ...
     */

    public function __construct()
    {
        $this->middleware('auth:mentee');

    }
    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;
        $type = !empty($request->type)?$request->type:'requested';
        $view = !empty($request->view)?$request->view:'calendar';

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $cur_date = date('Y-m-d H:i:s');
        }else{
            $cur_date = date('Y-m-d H:i:s');
        }

        $meeting = array();

        $requested = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value',
            DB::raw("(SELECT COUNT(*) FROM meeting_requests WHERE meeting_requests.meeting_id = meeting.id) AS is_requested"))
            ->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,
                            DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,IFNULL(meeting_web_status.web_status, 1) AS web_status,
                            IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done,IF( meeting.created_by = 0, 
                            (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor 
                            WHERE mentor.id = meeting.created_by) ) as creator_name,IFNULL(meeting_requests.note,'') AS note")
            ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
            ->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')
            ->leftJoin('school','school.id','meeting.school_id')
            ->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')
            ->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')
            ->where('meeting.mentee_id', Auth::user()->id)
            ->where('meeting.schedule_time' ,'>=' , $cur_date)
            ->whereIn('meeting_status.status', [0,3])
            ->orderBy('meeting.id','desc');

        $upcoming = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value')
            ->selectRaw("IFNULL(school.name,'') AS school_name,date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,
                            DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time,
                            IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , 
                            (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name,
                            IFNULL(meeting_web_status.web_status, 1) AS web_status,IFNULL(meeting_web_status.is_web_status_done, 0) AS is_web_status_done")
            ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
            ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
            ->leftJoin('school','school.id','meeting.school_id')
            ->leftJoin('meeting_web_status', 'meeting_web_status.meeting_id','meeting.id')
            ->where('meeting.mentee_id', Auth::user()->id)
            ->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")
            ->where('meeting_status.status', 1)->orderBy('meeting.schedule_time', 'asc');

        $past = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value')
            ->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,IFNULL(school.name,'') AS school_name,
                            DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time,
                            IF( meeting.created_by = 0, (SELECT name FROM admins WHERE admins.id = meeting.agency_id) , 
                            (SELECT CONCAT(firstname,' ',middlename,' ',lastname) FROM mentor WHERE mentor.id = meeting.created_by) ) as creator_name")
            ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
            ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
            ->leftJoin('school','school.id','meeting.school_id')
            ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."' AND meeting.mentee_id = ".Auth::user()->id.") 
                            OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'  AND meeting.mentee_id = ".Auth::user()->id."))")
            ->orderBy('meeting.schedule_time', 'desc');

        if($view == 'calendar') {

            if($type == 'requested'){

                $meetings = $requested->get();

            }else if($type == 'upcoming'){

                $meetings = $upcoming->get();

            }else if($type == 'past'){

                $meetings = $past->get();

            }
        } else if($view == 'list') {

            if ($type == 'requested') {

                $meetings = $requested->paginate(10);

            } else if ($type == 'upcoming') {

                $meetings = $upcoming->paginate(10);

            } else if ($type == 'past') {

                $meetings = $past->paginate(10);

            }

            $meetings->appends(array('type'=>$type, 'view'=>$view))->links();

        }

        return view('mentee.list-meeting')->with('type',$type)->with('view',$view)->with('meetings',$meetings);
    }


    public function accept_app_meeting(Request $request)
    {
        /* No reschedule is one type accept the meeting and decline the request */
        $id = !empty($request->id)?$request->id:'';

        try{
            $meeting_id = Crypt::decrypt($id);

            $is_exist = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->where('user_id',  Auth::user()->id)->first();

            if(empty($is_exist)){
                session(['error_message' => "No meeting found"]);
            }

            $is_accepted = DB::table('meeting_status')->where('meeting_id',$meeting_id)->where('status', 1)->first();

            if(empty($is_accepted)){
                DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);
                DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->where('user_id',  Auth::user()->id)->update(['status' => 1]);

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

                session(['success_message' => "You have accepted this meeting successfully"]);

            }else{

                session(['error_message' => "You have already accepted this meeting"]);
            }

            return redirect('/mentee/meeting/list?type=requested&view=calendar');

        } catch ( \DecryptException $e ) {
            return redirect('/mentee/meeting/list?type=requested&view=calendar');
        }

    }

    public function accept_web_meeting(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';
        $web_status = !empty($request->web_status)?$request->web_status:'';

        try{
            $meeting_id = Crypt::decrypt($id);
            $is_exist = DB::table('meeting')->where('id',$meeting_id)->where('mentee_id', $this->user_id)->first();

            if(empty($is_exist)){
                session(['error_message' => "No meeting assigned to you"]);
            }

            $is_web_meeting = DB::table('meeting')->where('id',$meeting_id)->where('created_from','affiliate_portal')->first();

            if(empty($is_web_meeting)){
                session(['error_message' => "No web meeting found"]);
            }

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

                $meeting_title = $is_web_meeting->title;

                dd($meeting_title);

                $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'accept','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);
                $notification_response = meeting_event_notification('mentor',$mentor_id,$id,$meeting_title,'accept');
                DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => $notification_response]);

                session(['success_message'=>"Session request accepted successfully"]);

            }else{
                session(['error_message'=>"You have already changed the status"]);
            }

            return redirect('/mentee/meeting/list?type=requested&view=calendar');

        }catch ( \DecryptException $e ) {
            return redirect('/mentee/meeting/list?type=requested&view=calendar');
        }
    }


    public function accept_web_meeting_bkp(Request $request)
    {

        $id = !empty($request->id)?$request->id:'';
        $web_status = !empty($request->web_status)?$request->web_status:'';

        try{
            $meeting_id = Crypt::decrypt($id);
            $is_exist = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id', Auth::user()->id)->first();

            if(empty($is_exist)){
                session(['error_message' => "No meeting assigned to you"]);
            }

            $is_web_meeting = DB::table('meeting')->where('id',$meeting_id)->where('created_by_type','')->first();

            if(empty($is_web_meeting)){
                session(['error_message' => "No web meeting found"]);
            }

            $is_status_changed = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id',Auth::user()->id)->where('is_web_status_done', 1)->first();

            if(empty($is_status_changed)){
                DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->where('user_id', Auth::user()->id)->update(['web_status' => $web_status,'is_web_status_done'=>1,'web_status_date'=>date('Y-m-d H:i:s')]);

                DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status'=>1 , 'accept_date'=>date('Y-m-d H:i:s')]);

                session(['success_message'=>"Session request accepted successfully"]);

            }else{
                session(['error_message'=>"You have already changed the status"]);
            }

            return redirect(route('mentee.meeting.index'));

        }catch ( \DecryptException $e ) {
            return redirect(route('mentee.meeting.index'));
        }

    }


    public function request_reschedule(Request $request)
    {
        $id = $request->meeting_id;
        $note = !empty($request->note)?$request->note:'';

        if(!empty($note)){

            $meeting_users = DB::table('meeting_users')->select('*')->where('meeting_id',$id)->where('type','mentor')->first();
            $mentor_id = $meeting_users->user_id;

            $is_exist = DB::table('meeting_requests')->where('meeting_id',$id)->first();

            if(empty($is_exist)){
//                $meeting_id = Crypt::decrypt($id);
                DB::table('meeting_requests')->insert(['meeting_id'=>$id, 'mentor_id'=> $mentor_id , 'mentee_id'=> Auth::user()->id , 'note' => $note ]);

                $meeting_mentor = DB::table(MEETING_USERS)->where('meeting_id',$id)->where('type','mentor')->first();
                $mentor_id = $meeting_mentor->user_id;
                if(!empty($this->timezone)){
                    date_default_timezone_set($this->timezone);
                    $created_date = date('Y-m-d H:i:s');
                }else{
                    $created_date = date('Y-m-d H:i:s');
                }

                $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'reschedule','user_type'=>'mentor','user_id'=>$mentor_id,'notification_response'=>'','created_at'=>$created_date]);
                $notification_response = meeting_event_notification('mentor',$mentor_id,$id,'reschedule_request');
                DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => $notification_response]);

                session(['success_message' => "Request has been submitted successfully"]);

            }else{

                session(['error_message' => "A request is already there in this meeting"]);
            }


        }else{
            session(['error_message' => "Please add note"]);
        }

        return redirect('/mentee/meeting/list?type=requested&view=calendar');

    }



}
