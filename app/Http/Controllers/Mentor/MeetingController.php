<?php
namespace App\Http\Controllers\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use MaddHatter\LaravelFullcalendar\Facades\Calendar;
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
        $this->middleware('auth:mentor');

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

        $meetings = array();

        $requested = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')
                        ->selectRaw("IFNULL(school.name,'') AS school_name,IFNULL(meeting_requests.note,'') AS note,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,
                                    DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")
                        ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
                        ->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')
                        ->leftJoin('school','school.id','meeting.school_id')
                        ->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')
                        ->leftJoin('mentee', 'mentee.id','meeting.mentee_id')
                        ->where('meeting.mentor_id', Auth::user()->id)->where('meeting.schedule_time' ,'>=' , $cur_date)
                        ->whereIn('meeting_status.status', [0,3])
                        ->orderBy('meeting.id','desc');

        $upcoming = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')
                        ->selectRaw("IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")
                        ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')
                        ->leftJoin('school','school.id','meeting.school_id')
                        ->leftJoin('mentee', 'mentee.id','meeting.mentee_id')
                        ->where('meeting.mentor_id', Auth::user()->id)
                        ->whereRaw("(date_add(meeting.schedule_time,interval 30 minute) >= '".$cur_date."')")
                        ->where('meeting_status.status', 1)
                        ->orderBy('meeting.schedule_time', 'asc');

        $past = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname','mentee.middlename','mentee.lastname','mentee.image')
                        ->selectRaw("date_add(meeting.schedule_time,interval 30 minute) as newSchedule_time,IFNULL(school.name,'') AS school_name,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,
                                    DATE_FORMAT(meeting.schedule_time,'%H:%i:%s') AS time")
                        ->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')
                        ->leftJoin('session_method_location', 'session_method_location.id','meeting.session_method_location_id')
                        ->leftJoin('school','school.id','meeting.school_id')
                        ->leftJoin('mentee', 'mentee.id','meeting.mentee_id')
                        ->whereRaw("(meeting_status.status = 1 AND date_add(meeting.schedule_time,interval 30 minute) <= '".$cur_date."' AND meeting.mentor_id = ".Auth::user()->id.") 
                                    OR ((meeting_status.status != 1 AND meeting.schedule_time <= '".$cur_date."'  AND meeting.mentor_id = ".Auth::user()->id."))")
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
            if($type == 'requested'){

                $meetings = $requested->paginate(10);

            }else if($type == 'upcoming'){

                $meetings = $upcoming->paginate(10);

            }else if($type == 'past'){

                $meetings = $past->paginate(10);
            }

                    $meetings->appends(array('type'=>$type, 'view'=>$view))->links();

        }

        return view('mentor.list-meeting')->with('type',$type)->with('view',$view)->with('meetings',$meetings);
    }


    public function add(Request $request)
    {
        $id = $request->id;
        try{
            $meeting_id = Crypt::decrypt($id);
            $meeting_data = array();
            $is_datetime_valid = true;
            $meeting_requests = array();
            if(!empty($meeting_id)){
                $meeting_data = DB::table('meeting')
                                            ->select('meeting.*','meeting_users.user_id AS mentee_id')
                                            ->leftJoin('meeting_users','meeting_users.meeting_id','meeting.id')
                                            ->where('meeting_users.type', 'mentee')
                                            ->where('meeting.id',$meeting_id)
                                            ->first();
                if($meeting_data->schedule_time < date('Y-m-d H:i:s')){
                    $is_datetime_valid = false;
                }

                $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$meeting_id)->first();

            }
            $mentee_list = DB::table('mentee')
                            ->join('assign_mentee', 'assign_mentee.mentee_id', 'mentee.id')
                            ->join('school', 'school.id', 'mentee.school_id')
                            ->leftJoin('student_status', 'student_status.id', 'mentee.status')
                            ->select('mentee.id','mentee.timezone','mentee.firebase_id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email', 'mentee.current_living_details','mentee.image','mentee.cell_phone_number','mentee.school_id','school.name as school_name')
                            ->where('assign_mentee.assigned_by', Auth::user()->id )
                            ->where('student_status.view_in_application', 1)
                            // ->where('assign_mentee.is_primary',1)
                            ->orderBy('assign_mentee.created_date','desc')
                            ->get()->toarray();


            $session_method_location = DB::table('session_method_location')->select('*')->where('status',1)->get()->toarray();

            return view('mentor.add-meeting')->with('meeting_data',$meeting_data)->with('is_datetime_valid',$is_datetime_valid)->with('mentee_list',$mentee_list)->with('meeting_requests',$meeting_requests)->with('session_method_location',$session_method_location);
        } catch ( \DecryptException $e){
            return redirect(route('mentor.meeting.index'));
        }

    }


    public function save(Request $request)
    {
        // $fcmApiKey = $this->fcmApiKey;
        $id = Input::get('id');
        $title = Input::get('title');
        $description = Input::get('description');
        $mentee_ids = Input::get('mentee_ids');
        $schedule_time = Input::get('schedule_time');
        $school_id = Input::get('school_id');
        $school_location = Input::get('school_location');
        $school_type = Input::get('school_type');
        $session_method_location_id = Input::get('session_method_location_id');

        if(empty($description)){
            $description = "";
        }
        if(empty($school_location)){
            $school_location = "";
        }
        if(empty($school_id)){
            $school_id = 0;
        }

        if(empty($title)){
            $title = "Mentor Session";
        }

        if(empty($school_type)){
            $school_type = "";
        }



        if(!empty($school_id)){
            $school_data = DB::table('school')->where('id', $school_id)->first();
            $address = !empty($school_data->address)?$school_data->address:'';
            $latitude = !empty($school_data->latitude)?$school_data->latitude:'';
            $longitude = !empty($school_data->longitude)?$school_data->longitude:'';
        }else{
            $address = "";
            $latitude = "";
            $longitude = "";
        }

        $schedule_time = str_replace("-", "/", $schedule_time);
        $schedule_time = date("Y-m-d H:i:s", strtotime($schedule_time));

        // echo $schedule_time; die;

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }


        if(empty($id)){
            $id = DB::table('meeting')->insertGetId(['title'=>$title,'description'=>$description,'agency_id'=>Auth::user()->assigned_by,'created_by'=>Auth::user()->id,'created_by_type'=>'mentor','schedule_time'=>$schedule_time,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude,'school_id'=>$school_id,'school_type'=>$school_type,'school_location'=>$school_location , 'session_method_location_id' => $session_method_location_id , 'created_date' => $created_date , 'mentor_id' => Auth::user()->id , 'mentee_id' => $mentee_ids , 'created_from' => 'web_portal' ]);


            DB::table('meeting_users')->insert(['meeting_id'=>$id,'type'=>'mentor','user_id'=> Auth::user()->id ]);

            if(!empty($mentee_ids)){
                DB::table('meeting_users')->insert(['meeting_id'=>$id,'type'=>'mentee','user_id'=>$mentee_ids]);
            }
            DB::table('meeting_status')->insert(['meeting_id'=>$id]);

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'new_session','user_type'=>'mentee','user_id'=>$mentee_ids,'notification_response'=>'','created_at'=>$created_date]);
            $notification_response = meeting_event_notification('mentee',$mentee_ids,$id,'create');
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

