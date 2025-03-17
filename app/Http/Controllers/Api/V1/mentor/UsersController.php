<?php


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
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{

    public function testlogin(Request $request)
    {
        return response()->json(['status' => true, 'message' => "hit"]);
    }

    public function login(Request $request)
    {

        date_default_timezone_set('America/New_York');
        $datetime = date('Y-m-d H:i:s');

    	$input = $request->all();

        $email = !empty($input['email'])?$input['email']:'';
        $password = !empty($input['password'])?$input['password']:'';
        $latitude = !empty($input['latitude'])?$input['latitude']:'';
        $longitude = !empty($input['longitude'])?$input['longitude']:'';
        $firebase_id = !empty($input['firebase_id'])?$input['firebase_id']:'';
        $voip_device_token = !empty($input['voip_device_token'])?$input['voip_device_token']:'';
        $device_type = !empty($input['device_type'])?$input['device_type']:'';
        $waiver_statement_id = !empty($input['waiver_statement_id'])?$input['waiver_statement_id']:0;

        $data = array(
                    'token' => "",
                    'user_details' => null
                );
        
        
        if(empty($input['email'])){
            return response()->json(['status'=>false, 'message' => "Please give email", 'data' => $data ]);
        }
        
        if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)){
            return response()->json(['status'=>false, 'message' => "Invalid email", 'data' => $data ]);
        }
        
        if(empty($input['password'])){
            return response()->json(['status'=>false, 'message' => "Please give password", 'data' => $data ]);
        }

        if(empty($waiver_statement_id)){
            return response()->json(['status'=>false, 'message' => "Waiver statement id is required", 'data' => $data ]);
        }

        $check_waiver_exists = DB::table('waiver_statement')->where('status',1)->where('id',$waiver_statement_id)->first();

        if(empty($check_waiver_exists)){
            return response()->json(['status'=>false, 'message' => "No active waiver found", 'data' => $data ]);
        }

        
        
        $user_obj = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        //print_r($user_obj->toarray());

        
        
        if (!empty($user_obj)) {
            if (Hash::check($password, $user_obj->password)) {

                /* Check acive affiliate */

                $check_aff = DB::table('admins')->where('id', $user_obj->assigned_by)->first();

                if(empty($check_aff->is_active)){
                    

                    return response()->json(['status'=>false, 'message' => 'Your affiliate is inactive now.Please talk to admin to login.', 'data' => $data]);
                }


                DB::table('mentor')->where('id', $user_obj->id)->update([
                                                                    'firebase_id' => $firebase_id, 
                                                                    'voip_device_token' => $voip_device_token , 
                                                                    'device_type' => $device_type,
                                                                    'is_logged_out' => 0
                                                                ]);

                DB::table('waiver_acceptance')->where('user_type','mentor')->where('login_from', 'app')->where('user_id',$user_obj->id)->where('status', 1)->update(['status'=>0]); /* Previous made not signed*/

                DB::table('waiver_acceptance')->insert([
                                                        'waiver_statement_id'=>$waiver_statement_id,
                                                        'user_type'=>'mentor',
                                                        'user_id'=>$user_obj->id,
                                                        'status'=>1,
                                                        'created_at'=>$datetime,
                                                        'updated_at'=>$datetime
                                                    ]);  /* New signed entry */
                

                $id = $user_obj->id;

                $user_obj =DB::table('mentor')
                        ->select('mentor.id','mentor.firstname','mentor.middlename','mentor.lastname','mentor.email','mentor.firebase_id','mentor.device_type','mentor.is_chat_staff','mentor.is_chat_mentee','mentor.image AS profile_pic','mentor.phone','mentor.assigned_by','admins.name AS agency_name','admins.profile_pic AS linked_agency_image')
                        ->join('admins', 'admins.id', 'mentor.assigned_by','mentor.voip_device_token')
                        ->where('mentor.id', '=', $id)
                        ->first();

                // echo '<pre>'; print_r($user_obj); die;

                $token = Crypt::encryptString($user_obj->id);
                
                $data = array(
                                'token' => "Bearer ".$token,
                                'user_details' => $user_obj
                            );

                return response()->json(['status'=>true, 'message' => 'You are successfully logged in', 'data' => $data]);
            }else{
                

                return response()->json(['status'=>false, 'message' => 'You have entered an incorrect password. If you do not know your password you may use the "forgot password" feature below.' , 'data' => (object) array()]);
            }
        }else{
            
            return response()->json(['status'=>false, 'message' => "This email does not exist. Please put a valid email" , 'data' => $data ]);
        }

    }

    

    public function forgot_password(Request $request){
        $input = $request->all();
        if(empty($input['email'])){
            return response()->json(['status'=>false, 'message' => "Please provide your email address to begin the reset password process."]);
        }
        if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)){
            return response()->json(['status'=>false, 'message' => "The email adress does not exist in the Take Stock App. Please validate your email address with your local Take Stock in Children program and try again."]);
        }
        $email = $input['email'];
        $user = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        // $user =DB::table('mentor')->where('email', '=', $email)->where('is_active', '=', 1)->first();
        
        if (!empty($user)) {
                $activation_code = rand(100000000, 999999999);
                DB::table('mentor')->where('id', $user->id)->update(['activation_code' => $activation_code]);
                $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
                $headers = "From: no_reply@seeandsend.com";
                $to = $email;
                $subject = 'TSIC Mentor forgot password';
                email_send($to,$subject,$content);
                // mail($to,$subject,$content,$headers);
                return response()->json(['status'=>true, 'message' => "Proceed for next step.", 'data' => array()]);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct credentials.", 'data' => array()]);
        }
    }
    
    public function reset_password(Request $request){
        $input = $request->all();
        if(empty($input['password'])){
            return response()->json(['status'=>false, 'message' => "Please give password", 'data' => array()]);
        }
        if(empty($input['email'])){
            return response()->json(['status'=>false, 'message' => "Please give email", 'data' => array()]);
        }
        if(empty($input['otp'])){
            return response()->json(['status'=>false, 'message' => "Please give OTP", 'data' => array()]);
        }

        $email = $input['email'];
        $password = $input['password'];
        $otp = $input['otp'];
        
        $user_obj = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor.activation_code', '=', $otp)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        // $user_obj =DB::table('mentor')->where('email', '=', $email)->where('activation_code', '=', $otp)->where('is_active', '=', 1)->first();

        if (!empty($user_obj)) {
            DB::table('mentor')->where('id', $user_obj->id)->update(['password' => Hash::make($password) , 'activation_code' => '']);
            return response()->json(['status'=>true, 'message' => 'Password changed successfully.']);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct data.", 'data' => array()]);
        }
    }

    
}