<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    //
    public function get_access_token(Request $request)
    {
    	# code...
    	
    	$user_type = !empty($request->user_type)?$request->user_type:'';
    	$user_id = !empty($request->user_id)?$request->user_id:'';
    	$user_name = !empty($request->user_name)?$request->user_name:'';

    	
    	if(empty($user_type)){
    		return Response::json(['status'=>false,'message'=>"User type required", 'data' => array()]);
    	}
    	if(empty($user_id)){
    		return Response::json(['status'=>false,'message'=>"User id required", 'data' => array()]);
    	}
    	if(empty($user_name)){
    		return Response::json(['status'=>false,'message'=>"User id required", 'data' => array()]);
    	}

    	$user_name = str_replace(" ","_",$user_name);
    	$identity = $user_type.'_'.$user_id.'_'.$user_name;
    	$access_token = tw_generate_access_token($user_type,$user_id,$user_name);

    	return Response::json(['token'=>$access_token,'identity'=>$identity]);

    }

    public function channel_id_update(Request $request)
    {
    	$tablename = "mentor_mentee_chat_codes";
    	$chat_type = !empty($request->chat_type)?$request->chat_type:'';
    	$chat_code = !empty($request->chat_code)?$request->chat_code:'';
    	$channel_sid = !empty($request->channel_sid)?$request->channel_sid:'';


    	// if(empty($chat_type)){
    	// 	return Response::json(['status'=>false,'message'=>"Chat type required", 'data' => array()]);
    	// }

    	if(empty($chat_code)){
    		return Response::json(['status'=>false,'message'=>"Chat code required", 'data' => array()]);
    	}

    	if(empty($channel_sid)){
    		return Response::json(['status'=>false,'message'=>"Channel sid required", 'data' => array()]);
    	}

    	if ($chat_type == "mentor_staff") {
	        $tablename = "mentor_staff_chat_codes";
	    }
	    if ($chat_type == "mentee_staff") {
	        $tablename = "mentee_staff_chat_codes";
	    }

	    $check_exists = DB::table($tablename)->where('code',$chat_code)->where('channel_sid', '!=', '')->first();

	    if(!empty($check_exists)){
	    	return Response::json(['status'=>false,'message'=>"A channel sid ".$check_exists->channel_sid." is already there", 'data' => array()]);
	    }

	    DB::table($tablename)->where('code', $chat_code)->update(['channel_sid'=>$channel_sid]);

	    return Response::json(['status'=>true,'message'=>"Channel sid updated successfully",'data'=>array('channel_sid'=>$channel_sid)]);



    }

    public function send_nofication(Request $request)
    {
        # code...
        $user_type = !empty($request->user_type)?$request->user_type:'';
        $user_id = !empty($request->user_id)?$request->user_id:'';
        $message = !empty($request->message)?$request->message:'';
        $sender_name = !empty($request->sender_name)?$request->sender_name:'';
        $comes_from = !empty($request->comes_from)?$request->comes_from:'';

        if(empty($user_type)){
            return Response::json(['status'=>false,'message'=>"Please mention user type",'data'=>array() ]);
        }
        if($user_type != 'mentor' && $user_type != 'mentee'){
            return Response::json(['status'=>false,'message'=>"Unknown user type",'data'=>array() ]);
        }
        if(empty($user_id)){
            return Response::json(['status'=>false,'message'=>"Please mention user id",'data'=>array() ]);
        }
        if(empty($message)){
            return Response::json(['status'=>false,'message'=>"Please mention user type",'data'=>array() ]);
        }
        if(empty($sender_name)){
            return Response::json(['status'=>false,'message'=>"Please mention sender name",'data'=>array() ]);
        }
        if(empty($comes_from)){
            return Response::json(['status'=>false,'message'=>"Please mention comes from",'data'=>array() ]);
        }
        if($comes_from != 'mentor' && $comes_from != 'mentee' && $comes_from != 'staff'){
            return Response::json(['status'=>false,'message'=>"Unknown comes from",'data'=>array() ]);
        }

        $check_user = DB::table($user_type)->find($user_id);
        if(empty($check_user)){
            return Response::json(['status'=>false,'message'=>"No ".$user_type." found ",'data'=>array() ]);
        }

        $device_type = !empty($check_user->device_type)?$check_user->device_type:'';
        $firebase_id = !empty($check_user->firebase_id)?$check_user->firebase_id:'';

        if(!empty($device_type) && !empty($firebase_id)){
            $notification = tw_chat_send_notification($user_type,$user_id,$message,$device_type,$firebase_id,$sender_name,$comes_from);
            return Response($notification);
        }else{
            return Response::json(['status'=>false,'message'=>"Not logged in any device",'data'=>array() ]);
        }
    }
}
