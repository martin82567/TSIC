<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use DB;
use Crypt;
class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        /*TODAY'S MENTORING INTERACTIONS*/
            $mentor_interaction = array();

            // $today = date('m-d-Y');

            if(Auth::user()->type == 1){
                $mentor_interaction = DB::table('session')->select('session.*','mentee.firstname AS mentee_firstname','mentee.middlename AS mentee_middlename','mentee.lastname AS mentee_lastname','mentor.firstname AS mentor_firstname','mentor.middlename AS mentor_middlename','mentor.lastname AS mentor_lastname')->leftJoin('mentee','mentee.id','session.mentee_id')->leftJoin('mentor','mentor.id','session.mentor_id')->where('session.schedule_date', '=', date('Y-m-d') )->orderBy('session.schedule_date','desc')->take(10)->get()->toarray();
            }else if(Auth::user()->type == 2){
                $mentors = $mentor_ids =  array();
                $mentors = DB::table('mentor')->where('assigned_by', Auth::user()->id)->get()->toarray();
                if(!empty($mentors)){
                    foreach($mentors as $m){
                        $mentor_ids[] = $m->id;
                    }
                }
                // print_r($mentor_ids); die;
                if(!empty($mentor_ids)){
                    $mentor_interaction = DB::table('session')->select('session.*','mentee.firstname AS mentee_firstname','mentee.middlename AS mentee_middlename','mentee.lastname AS mentee_lastname','mentor.firstname AS mentor_firstname','mentor.middlename AS mentor_middlename','mentor.lastname AS mentor_lastname')->leftJoin('mentee','mentee.id','session.mentee_id')->leftJoin('mentor','mentor.id','session.mentor_id')->where('session.schedule_date', '=', date('Y-m-d') )->whereIn('session.mentor_id', $mentor_ids)->orderBy('session.schedule_date','desc')->take(10)->get()->toarray();

                }
                
            }else if(Auth::user()->type == 3){

                if(Auth::user()->parent_id != 1){

                    $mentors = $mentor_ids =  array();
                    $mentors = DB::table('mentor')->where('assigned_by', Auth::user()->parent_id)->get()->toarray();
                    if(!empty($mentors)){
                        foreach($mentors as $m){
                            $mentor_ids[] = $m->id;
                        }
                    }
                    $mentor_interaction = DB::table('session')->select('session.*','mentee.firstname AS mentee_firstname','mentee.middlename AS mentee_middlename','mentee.lastname AS mentee_lastname','mentor.firstname AS mentor_firstname','mentor.middlename AS mentor_middlename','mentor.lastname AS mentor_lastname')->leftJoin('mentee','mentee.id','session.mentee_id')->leftJoin('mentor','mentor.id','session.mentor_id')->where('session.schedule_date', '=', date('Y-m-d') )->whereIn('session.mentor_id', $mentor_ids)->orderBy('session.schedule_date','desc')->take(10)->get()->toarray();

                }else{

                    $mentor_interaction = DB::table('session')->select('session.*','mentee.firstname AS mentee_firstname','mentee.middlename AS mentee_middlename','mentee.lastname AS mentee_lastname','mentor.firstname AS mentor_firstname','mentor.middlename AS mentor_middlename','mentor.lastname AS mentor_lastname')->leftJoin('mentee','mentee.id','session.mentee_id')->leftJoin('mentor','mentor.id','session.mentor_id')->where('session.schedule_date', '=', date('Y-m-d') )->orderBy('session.schedule_date','desc')->take(10)->get()->toarray();
                }
            }


        /*Goal Leadboard*/

            if(Auth::user()->type == 1){

                $mentee = array();
                $goal_leadboard = DB::table('assign_goal')->select('assign_goal.*',
                DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_goal.goaltask_id)  as goaltask_name" ),
                DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_firstname" ),
                DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_middlename" ),
                DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_lastname" )
                        )->join('goaltask', 'goaltask.id', 'assign_goal.goaltask_id')->where('assign_goal.status',2)->orderBy('assign_goal.point','desc')->take(10)->get()->toarray();


            }else if(Auth::user()->type == 2){

                $mentee = array();
                $admin_victim = DB::table('mentee')->select('mentee.id')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->where('mentee.assigned_by', Auth::user()->id)->get()->toarray();
                

                if(!empty($admin_victim)){
                    foreach($admin_victim as $av){
                        $mentee[] = $av->id;
                    }
                }

                $goal_leadboard = DB::table('assign_goal')->select('assign_goal.*',
                DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_goal.goaltask_id)  as goaltask_name" ),
                DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_firstname" ),
                DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_middlename" ),
                DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_lastname" )
                            )
                ->join('goaltask', 'goaltask.id', 'assign_goal.goaltask_id')
                ->whereIn('assign_goal.victim_id',$mentee)
                ->where('assign_goal.status',2)
                ->orderBy('assign_goal.point','desc')
                ->take(10)
                ->get()->toarray();

            }else if(Auth::user()->type == 3){

                $parent_id = Auth::user()->parent_id;

                $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
                if($admin_parent->type == 1){

                    $mentee = array();
                    $goal_leadboard = DB::table('assign_goal')->select('assign_goal.*',
                    DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_goal.goaltask_id)  as goaltask_name" ),
                    DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_firstname" ),
                    DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_middlename" ),
                    DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_lastname" )
                            )->join('goaltask', 'goaltask.id', 'assign_goal.goaltask_id')->where('assign_goal.status',2)->orderBy('assign_goal.point','desc')->take(10)->get()->toarray();

                }else{
                    $mentee = array();
                    $admin_victim = DB::table('mentee')->select('mentee.id')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->where('mentee.assigned_by', $parent_id)->get()->toarray();

                    if(!empty($admin_victim)){
                        foreach($admin_victim as $av){
                            $mentee[] = $av->id;
                        }
                    }

                $goal_leadboard = DB::table('assign_goal')->select('assign_goal.*',
                DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_goal.goaltask_id)  as goaltask_name" ),
                DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_firstname" ),
                DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_middlename" ),
                DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_goal.victim_id) as victim_lastname" )
                            )->join('goaltask', 'goaltask.id', 'assign_goal.goaltask_id')->whereIn('assign_goal.victim_id',$mentee)->where('assign_goal.status',2)->orderBy('assign_goal.point','desc')->take(10)->get()->toarray();

                }

                

            }

        /*Task Leadboard*/

            if(Auth::user()->type == 1){

                $mentee = array();
                $task_leadboard = DB::table('assign_task')->select('assign_task.*',
                DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_task.goaltask_id)  as goaltask_name" ),
                DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_firstname" ),
                DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_middlename" ),
                DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_lastname" )
                        )->join('goaltask', 'goaltask.id', 'assign_task.goaltask_id')->where('assign_task.status',2)->orderBy('assign_task.point','desc')->take(10)->get()->toarray();


            }else if(Auth::user()->type == 2){

                $mentee = array();
                $admin_victim = DB::table('mentee')->select('mentee.id')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->where('mentee.assigned_by', Auth::user()->id)->get()->toarray();

                if(!empty($admin_victim)){
                    foreach($admin_victim as $av){
                        $mentee[] = $av->id;
                    }
                }

                $task_leadboard = DB::table('assign_task')->select('assign_task.*',
                DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_task.goaltask_id)  as goaltask_name" ),
                DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_firstname" ),
                DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_middlename" ),
                DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_lastname" )
                            )->join('goaltask', 'goaltask.id', 'assign_task.goaltask_id')->whereIn('assign_task.victim_id',$mentee)->where('assign_task.status',2)->orderBy('assign_task.point','desc')->take(10)->get()->toarray();
                
            }else if(Auth::user()->type == 3){



                $parent_id = Auth::user()->parent_id;

                $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
                if($admin_parent->type == 1){

                    $mentee = array();
                    $task_leadboard = DB::table('assign_task')->select('assign_task.*',
                    DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_task.goaltask_id)  as goaltask_name" ),
                    DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_firstname" ),
                    DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_middlename" ),
                    DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_lastname" )
                            )->join('goaltask', 'goaltask.id', 'assign_task.goaltask_id')->where('assign_task.status',2)->orderBy('assign_task.point','desc')->take(10)->get()->toarray();

                }else{

                        $mentee = array();
                        $admin_victim = DB::table('mentee')->select('mentee.id')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->where('mentee.assigned_by', $parent_id)->get()->toarray();

                        if(!empty($admin_victim)){
                            foreach($admin_victim as $av){
                                $mentee[] = $av->id;
                            }
                        }

                        $task_leadboard = DB::table('assign_task')->select('assign_task.*',
                        DB::raw("(SELECT goaltask.name FROM goaltask WHERE goaltask.id = assign_task.goaltask_id)  as goaltask_name" ),
                        DB::raw("(SELECT mentee.firstname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_firstname" ),
                        DB::raw("(SELECT mentee.middlename FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_middlename" ),
                        DB::raw("(SELECT mentee.lastname FROM mentee WHERE mentee.id = assign_task.victim_id) as victim_lastname" )
                                    )->join('goaltask', 'goaltask.id', 'assign_task.goaltask_id')->whereIn('assign_task.victim_id',$mentee)->where('assign_task.status',2)->orderBy('assign_task.point','desc')->take(10)->get()->toarray();

                }

            }
        
        /*Session(Meeting) Logs*/

            $session_type = !empty($request->session_type)?$request->session_type:'';
            $timezone = !empty(Auth::user()->timezone)?Auth::user()->timezone:'America/New_York';
            date_default_timezone_set($timezone);
            $today = date('Y-m-d H:i:s');
            $today_start = date('Y-m-d');
            $last_week = date('Y-m-d', strtotime("-1 week"));
            $last_month = date('Y-m-d', strtotime("-1 month"));
            
            if($session_type == 'today'){            
                $session_duration = " session.schedule_date BETWEEN '".$today_start."' AND '".$today."' ";
            }else if($session_type == 'lastweek'){            
                $session_duration = " session.schedule_date BETWEEN '".$last_week."' AND '".$today."' ";
            }else if($session_type == 'lastmonth'){
                $session_duration = " session.schedule_date BETWEEN '".$last_month."' AND '".$today."' ";
            }else{
                $session_duration = " session.schedule_date BETWEEN '".$today_start."' AND '".$today."' ";
            }        
            $session_log = array();

            if(Auth::user()->type == 1){
                $session_log = DB::table('session')
                                        ->select('session.*','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname')                                        
                                        ->leftJoin('mentor','mentor.id','session.mentor_id')
                                        ->leftJoin('mentee','mentee.id','session.mentee_id')
                                        ->where('session.status',1)
                                        ->whereRaw($session_duration)
                                        ->get()->toarray();

            }else if(Auth::user()->type == 2){
                $mentor_ids = array();
                $mentors = DB::table('mentor')->select('id','assigned_by')->where('assigned_by', Auth::user()->id)->get()->toarray();
                if(!empty($mentors)){
                    foreach($mentors as $m){
                        $mentor_ids[] = $m->id;
                    }
                }
                if(!empty($mentor_ids)){
                    $session_log = DB::table('session')
                                            ->select('session.*','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname')                                        
                                            ->leftJoin('mentor','mentor.id','session.mentor_id')
                                            ->leftJoin('mentee','mentee.id','session.mentee_id')
                                            ->where('session.status',1)
                                            ->whereIn('session.mentor_id', $mentor_ids)
                                            ->whereRaw($session_duration)
                                            ->get()->toarray();
                }
                 

            }else if(Auth::user()->type == 3){
                $parent_id = Auth::user()->parent_id;
                $age = 0;
                $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
                if($admin_parent->type == 1){
                    $age = 1;
                }else{
                    $age = 0;
                    $mentor_ids = array();
                    $mentors = DB::table('mentor')->select('id','assigned_by')->where('assigned_by', $parent_id)->get()->toarray();
                    if(!empty($mentors)){
                        foreach($mentors as $m){
                            $mentor_ids[] = $m->id;
                        }
                    }
                }

                // echo '<pre>'; echo $parent_id;
                // echo '<pre>'; echo $age;
                // die;

                if(!empty($age)){
                    $session_log = DB::table('session')
                                        ->select('session.*','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname')                                        
                                        ->leftJoin('mentor','mentor.id','session.mentor_id')
                                        ->leftJoin('mentee','mentee.id','session.mentee_id')
                                        ->where('session.status',1)
                                        ->whereRaw($session_duration)
                                        ->get()->toarray();
                }else{
                    $session_log = DB::table('session')
                                        ->select('session.*','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname')                                        
                                        ->leftJoin('mentor','mentor.id','session.mentor_id')
                                        ->leftJoin('mentee','mentee.id','session.mentee_id')
                                        ->where('session.status',1)
                                        ->whereIn('session.mentor_id', $mentor_ids)
                                        ->whereRaw($session_duration)
                                        ->get()->toarray();
                }
                
            }
        /*Mentor-Mentee Chat*/

            $chat_time = !empty($request->chat_time)?$request->chat_time:'';

            $chat_data = array();

            if($chat_time == 'today'){
                $chat_duration = " mentor_mentee_chat_threads.created_date BETWEEN '".$today_start."' AND '".$today."'  ";
            }else if($chat_time == 'lastweek'){
                $chat_duration = " mentor_mentee_chat_threads.created_date BETWEEN '".$last_week."' AND '".$today."'  ";
            }else if($chat_time == 'lastmonth'){
                $chat_duration = " mentor_mentee_chat_threads.created_date BETWEEN '".$last_month."' AND '".$today."'  ";
            }else{
                $chat_duration = " mentor_mentee_chat_threads.created_date BETWEEN '".$today_start."' AND '".$today."'  ";
            }

            if(Auth::user()->type == 1){
                $chat_data = DB::table('mentor_mentee_chat_threads')->whereRaw($chat_duration)->orderBy('mentor_mentee_chat_threads.id','desc')->get()->toarray();

            } else if(Auth::user()->type == 2){

                $chat_codes = array();

                if(!empty($mentor_ids)){
                    $chat_codes = DB::table('mentor_mentee_chat_codes')->whereIn('mentor_id',$mentor_ids)->get()->toarray(); 
                }
                
                if(!empty($chat_codes)){
                    foreach($chat_codes as $codes){
                        $code_arr[] = $codes->code;
                    }
                } 

                if(!empty($code_arr)){
                    $chat_data = DB::table('mentor_mentee_chat_threads')->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->whereRaw($chat_duration)->orderBy('mentor_mentee_chat_threads.id','desc')->get()->toarray();
                }
            } else if(Auth::user()->type == 3){
                if(!empty($age)){
                    $chat_data = DB::table('mentor_mentee_chat_threads')->whereRaw($chat_duration)->orderBy('mentor_mentee_chat_threads.id','desc')->get()->toarray();
                }else{
                    $chat_codes = array();

                    if(!empty($mentor_ids)){
                        $chat_codes = DB::table('mentor_mentee_chat_codes')->whereIn('mentor_id',$mentor_ids)->get()->toarray(); 
                    }
                    
                    if(!empty($chat_codes)){
                        foreach($chat_codes as $codes){
                            $code_arr[] = $codes->code;
                        }
                    } 

                    if(!empty($code_arr)){
                        $chat_data = DB::table('mentor_mentee_chat_threads')->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->whereRaw($chat_duration)->orderBy('mentor_mentee_chat_threads.id','desc')->get()->toarray();
                    }

                }
            } 

        /*Mentor-Mentee Chat Count*/

            $chat_count = array();
                
            $chat_count['today'] = DB::table('mentor_mentee_chat_threads')->whereRaw(" mentor_mentee_chat_threads.created_date BETWEEN '".$today_start."' AND '".$today."'  ");
            $chat_count['lastweek'] = DB::table('mentor_mentee_chat_threads')->whereRaw(" mentor_mentee_chat_threads.created_date BETWEEN '".$last_week."' AND '".$today."'  ");
            $chat_count['lastmonth'] = DB::table('mentor_mentee_chat_threads')->whereRaw(" mentor_mentee_chat_threads.created_date BETWEEN '".$last_month."' AND '".$today."'  ");

            $now_month = date('m');
            $settings = DB::table('settings')->first();
            $session_start_month = $settings->session_start_month;
            
            if($now_month <= $session_start_month){            
                $date_year = (date('Y') - 1);
                $last_year = date($date_year.'-'.$session_start_month.'-01');
                $chat_count['lastyear'] = DB::table('mentor_mentee_chat_threads')->whereRaw(" mentor_mentee_chat_threads.created_date BETWEEN '".$last_year."' AND '".$today."'  ");
                
            }else{
                $date_year = date('Y');
                $last_year = date($date_year.'-'.$session_start_month.'-01');
                $chat_count['lastyear'] = DB::table('mentor_mentee_chat_threads')->whereRaw(" mentor_mentee_chat_threads.created_date BETWEEN '".$last_year."' AND '".$today."'  ");
            }

            if(Auth::user()->type == 1){
                
                $chat_count['today'] = $chat_count['today']->count();
                $chat_count['lastweek'] = $chat_count['lastweek']->count();
                $chat_count['lastmonth'] = $chat_count['lastmonth']->count();
                $chat_count['lastyear'] = $chat_count['lastyear']->count();
                

            } else if(Auth::user()->type == 2){

                $chat_codes = array();

                if(!empty($mentor_ids)){
                    $chat_codes = DB::table('mentor_mentee_chat_codes')->whereIn('mentor_id',$mentor_ids)->get()->toarray(); 
                }
                
                if(!empty($chat_codes)){
                    foreach($chat_codes as $codes){
                        $code_arr[] = $codes->code;
                    }
                } 

                if(!empty($code_arr)){
                    $chat_count['today'] = $chat_count['today']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                    $chat_count['lastweek'] = $chat_count['lastweek']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                    $chat_count['lastmonth'] = $chat_count['lastmonth']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                    $chat_count['lastyear'] = $chat_count['lastyear']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                    
                }else{
                    $chat_count['today'] = 0;
                    $chat_count['lastweek'] = 0;
                    $chat_count['lastmonth'] = 0;
                    $chat_count['lastyear'] = 0;
                }
            } else if(Auth::user()->type == 3){
                if(!empty($age)){
                    $chat_count = DB::table('mentor_mentee_chat_threads')->count();
                }else{
                    $chat_codes = array();

                    if(!empty($mentor_ids)){
                        $chat_codes = DB::table('mentor_mentee_chat_codes')->whereIn('mentor_id',$mentor_ids)->get()->toarray(); 
                    }
                    
                    if(!empty($chat_codes)){
                        foreach($chat_codes as $codes){
                            $code_arr[] = $codes->code;
                        }
                    } 

                    if(!empty($code_arr)){                        
                        $chat_count['today'] = $chat_count['today']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                        $chat_count['lastweek'] = $chat_count['lastweek']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                        $chat_count['lastmonth'] = $chat_count['lastmonth']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                        $chat_count['lastyear'] = $chat_count['lastyear']->whereIn('mentor_mentee_chat_threads.chat_code',$code_arr)->count();
                    }else{
                        $chat_count['today'] = 0;
                        $chat_count['lastweek'] = 0;
                        $chat_count['lastmonth'] = 0;
                        $chat_count['lastyear'] = 0;
                    }

                }
            } 

        /****/            

        return view('admin.home')->with('mentor_interaction',$mentor_interaction)->with('goal_leadboard',$goal_leadboard)->with('task_leadboard',$task_leadboard)->with('session_type',$session_type)->with('session_log',$session_log)->with('chat_count',$chat_count)->with('chat_data',$chat_data);
    }

