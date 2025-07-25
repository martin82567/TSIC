<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Response;

class FaqController extends Controller
{
    //
    protected $user_id;

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

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    }

    public function index(Request $request)
    {
    	# code...
    	$settings = DB::table('settings')->find(1);
    	$mentee_faq = !empty($settings->mentee_faq)?$settings->mentee_faq:'';
    	return response()->json(['status'=>true, 'message' => 'Here is your data', 'data' => array('mentee_faq' => $mentee_faq)]);
    }


}