//            dd($notification_response);

            session(['success_message'=>"Session created successfully"]);
        }else{
            $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$id)->where('status', 0)->first();

            if(!empty($meeting_requests)){
                DB::table('meeting')->where('id',$id)->update(['schedule_time'=>$schedule_time]);
                DB::table('meeting_requests')->where('id',$meeting_requests->id)->update(['status' => 1]);
                DB::table('meeting_status')->where('meeting_id',$id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);

                DB::table('meeting_users')->where('meeting_id',$id)->where('type', 'mentee')->update(['status' => 1]);
                session(['success_message'=>"Session recsheduled successfully"]);

            }else{
                DB::table('meeting')->where('id', $id)->update(['title'=>$title,'description'=>$description,'schedule_time'=>$schedule_time,'school_id'=>$school_id,'school_location'=>$school_location , 'session_method_location_id' => $session_method_location_id,'school_type'=>$school_type]);

                $mentee = DB::table('meeting_users')->where('meeting_id',$id)->where('type','mentee')->first();
                $mentee_id = $mentee->user_id;
                DB::table('meeting_users')->where('id',$mentee->id)->update(['user_id'=>$mentee_ids]);

                $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'update_session','user_type'=>'mentee','user_id'=>$mentee_ids,'notification_response'=>'','created_at'=>$created_date]);
                $notification_response = meeting_event_notification('mentee',$mentee_ids,$id,'update');
                DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

                session(['success_message'=>"Session updated successfully"]);

            }
        }

        return redirect('/mentor/meeting/list?type=requested&view=calendar');
    }


    public function get_session_data_ajax(Request $request)
    {
        $id = $request->id;
        // $session_data = DB::table('meeting')->select('meeting.*','school.name AS school_name')->leftJoin('school','school.id','meeting.school_id')->where('meeting.id',$id)->first();

        $session_data = DB::table('meeting_users')->select('meeting_users.*','mentee.firstname','mentee.middlename','mentee.lastname','mentee.school_id','school.name AS school_name','meeting.school_type')->join('mentee', 'mentee.id', 'meeting_users.user_id')->join('school','school.id','mentee.school_id')->join('meeting','meeting.id','meeting_users.meeting_id')->where('meeting_users.meeting_id', $id)->where('meeting_users.type', 'mentee')->get()->toarray();

        $school_name = $session_data[0]->school_name;
        $school_id = $session_data[0]->school_id;

        $meeting_data = DB::table('meeting')->select('school_id')->where('id',$id)->first();
        if(!empty($meeting_data->school_id)){
            $is_school = 1;
        }else{
            $is_school = 0;
        }



        return  json_encode(array( 'session_data'=>$session_data , 'school_name'=>$school_name, 'school_id'=>$school_id , 'is_school'=>$is_school)) ;
    }


    public function cancel(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        try{
            $meeting_id = Crypt::decrypt($id);
            DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 3 , 'cancel_date' => date('Y-m-d H:i:s')]);

            $mentee = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->first();
            $mentee_id = $mentee->user_id;

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'delete_session','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);
            $notification_response = meeting_event_notification('mentee',$mentee_id,$id,'delete');
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

            session(['success_message' => "Session cancelled successfully"]);


            $meeting = DB::table('meeting')->find($meeting_id);
            $mentee = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->first();
            $mentee_id = $mentee->user_id;


            return redirect('/mentor/meeting/list?type=requested&view=calendar');

        } catch ( \DecryptException $e ) {
            return redirect('/mentor/meeting/list?type=requested&view=calendar');
        }



    }

    public function delete(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        try{
            $meeting_id = Crypt::decrypt($id);
            //Change status to '4' i.e. for Delete Confirmed Session
            DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 4 , 'cancel_date' => date('Y-m-d H:i:s')]);

            session(['success_message' => "Session deleted successfully"]);


            $meeting = DB::table('meeting')->find($meeting_id);
            $mentee = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->first();
            $mentee_id = $mentee->user_id;

            $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'delete','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);
            $notification_response = meeting_event_notification('mentee',$mentee_id,$meeting_id,'delete');
            DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

            return redirect('/mentor/meeting/list?type=upcoming&view=calendar');

        } catch ( \DecryptException $e ) {
            return redirect('/mentor/meeting/list?type=upcoming&view=calendar');
        }



    }

    public function deny(Request $request)
    {
        /* No reschedule is one type accept the meeting and decline the request */
        $id = !empty($request->id)?$request->id:'';

        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = $affiliate_data->timezone;

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }

        try{
            $meeting_id = Crypt::decrypt($id);
            $meeting_requests = DB::table('meeting_requests')->where('meeting_id',$meeting_id)->where('status', 0)->first();

            $mentee = DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type','mentee')->first();
            $mentee_id = $mentee->user_id;

            if(!empty($meeting_requests)){

                DB::table('meeting_requests')->where('meeting_id',$meeting_id)->update(['status' => 2]);
                DB::table('meeting_status')->where('meeting_id',$meeting_id)->update(['status' => 1 , 'accept_date' => date('Y-m-d H:i:s')]);
                DB::table('meeting_users')->where('meeting_id',$meeting_id)->where('type', 'mentee')->update(['status' => 1 ]);

                $notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$meeting_id,'notification_for'=>'deny','user_type'=>'mentee','user_id'=>$mentee_id,'notification_response'=>'','created_at'=>$created_date]);
                $notification_response = meeting_event_notification('mentee',$mentee_id,$id,'deny_reschedule');
                DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);

                session(['success_message' => "Request has been denied successfully on this session"]);

            }else{
                session(['error_message' => "No pending request found on this meeting"]);
            }

            return redirect('/mentor/meeting/list?type=requested&view=calendar');

        } catch ( \DecryptException $e ) {
            return redirect('/mentor/meeting/list?type=requested&view=calendar');
        }




    }



}
