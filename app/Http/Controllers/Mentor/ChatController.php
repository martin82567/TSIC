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

class ChatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:mentor');

    }


    public function userlist(Request $request)
    {
        $type = !empty($request->type)?$request->type:'mm';
        $data = array();

        if($type == 'st'){

            $data = DB::table('admins')->select('admins.id','admins.name','admins.timezone','admins.email','admins.address','admins.profile_pic','timezones.timezone_offset')->leftJoin('timezones', 'timezones.value', 'admins.timezone')->where('admins.parent_id', Auth::user()->assigned_by)->where('admins.is_active',1)->paginate(10);

            if(!empty($data)){
                foreach($data as $user){
                    $unread_chat_count = DB::table('mentor_staff_chat_threads')->where('receiver_id',Auth::user()->id)->where('sender_id',$user->id)->where('receiver_is_read', 0)->count();
                    $user->unread_chat_count = $unread_chat_count;
                }
            }

        }else if($type == 'mm'){
            $data = DB::table('mentee')
                            ->join('assign_mentee', 'assign_mentee.mentee_id', 'mentee.id')
                            ->join('school', 'school.id', 'mentee.school_id')
                            ->leftJoin('student_status', 'student_status.id', 'mentee.status')
                            ->select('mentee.id','mentee.timezone','mentee.firebase_id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email', 'mentee.current_living_details','mentee.image','mentee.cell_phone_number','mentee.school_id','mentee.platform_status','school.name as school_name')
                            ->where('assign_mentee.assigned_by',Auth::user()->id)
                            ->where('student_status.view_in_application', 1)
                            ->where('mentee.platform_status', 1)
                            ->orderBy('assign_mentee.created_date','desc')
                            ->paginate(10);

            if(!empty($data)){
                foreach($data as $user){
                    $unread_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id',Auth::user()->id)->where('sender_id',$user->id)->where('receiver_is_read', 0)->count();

                    $user->unread_chat_count = $unread_chat_count;
                }
            }
        }

        // echo '<pre>'; print_r($data); die;

        return view('mentor.chat-user-list')->with('data',$data)->with('type',$type);
    }

    public function get_staff_chatcode(Request $request)
    {
        try{
            $mentor_id = Auth::user()->id;
            $staff_id = Crypt::decrypt($request->staff_id);

            $is_exist = DB::table('mentor_staff_chat_codes')->where('mentor_id',$mentor_id)->where('staff_id',$staff_id)->first();

            if(!empty($is_exist)){
                $code = $is_exist->code;
            }else{
                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char.rand(10000000,99999999);
                DB::table('mentor_staff_chat_codes')->insert(['code'=>$code,'mentor_id'=>$mentor_id,'staff_id'=>$staff_id]);
            }

            DB::table('mentor_staff_chat_threads')->where('from_where','staff')->where('sender_id',$staff_id)->where('receiver_id',$mentor_id)->update(['receiver_is_read'=>1]);

            return redirect('/mentor/chat/message?type=st&code='.$code);



        }catch(\Exception $e){
            return redirect('/mentor/chat/userlist?type=st');
        }
    }

    public function get_mentee_chatcode(Request $request)
    {
        try{
            $mentee_id = Crypt::decrypt($request->mentee_id);
            $mentor_id = Auth::user()->id;

            $is_exist = DB::table('mentor_mentee_chat_codes')->where('mentee_id',$mentee_id)->where('mentor_id',$mentor_id)->first();

            if(!empty($is_exist)){
                $code = $is_exist->code;
            }else{
                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char.rand(10000000,99999999);
                DB::table('mentor_mentee_chat_codes')->insert(['code'=>$code,'mentee_id'=>$mentee_id,'mentor_id'=>$mentor_id]);
            }

            DB::table('mentor_mentee_chat_threads')->where('from_where','mentee')->where('sender_id',$mentee_id)->where('receiver_id',$mentor_id)->update(['receiver_is_read'=>1]);

            return redirect('/mentor/chat/message?type=mm&code='.$code);



        }catch(\Exception $e){
            return redirect('/mentor/chat/userlist?type=mm');
        }
    }

    public function message(Request $request)
    {
        $type = !empty($request->type)?$request->type:'mm';
        $code = !empty($request->code)?$request->code:'';
        $affiliate_data = DB::table('admins')->select('*')->where('id', Auth::user()->assigned_by)->first();
        $timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';
        $chat_details_arr = array();

        if($type == 'st'){
            $mentor_staff_chat_codes = DB::table('mentor_staff_chat_codes AS mscc')->where('code',$code)->first();

            if(empty($mentor_staff_chat_codes)){
                return redirect('/mentor/chat/userlist?type=st');
            }

            $channel_sid = $mentor_staff_chat_codes->channel_sid;

            $chat_details_arr = DB::table('mentor_staff_chat_threads AS msct')->select('msct.*')->where('msct.chat_code',$code)->orderBy('msct.id','asc')->get()->toarray();

            $staff_id = $mentor_staff_chat_codes->staff_id;
            $staff_data = DB::table('admins')->select('*')->where('id',$staff_id)->first();
            $sender_name = $staff_data->name;
            $sender_id = $staff_id;

            $from_where = "mentor";
            $socket_chat_type = "staff";

            $chat_type = "mentor_staff";
            $receiver_type = "staff";
        }else if($type == 'mm'){

            $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('code',$code)->first();


            if(empty($mentor_mentee_chat_codes)){
                return redirect('/mentor/chat/userlist?type=mm');
            }

            $channel_sid = $mentor_mentee_chat_codes->channel_sid;

            $chat_details_arr = DB::table('mentor_mentee_chat_threads AS mmct')->select('mmct.*')->where('mmct.chat_code',$code)->orderBy('mmct.id','asc')->get()->toarray();

            $mentee_id = $mentor_mentee_chat_codes->mentee_id;
            $mentee_data = DB::table('mentee')->select('*')->where('id',$mentee_id)->first();
            $sender_name = $mentee_data->firstname.' '.$mentee_data->middlename.' '.$mentee_data->lastname;
            $sender_id = $mentee_id;

            $from_where = "mentor";
            $socket_chat_type = "";

            $chat_type = "mentor_mentee";
            $receiver_type = "mentee";

        }

        $mentor_name = Auth::user()->firstname.' '.Auth::user()->lastname;

        return view('mentor.chat-message')->with('type',$type)->with('code',$code)->with('chat_details_arr',$chat_details_arr)->with('sender_id',$sender_id)->with('sender_name',$sender_name)->with('timezone',$timezone)->with('mentor_id', Auth::user()->id)->with('mentor_name',$mentor_name)->with('from_where',$from_where)->with('socket_chat_type',$socket_chat_type)->with('channel_sid',$channel_sid)->with('chat_type',$chat_type)->with('receiver_type',$receiver_type);

    }



}
