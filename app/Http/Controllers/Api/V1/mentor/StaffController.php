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

class StaffController extends Controller
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
    public function stafflist(Request $request)
    {
        
        $data = array();
        $data = DB::table('admins')->select('admins.id','admins.name','admins.timezone','admins.email','admins.address','admins.profile_pic','timezones.timezone_offset')->leftJoin('timezones', 'timezones.value', 'admins.timezone')->where('admins.parent_id', $this->assigned_by)->where('admins.is_active',1)->get();
        
        if(!empty($data)){
            foreach($data as $user){

                $mentor_staff_chat_codes = DB::table('mentor_staff_chat_codes')->where('mentor_id', $this->user_id )->where('staff_id',$user->id)->first();

                $channel_sid = "";
                $code = "";
                if(!empty($mentor_staff_chat_codes)){
                    $channel_sid = !empty($mentor_staff_chat_codes->channel_sid)?$mentor_staff_chat_codes->channel_sid:"";
                    $code = !empty($mentor_staff_chat_codes->code)?$mentor_staff_chat_codes->code:"";
                }
                $user->channel_sid = $channel_sid;
                $user->code = $code;


                $unread_chat_count = DB::table('mentor_staff_chat_threads')->where('receiver_id',$this->user_id)->where('sender_id',$user->id)->where('receiver_is_read', 0)->count();
                $user->unread_chat_count = $unread_chat_count;
            }
        }

        return response()->json(['status'=>true, 'message' => "Staff List.", 'data' => $data->toarray() ]);
        // echo '<pre>'; print_r($data); die;

    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/

}