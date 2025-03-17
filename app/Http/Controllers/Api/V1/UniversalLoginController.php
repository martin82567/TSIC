<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UniversalLoginController extends Controller
{
    public function check_user(Request $request){
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

        if(empty($request->email)){
            return response()->json(['status'=>false, 'message' => "Please give email", 'data' => $data ]);
        }

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return response()->json(['status'=>false, 'message' => "Invalid email", 'data' => $data ]);
        }

        if($mentor && !empty($mentor)) {
            return response()->json(['status'=>true, 'message' => "", 'data' => array('email' => $request->email, 'user_type' => 'mentor')]);
        } elseif($mentee && !empty($mentee)) {
            return response()->json(['status'=>true, 'message' => "", 'data' => array('email' => $request->email, 'user_type' => 'mentee')]);
        }

        $data = array(
            'email' => null,
            'user_type' => null
        );
        return response()->json(['status'=>false, 'message' => "This email does not exist. Please validate your email address with your local Take Stock in Children program and try again.", 'data' => $data]);
    }
    public function login(Request $request) {
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();
        $data = array(
            'token' => "",
            'user_details' => null
        );

        if(empty($request->email)){
            return response()->json(['status'=>false, 'message' => "Please give email", 'data' => $data ]);
        }

        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            return response()->json(['status'=>false, 'message' => "Invalid email", 'data' => $data ]);
        }

        if(empty($request->password)){
            return response()->json(['status'=>false, 'message' => "Please give password", 'data' => $data ]);
        }

        if(empty($request->waiver_statement_id)){
            return response()->json(['status'=>false, 'message' => "Waiver statement id is required", 'data' => $data ]);
        }

        $check_waiver_exists = DB::table('waiver_statement')->where('status',1)->where('id',$request->waiver_statement_id)->first();

        if(empty($check_waiver_exists)){
            return response()->json(['status'=>false, 'message' => "No active waiver found", 'data' => $data ]);
        }

        if($mentor && !empty($mentor)) {
            return $this->mentorLogin($request);
        }

        if($mentee && !empty($mentee)) {
            return $this->menteeLogin($request);
        }

        $data = array(
            'token' => "",
            'user_details' => null
        );
        return response()->json(['status'=>false, 'message' => "This email does not exist. Please validate your email address with your local Take Stock in Children program and try again.", 'data' => $data]);
    }

    public function forgot_password(Request $request) {
        $input = $request->all();
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

        if(empty($input['email'])){
            return response()->json(['status'=>false, 'message' => "Please provide your email address to begin the reset password process."]);
        }

        if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL)){
            return response()->json(['status'=>false, 'message' => "The email adress does not exist in the Take Stock App. Please validate your email address with your local Take Stock in Children program and try again."]);
        }

        $email = $input['email'];

        if($mentor && !empty($mentor)) {
            return $this->mentorForgotPassword($email);
        }
        if($mentee && !empty($mentee)) {
            return $this->menteeForgotPassword($email);
        }

        $data = array(
            'token' => "",
            'user_details' => null
        );

        return response()->json(['status'=>false, 'message' => "Please give correct credentials.", 'data' => array()]);
    }

    public function reset_password(Request $request){
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

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

        if($mentor && !empty($mentor)) {
            return $this->mentorResetPassword($request);
        }

        if($mentee && !empty($mentee)) {
            return $this->menteeResetPassword($request);
        }

        return response()->json(['status'=>false, 'message' => "Please give correct data.", 'data' => array()]);
    }

    private function menteeLogin($request)
    {

        date_default_timezone_set('America/New_York');
        $datetime = date('Y-m-d H:i:s');

        $input = $request->all();

        $email = !empty($input['email'])?$input['email']:'';
        $password = !empty($input['password'])?$input['password']:'';
        $latitude = !empty($input['latitude'])?$input['latitude']:'';
        $longitude = !empty($input['longitude'])?$input['longitude']:'';
        $firebase_id = !empty($input['firebase_id'])?$input['firebase_id']:'';
        $device_type = !empty($input['device_type'])?$input['device_type']:'';
        $voip_device_token = !empty($input['voip_device_token'])?$input['voip_device_token']:'';
        $waiver_statement_id = !empty($input['waiver_statement_id'])?$input['waiver_statement_id']:0;

        $user_obj =DB::table('mentee')->select('mentee.*','student_status.view_in_application')->leftJoin('student_status','student_status.id','mentee.status')->where('mentee.email', '=', $email)->where('student_status.view_in_application', '=', 1)->where('mentee.platform_status',1)->first();

        //print_r($user_obj->toarray());

        $data = array(
            'token' => "",
            'user_details' => null
        );

        if (!empty($user_obj)) {
            if (Hash::check($password, $user_obj->password)) {

                /* Check acive affiliate */

                $check_aff = DB::table('admins')->where('id', $user_obj->assigned_by)->first();

                if(empty($check_aff->is_active)){
                    $data = array(
                        'token' => "",
                        'user_details' => null
                    );

                    return response()->json(['status'=>false, 'message' => 'Your affiliate is inactive now.Please talk to admin to login.', 'data' => $data]);
                }

                DB::table('mentee')->where('id', $user_obj->id)->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'firebase_id' => $firebase_id,
                    'voip_device_token' => $voip_device_token,
                    'device_type' => $device_type,
                    'is_logged_out' => 0
                ]);

                DB::table('waiver_acceptance')->where('user_type','mentee')->where('login_from','app')->where('user_id',$user_obj->id)->where('status', 1)->update(['status'=>0]); /* Previous made not signed*/

                DB::table('waiver_acceptance')->insert([
                    'waiver_statement_id'=>$waiver_statement_id,
                    'user_type'=>'mentee',
                    'user_id'=>$user_obj->id,
                    'status'=>1,
                    'created_at'=>$datetime,
                    'updated_at'=>$datetime
                ]); /* New signed entry */



                $id = $user_obj->id;

                $user_obj =DB::table('mentee')
                    ->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.current_living_details','mentee.image','mentee.voip_device_token','admins.firstname as linked_agency_firstname','admins.middlename as linked_agency_middlename','admins.lastname as linked_agency_lastname','admins.name as linked_agency_name','admins.profile_pic as linked_agency_image')
                    // ->leftjoin('assign_victim', 'assign_victim.victim_id', '=', 'mentee.id')
                    ->leftjoin('admins', 'mentee.assigned_by', '=', 'admins.id')
                    ->where('mentee.id', '=', $id)
                    ->first();

                $token = Crypt::encryptString($user_obj->id);
                // $submited_tips_token = uniqid().'_'.$user_obj->appuser_id.'_'.uniqid();

                $goal_achived_count = DB::table('goaltask')
                    ->leftjoin('assign_goal', 'assign_goal.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_goal.id as assign_id','assign_goal.status as datastatus','assign_goal.begin_time','assign_goal.complated_time','assign_goal.note')
                    ->where('assign_goal.victim_id',$user_obj->id)
                    ->where('assign_goal.status',2)
                    ->count();
                $user_obj->goal_achived = $goal_achived_count;
                $user_obj->linked_agency_image = !empty($user_obj->linked_agency_image)? config('app.aws_url').'agency_pic/'.$user_obj->linked_agency_image :'';
                // $user_obj->linked_agency_image = !empty($user_obj->linked_agency_image)?url('/public/uploads/agency_pic').'/'.$user_obj->linked_agency_image:'';

                $data = array(
                    'token' => "Bearer ".$token,
                    // 'submited_tips_token' => "",
                    'user_details' => $user_obj,
                    'user_type' => 'mentee'
                );

                return response()->json(['status'=>true, 'message' => 'You are successfully logged in', 'data' => $data]);
            }else{
                $user_obj =DB::table('mentee')
                    ->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.current_living_details','mentee.image','mentee.voip_device_token','admins.firstname as linked_agency_firstname','admins.middlename as linked_agency_middlename','admins.lastname as linked_agency_lastname','admins.name as linked_agency_name','admins.profile_pic as linked_agency_image')
                    // ->leftjoin('assign_victim', 'assign_victim.victim_id', '=', 'mentee.id')
                    ->leftjoin('admins', 'mentee.assigned_by', '=', 'admins.id')
                    ->where('mentee.id', '=', 0)
                    ->first();
                $data = array(
                    'token' => "",
                    // 'submited_tips_token' => "",
                    'user_details' => $user_obj,
                    'user_type' => 'mentee'
                );

                return response()->json(['status'=>false, 'message' => 'You have entered an incorrect password. If you do not know your password you may use the "forgot password" feature below.', 'data' => $data]);
            }
        }else{
            $user_obj =DB::table('mentee')
                ->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.current_living_details','mentee.image','mentee.voip_device_token','admins.firstname as linked_agency_firstname','admins.middlename as linked_agency_middlename','admins.lastname as linked_agency_lastname','admins.name as linked_agency_name','admins.profile_pic as linked_agency_image')
                // ->leftjoin('assign_victim', 'assign_victim.victim_id', '=', 'mentee.id')
                ->leftjoin('admins', 'mentee.assigned_by', '=', 'admins.id')
                ->where('mentee.id', '=', 0)
                ->first();
            $data = array(
                'token' => "",
                // 'submited_tips_token' => "",
                'user_details' => $user_obj,
                'user_type' => 'mentee'
            );
            return response()->json(['status'=>false, 'message' => "This email does not exist. Please validate your email address with your local Take Stock in Children program and try again.", 'data' => $data]);
        }

    }

    private function mentorLogin($request)
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


        $user_obj = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        //print_r($user_obj->toarray());

        $data = array(
            'token' => "",
            'user_details' => null
        );


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
                    'user_details' => $user_obj,
                    'user_type' => 'mentor'
                );

                return response()->json(['status'=>true, 'message' => 'You are successfully logged in', 'data' => $data]);
            }else{


                return response()->json(['status'=>false, 'message' => 'You have entered an incorrect password. If you do not know your password you may use the "forgot password" feature below.' , 'data' => (object) array()]);
            }
        }else{

            return response()->json(['status'=>false, 'message' => "This email does not exist. Please put a valid email" , 'data' => $data ]);
        }

    }

    private function menteeForgotPassword($email){
        $user = DB::table('mentee')->select('mentee.*','student_status.view_in_application')->leftJoin('student_status','student_status.id','mentee.status')->where('mentee.email', '=', $email)->where('student_status.view_in_application', '=', 1)->where('mentee.platform_status',1)->first();

        if (!empty($user)) {
            $activation_code = rand(100000000, 999999999);
            DB::table('mentee')->where('id', $user->id)->update(['activation_code' => $activation_code]);
            $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
            $headers = "From: no_reply@seeandsend.com";
            $to = $email;
            $subject = 'TSIC Mentee forgot password';
            email_send($to,$subject,$content);
            // mail($to,$subject,$content,$headers);
            return response()->json(['status'=>true, 'message' => "Proceed for next step.", 'data' => array('user_type' => 'mentee')]);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct credentials.", 'data' => array()]);
        }
    }

    private function mentorForgotPassword($email){
        $user = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        if (!empty($user)) {
            $activation_code = rand(100000000, 999999999);
            DB::table('mentor')->where('id', $user->id)->update(['activation_code' => $activation_code]);
            $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
            $headers = "From: no_reply@seeandsend.com";
            $to = $email;
            $subject = 'TSIC Mentor forgot password';
            email_send($to,$subject,$content);
            // mail($to,$subject,$content,$headers);
            return response()->json(['status'=>true, 'message' => "Proceed for next step.", 'data' => array('user_type' => 'mentor')]);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct credentials.", 'data' => array()]);
        }
    }

    private function menteeResetPassword($request){
        $email = $request->email;
        $password = $request->password;
        $otp = $request->otp;

        $user_obj = DB::table('mentee')->select('mentee.*','student_status.view_in_application')->leftJoin('student_status','student_status.id','mentee.status')->where('mentee.email', '=', $email)->where('mentee.activation_code', '=', $otp)->where('student_status.view_in_application', '=', 1)->where('mentee.platform_status',1)->first();
        // $user_obj =DB::table('mentee')->where('email', '=', $email)->where('activation_code', '=', $otp)->where('status', '=', 1)->first();

        if (!empty($user_obj)) {
            DB::table('mentee')->where('id', $user_obj->id)->update(['password' => Hash::make($password) , 'activation_code' => '0']);
            return response()->json(['status'=>true, 'message' => 'Password changed successfully.', 'data' => array('user_type' => 'mentee')]);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct data.", 'data' => array()]);
        }
    }

    private function mentorResetPassword($request){
        $email = $request->email;
        $password = $request->password;
        $otp = $request->otp;

        $user_obj = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor.activation_code', '=', $otp)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        if (!empty($user_obj)) {
            DB::table('mentor')->where('id', $user_obj->id)->update(['password' => Hash::make($password) , 'activation_code' => '']);
            return response()->json(['status'=>true, 'message' => 'Password changed successfully.', 'data' => array('user_type' => 'mentor')]);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give correct data.", 'data' => array()]);
        }
    }
}
