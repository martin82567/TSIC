<?php

/*Mentee Chats*/

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Hash;
use Artisan;
use Config;
use Auth;
use DB;
use DateTime;

class ChatsController extends Controller
{
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

            $this->assigned_by = $chk_user->assigned_by;

            mentee_last_activity($this->user_id);
            
            // $assign_mentee = DB::table('assign_mentee')->where('mentee_id',$this->user_id)->first();

            // if(!empty($assign_mentee)){
            //     $this->assigned_by = $assign_mentee->assigned_by;
            // }else{
            //     $this->assigned_by = 0;
            // }

            

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    } 

    public function index(Request $request)
    {
        $mentee_id = $this->user_id;
        $assigned_by = $this->assigned_by;
        $mentor_id = !empty($request->mentor_id)?$request->mentor_id:'';
        $take = !empty($request->take)?$request->take:'';
        $page = !empty($request->page)?$request->page:0;
        
        if(empty($mentor_id))
            return response()->json(['status'=>false,'message'=>"Mentor id is required",'data'=>array()]);

        
        $chk_mentor_exist = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.view_in_application','mentor.is_active')->where('mentor.id',$mentor_id)->first();


        if(empty($chk_mentor_exist)){
            return response()->json(['status'=>false,'message'=>"Mentor does not exist",'data'=>array()]);
        }

        if(empty($chk_mentor_exist->view_in_application)){
            return response()->json(['status'=>false,'message'=>"Mentor is not active.Please talk to app admin",'data'=>array()]);
        }

        if(empty($chk_mentor_exist->platform_status)){
            return response()->json(['status'=>false,'message'=>"Mentor status is deactivated.Please talk to app admin",'data'=>array()]);
        }

        if($chk_mentor_exist->assigned_by != $assigned_by){
            return response()->json(['status'=>false,'message'=>"Unknown mentor",'data'=>array()]);
        }        
        
        $admins = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftJoin('timezones','timezones.value','admins.timezone')->where('admins.id', $assigned_by)->first();

        $timezone = $admins->timezone;
        $timezone_offset = $admins->timezone_offset;

        $mentor_firebase_id = !empty($chk_mentor_exist->firebase_id)?$chk_mentor_exist->firebase_id:'';
        $mentor_device_type = !empty($chk_mentor_exist->device_type)?$chk_mentor_exist->device_type:'';
        
        $is_check = DB::table('mentor_mentee_chat_codes')->where('mentor_id',$mentor_id)->where('mentee_id',$mentee_id)->first();

        if(!empty($is_check)){

            $chat_code_no = $is_check->code;

        }else{
            $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
            shuffle($characters);
            $rand_char = '';
            foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
            $chat_code_no = $rand_char.rand(10000000,99999999);

            DB::table('mentor_mentee_chat_codes')->insert(['code'=>$chat_code_no,'mentor_id'=>$mentor_id,'mentee_id'=>$mentee_id]);

        }

        if(!empty($take)){
            $skip = ($take*$page);
            $data = DB::table('mentor_mentee_chat_threads AS mmct')->where('mmct.chat_code',$chat_code_no)->orderBy('mmct.id','desc')->skip($skip)->take($take)->get()->toarray();
        }else{
            $data = DB::table('mentor_mentee_chat_threads AS mmct')->where('mmct.chat_code',$chat_code_no)->orderBy('mmct.id','asc')->get()->toarray();
        }

        

        if(!empty($data)){
            foreach($data as $d){
                $d->created_date = date('m-d-Y H:i:s', strtotime($d->created_date));
                $d->stripped_message = !empty($d->stripped_message)?$d->stripped_message:'';
            }

            DB::table('mentor_mentee_chat_threads')->where('from_where','mentor')->where('chat_code',$chat_code_no)->update(['receiver_is_read'=>1]);
        }

        $count_message = DB::table('mentor_mentee_chat_threads AS mmct')->where('mmct.chat_code',$chat_code_no)->count();

        return response()->json(['status'=>true,'message'=>"Here is your data",'data'=>array('chat_code'=>$chat_code_no, 'firebase_id' => $mentor_firebase_id , 'device_type' => $mentor_device_type, 'timezone' => $timezone, 'timezone_offset' => $timezone_offset , 'count_message' => $count_message , 'threads'=>$data)]);        
    }

    
    public function get_staff_list()
    {
        $staffs = array();
        $mentee_id = $this->user_id;
        $user_details = DB::table('mentee')->where('id',$mentee_id)->first();
        $assigned_by = $user_details->assigned_by;

        if(!empty($assigned_by)){
            $staffs = DB::table('admins')->select('admins.id','admins.name','admins.email','admins.timezone','admins.profile_pic','admins.latitude','admins.longitude','admins.type','admins.parent_id','timezones.timezone_offset')->leftJoin('timezones', 'timezones.value', 'admins.timezone')->where('admins.parent_id',$assigned_by)->where('admins.type',3)->where('admins.is_active',1)->orderBy('admins.name','asc')->get()->toarray();
        }
        
        if(!empty($staffs)){
            foreach($staffs as $user){

                $mentee_staff_chat_codes = DB::table('mentee_staff_chat_codes')->where('mentee_id', $this->user_id )->where('staff_id',$user->id)->first();

                $channel_sid = "";
                $code = "";
                if(!empty($mentee_staff_chat_codes)){
                    $channel_sid = !empty($mentee_staff_chat_codes->channel_sid)?$mentee_staff_chat_codes->channel_sid:"";
                    $code = !empty($mentee_staff_chat_codes->code)?$mentee_staff_chat_codes->code:"";
                }
                $user->channel_sid = $channel_sid;
                $user->code = $code;

                
                $unread_chat_count = DB::table('mentee_staff_chat_threads')->where('receiver_id',$this->user_id)->where('sender_id',$user->id)->where('receiver_is_read', 0)->count();
                $user->unread_chat_count = $unread_chat_count;
            }
        }

        return response()->json(['status'=>true,'message'=>"Here is your data",'data'=>array('staffs'=>$staffs)]);
    }

    public function staff_chat(Request $request)
    {
        $mentee_id = $this->user_id;
        $staff_id = !empty($request->staff_id)?$request->staff_id:'';
        $take = !empty($request->take)?$request->take:'';
        $page = !empty($request->page)?$request->page:0;

        if(empty($staff_id))
            return response()->json(['status'=>false,'message'=>"Staff is required",'data'=>array()]);


        $admins = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftJoin('timezones','timezones.value','admins.timezone')->where('admins.id', $staff_id)->first();

        $timezone = $admins->timezone;
        $timezone_offset = $admins->timezone_offset;

        
        $is_check = DB::table('mentee_staff_chat_codes')->where('staff_id',$staff_id)->where('mentee_id',$mentee_id)->first();

        if(!empty($is_check)){

            $chat_code_no = $is_check->code;

        }else{
            $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
            shuffle($characters);
            $rand_char = '';
            foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
            $chat_code_no = $rand_char.rand(10000000,99999999);

            DB::table('mentee_staff_chat_codes')->insert(['code'=>$chat_code_no,'staff_id'=>$staff_id,'mentee_id'=>$mentee_id]);

        }

        if(!empty($take)){
            $skip = ($take*$page);
            $data = DB::table('mentee_staff_chat_threads AS msct')->where('msct.chat_code',$chat_code_no)->orderBy('msct.id','desc')->skip($skip)->take($take)->get()->toarray();

            
        }else{
            $data = DB::table('mentee_staff_chat_threads AS msct')->where('msct.chat_code',$chat_code_no)->orderBy('msct.id','asc')->get()->toarray();
        }

        

        if(!empty($data)){
            foreach($data as $d){
                $d->created_date = date('m-d-Y H:i:s', strtotime($d->created_date));
                $d->stripped_message = !empty($d->stripped_message)?$d->stripped_message:'';
            }

            DB::table('mentee_staff_chat_threads')->where('from_where','staff')->where('chat_code',$chat_code_no)->update(['receiver_is_read'=>1]);
        }

        return response()->json(['status'=>true,'message'=>"Here is your data",'data'=>array('chat_code'=>$chat_code_no, 'timezone' => $timezone, 'timezone_offset' => $timezone_offset , 'threads'=>$data)]);
    }     


}