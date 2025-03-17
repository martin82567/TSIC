<?php

/*Mentor Chats*/

namespace App\Http\Controllers\Api\V1\mentor;

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
/**++++++++++++++++++++++++++++++++++++++++++++++**/
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
    public function mentee(Request $request)
    {

        $affiliate_data = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftJoin('timezones','timezones.value','admins.timezone')->where('admins.id',$this->assigned_by)->first();
        $timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';
        $timezone_offset = !empty($affiliate_data->timezone_offset)?$affiliate_data->timezone_offset:'-5';

        
    	$mentor_id = $this->user_id;
    	$mentee_id = !empty($request->mentee_id)?$request->mentee_id:'';
        $take = !empty($request->take)?$request->take:'';
        $page = !empty($request->page)?$request->page:0;

    	if(empty($mentee_id))
    		return response()->json(['status'=>false,'message'=>"Please mention mentee",'data'=>array()]);

    	if(!is_numeric($mentee_id))
    		return response()->json(['status'=>false,'message'=>"Mentee value should be numeric",'data'=>array()]);

    	$is_check = DB::table('mentor_mentee_chat_codes')->where('mentor_id',$mentor_id)->where('mentee_id',$mentee_id)->first();

        $mentee_data = DB::table('mentee')->select('*')->where('id',$mentee_id)->first();
        $mentee_firebase_id = !empty($mentee_data->firebase_id)?$mentee_data->firebase_id:'';
        $mentee_device_type = !empty($mentee_data->device_type)?$mentee_data->device_type:'';

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

            DB::table('mentor_mentee_chat_threads')->where('from_where','mentee')->where('chat_code',$chat_code_no)->update(['receiver_is_read'=>1]);
    	}

        $count_message = DB::table('mentor_mentee_chat_threads AS mmct')->where('mmct.chat_code',$chat_code_no)->count();

    	return response()->json(['status'=>true,'message'=>"Here is your data",'data'=>array('chat_code'=>$chat_code_no,'firebase_id'=>$mentee_firebase_id,'device_type'=> $mentee_device_type,'timezone'=>$timezone,'timezone_offset'=>$timezone_offset,'count_message'=>$count_message,'threads'=>$data)]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function staff(Request $request)
    {

        $affiliate_data = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftJoin('timezones','timezones.value','admins.timezone')->where('admins.id',$this->assigned_by)->first();
        $timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';
        $timezone_offset = !empty($affiliate_data->timezone_offset)?$affiliate_data->timezone_offset:'-5';

    	$mentor_id = $this->user_id;
    	$staff_id = !empty($request->staff_id)?$request->staff_id:'';
        $take = !empty($request->take)?$request->take:'';
        $page = !empty($request->page)?$request->page:0;

    	if(empty($staff_id))
    		return response()->json(['status'=>false,'message'=>"Please mention staff",'data'=>array()]);

    	if(!is_numeric($staff_id))
    		return response()->json(['status'=>false,'message'=>"Staff value should be numeric",'data'=>array()]);

    	$is_check = DB::table('mentor_staff_chat_codes')->where('mentor_id',$mentor_id)->where('staff_id',$staff_id)->first();

    	if(!empty($is_check)){

    		$chat_code_no = $is_check->code;

    	}else{
    		$characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
            shuffle($characters);
            $rand_char = '';
            foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
            $chat_code_no = $rand_char.rand(10000000,99999999);

            DB::table('mentor_staff_chat_codes')->insert(['code'=>$chat_code_no,'mentor_id'=>$mentor_id,'staff_id'=>$staff_id]);

    	}

        if(!empty($take)){
            $skip = ($take*$page);            

            $data = DB::table('mentor_staff_chat_threads AS msct')->where('msct.chat_code',$chat_code_no)->orderBy('msct.id','desc')->skip($skip)->take($take)->get()->toarray();
        }else{
            
            $data = DB::table('mentor_staff_chat_threads AS msct')->where('msct.chat_code',$chat_code_no)->orderBy('msct.id','asc')->get()->toarray();
        }

    	

        if(!empty($data)){
            foreach($data as $d){
                $d->created_date = date('m-d-Y H:i:s', strtotime($d->created_date));
                $d->stripped_message = !empty($d->stripped_message)?$d->stripped_message:'';
            }

            DB::table('mentor_staff_chat_threads')->where('from_where','staff')->where('chat_code',$chat_code_no)->update(['receiver_is_read'=>1]);
        }



    	return response()->json(['status'=>true,'message'=>"Here is your data",'data'=>array('chat_code'=>$chat_code_no,'timezone'=>$timezone,'timezone_offset'=>$timezone_offset,'threads'=>$data)]);


    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
}