/********************** Settings ********************/
    public function settings()
    {
        $settings = DB::table('settings')->first();
        return view('admin.settings')->with('settings',$settings);
    }

    public function save_settings(Request $request)
    {

        date_default_timezone_set('America/New_York');
        $datetime = date('Y-m-d H:i:s');

        $settings = DB::table('settings')->find(1);

        $site_name = !empty($request->site_name)?$request->site_name:'';
        $site_email = !empty($request->site_email)?$request->site_email:'';
        $site_phone_no = !empty($request->site_phone_no)?$request->site_phone_no:'';        
        $video_chat_duration = !empty($request->video_chat_duration)?$request->video_chat_duration:'';
        $video_chat_start_time = !empty($request->video_chat_start_time)?$request->video_chat_start_time:'';
        $video_chat_end_time = !empty($request->video_chat_end_time)?$request->video_chat_end_time:'';
        $twilio_account_sid = !empty($request->twilio_account_sid)?$request->twilio_account_sid:'';
        $twilio_auth_token = !empty($request->twilio_auth_token)?$request->twilio_auth_token:'';
        $twilio_apiKeySid = !empty($request->twilio_apiKeySid)?$request->twilio_apiKeySid:'';
        $twilio_apiKeySecret = !empty($request->twilio_apiKeySecret)?$request->twilio_apiKeySecret:'';
        $mentor_faq = !empty($request->mentor_faq)?$request->mentor_faq:'';
        $mentee_faq = !empty($request->mentee_faq)?$request->mentee_faq:'';
        $waiver_statement = !empty($request->waiver_statement)?$request->waiver_statement:'';
        $waiver_url = !empty($request->waiver_url)?$request->waiver_url:'';
        $is_waiver_reset = !empty($request->is_waiver_reset)?$request->is_waiver_reset:'';

        DB::table('settings')->where('id', 1)->update([
                                                        'site_name'=>$site_name, 
                                                        'site_email'=>$site_email, 
                                                        'site_phone_no'=>$site_phone_no, 
                                                        'video_chat_duration'=>$video_chat_duration,
                                                        'video_chat_start_time'=>$video_chat_start_time,
                                                        'video_chat_end_time'=>$video_chat_end_time,
                                                        'twilio_account_sid'=>$twilio_account_sid,
                                                        'twilio_auth_token'=>$twilio_auth_token,
                                                        'twilio_apiKeySid'=>$twilio_apiKeySid,
                                                        'twilio_apiKeySecret'=>$twilio_apiKeySecret,
                                                        'mentor_faq'=>$mentor_faq,
                                                        'mentee_faq'=>$mentee_faq,
                                                        'waiver_statement'=>$waiver_statement,
                                                        'waiver_url'=>$waiver_url,
                                                        'updated_at'=>$datetime
                                                    ]);

        /* Check Waiver */

        $success_message = "Settings updated successfully";

        if(trim($waiver_statement) != $settings->waiver_statement || trim($waiver_url) != $settings->waiver_url){

            DB::table('waiver_statement')->update(['status'=>0,'updated_at'=>$datetime]);
            DB::table('waiver_statement')->insert(['statement'=>$waiver_statement,'url'=>$waiver_url,'status'=>1,'created_at'=>$datetime,'updated_at'=>$datetime]);

            DB::table('mentor')->update(['is_logged_out'=>1]);
            DB::table('mentee')->update(['is_logged_out'=>1]);

            // Auth::guard('mentor')->logout();
            // Auth::guard('mentee')->logout();

            DB::table('waiver_acceptance')->update(['status'=>2,'updated_at'=>$datetime]); /* Made all signed to inactive */

            if(!empty($is_waiver_reset)){
            	$success_message = "Waiver settings updated successfully";
            }
        }



        session(['success_message'=> $success_message]);
        return redirect('/admin/settings');
    }



}