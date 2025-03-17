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

class MentorController extends Controller
{

    protected $assigned_by;

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

            mentor_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }
        
    }

/**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function menteelist()
    {
        $user_details = DB::table('mentee')
                            ->join('assign_mentee', 'assign_mentee.mentee_id', 'mentee.id')
                            ->join('school', 'school.id', 'mentee.school_id')
                            ->leftJoin('student_status', 'student_status.id', 'mentee.status')
                            ->select('mentee.id','mentee.timezone','mentee.firebase_id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email', 'mentee.current_living_details','mentee.image','mentee.cell_phone_number','mentee.school_id','mentee.is_chat_video','mentee.platform_status','school.name as school_name')
                            ->where('assign_mentee.assigned_by',$this->user_id)
                            ->where('student_status.view_in_application', 1)
                            ->where('mentee.platform_status', 1)
                            ->orderBy('assign_mentee.created_date','desc')
                            ->get();
        if(!empty($user_details)){
            foreach($user_details as $user){
                $last_session_date = '';
                $session = DB::table('session')->select('schedule_date')->where('mentee_id',$user->id)->where('mentor_id',$this->user_id)->orderby('id','desc')->first();
                if(!empty($session)){
                    $last_session_date = date('m-d-Y', strtotime($session->schedule_date));
                }
                $user->last_session_date = $last_session_date;
                
                $upcoming_meeting_date = '';
                $upcoming_meeting = DB::table('meeting')
                                            ->join('meeting_users', 'meeting.id', 'meeting_users.meeting_id')
                                            ->join('meeting_status', 'meeting.id', 'meeting_status.meeting_id')
                                            ->select('meeting.*')
                                            ->where('meeting.created_by',$this->user_id)
                                            ->where('meeting_users.type','mentee')
                                            ->where('meeting_users.user_id',$user->id)
                                            ->where('meeting_status.status',1)
                                            ->where('meeting.schedule_time','>=',date('Y-m-d'))
                                            ->orderby('meeting.schedule_time','asc')
                                            ->first();
                                        
                //echo '<pre>';print_r($upcoming_meeting);
                if(!empty($upcoming_meeting->schedule_time)){
                    $upcoming_meeting_date = date('m-d-Y', strtotime($upcoming_meeting->schedule_time));
                }
                $user->upcoming_meeting_date = $upcoming_meeting_date;
                
                
                $unread_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id',$this->user_id)->where('sender_id',$user->id)->where('receiver_is_read', 0)->count();
                
                $user->unread_chat_count = $unread_chat_count;

                $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('mentor_id', $this->user_id )->where('mentee_id',$user->id)->first();

                $channel_sid = "";

                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char.rand(10000000,99999999);

                if(!empty($mentor_mentee_chat_codes)){
                    $channel_sid = !empty($mentor_mentee_chat_codes->channel_sid)?$mentor_mentee_chat_codes->channel_sid:"";

                    $code = !empty($mentor_mentee_chat_codes->code)?$mentor_mentee_chat_codes->code:"";                  
                }else{
                    DB::table('mentor_mentee_chat_codes')->insert(['code'=>$code,'mentor_id'=>$this->user_id,'mentee_id'=>$user->id]); 
                }

                $user->channel_sid = $channel_sid;
                $user->code = $code;
            }
        } //die;
        return response()->json(['status'=>true, 'message' => "Mentee List.", 'data' => $user_details->toarray() ]);
    }

/**++++++++++++++++++++++++++++++++++++++++++++++**/   
    public function get_mentee_reports(Request $request)
    {
        $data = array();
        $mentee_id = !empty($request->mentee_id)?$request->mentee_id:'';

        if(empty($mentee_id))
            return response()->json(['status'=>false,'message'=>"Mentee is required",'data'=>array()]);

        if(!is_numeric($mentee_id))
            return response()->json(['status'=>false,'message'=>"Mentee id should be numeric",'data'=>array()]);

        //$data = DB::table('report')->where('mentee_id',$mentee_id)->orderBy('id','desc')->get()->toarray();

        return response()->json(['status'=>true, 'message' => "Report List.", 'data' => $data]);

    }    
/**++++++++++++++++++++++++++++++++++++++++++++++**/  
    public function get_school($value='')
    {        
        $assigned_by = $this->assigned_by;

        $data = array();
        $data = DB::table('school')->where('agency_id', $assigned_by)->where('status',1)->orderBy('name','asc')->get()->toarray();

        return response()->json(['status'=>true, 'message' => "School List.", 'data' => $data]);
    }     
/**++++++++++++++++++++++++++++++++++++++++++++++**/      
    public function search_learning(Request $request){
        
        $mentor = DB::table('mentor')->select('*')->where('id',$this->user_id)->first();
        
        if(empty($mentor)){
            return response()->json(['status'=>true, 'message' => '', 'data' => array('e_learning_list' => array())]);
        }
        

        $search_keyword = $request->search_keyword;
        if(empty($search_keyword)){
            $search_keyword = '';
        }

        // $e_learning_arr = DB::table('e_learning')->where('name','like', '%'.$search_keyword.'%')->where('affiliate_id',$mentor->assigned_by)->where('is_active', 1)->get();

        $e_learning_arr = DB::table('e_learning AS e')->select('e.id','e.name','e.description','e.type','e.file','e.url','a.affiliate_id')->leftJoin('e_learning_affiliates AS a','a.e_learning_id','e.id')->leftJoin('e_learning_users AS u','u.e_learning_id','e.id')->where('e.name','like', '%'.$search_keyword.'%')->where('e.is_active', 1)->where('u.user_type', 1)->where('a.affiliate_id', $mentor->assigned_by)->get();
        
        return response()->json(['status'=>true, 'message' => '', 'data' => array('e_learning_list' => $e_learning_arr)]);
    }
}