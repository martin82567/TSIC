<?php

namespace App\Http\Controllers\Auth\Mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MentorLoginControllerBackup extends Controller
{

    public function __construct()
    {
      $this->middleware('guest:mentor', ['except' => ['logout']]);
    }

    public function showLoginForm()
    {
      $today = date('Y-m-d H:i:s');
      // echo $today; die;
      $system_messaging = DB::table(SYSTEM_MESSAGING.' AS msg')->select('msg.id','msg.message','msg.start_datetime','msg.end_datetime','appids.app_id','msg.created_by')->whereRaw("msg.start_datetime <= '".$today."' AND msg.end_datetime >= '".$today."'");
      $system_messaging = $system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS.' AS appids',function ($join) {
                $join->on('appids.message_id', '=' , 'msg.id') ;
                $join->where('appids.app_id', '=' , 2) ;
            });
      $system_messaging = $system_messaging->where('appids.app_id', 2);
      $system_messaging = $system_messaging->where('msg.is_expired',0);
      $system_messaging = $system_messaging->where('msg.created_by',1);
      $system_messaging = $system_messaging->orderBy('msg.id','desc');
      $system_messaging = $system_messaging->get()->toarray();

      $waiver_statement = DB::table('waiver_statement')->where('status', 1)->first();
      return view('auth.Mentor.login',compact('system_messaging','waiver_statement'));
    }

    public function showForgetPassword()
    {
      return view('auth.Mentor.forget-password');
    }

    public function submit_forget_password(Request $request)
    {
      $email = !empty($request->email)?$request->email:'';
      if(!empty($email)){
        $user = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

        // $user =DB::table('mentor')->where('email', '=', $email)->where('is_active', '=', 1)->first();

        if (!empty($user)) {
          $activation_code = rand(100000000, 999999999);
          DB::table('mentor')->where('id', $user->id)->update(['activation_code' => $activation_code]);
          $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
          $to = $email;
          $subject = 'TSIC Mentor forgot password';
          email_send($to,$subject,$content);
          session(['message' => 'A new OTP sent to your email address','msg_class'=>'success']);
          return redirect('/mentor/reset-password'.'/'.urlencode($email));
        }else{

          session(['message' => 'Please give correct credentials.','msg_class'=>'danger']);
          return redirect('/mentor/forget-password');
        }
      }else{
        session(['message' => 'Please give email id','msg_class'=>'danger']);
        return redirect('/mentor/forget-password');
      }

    }

    public function showResetPassword($email)
    {
      return view('auth.Mentor.reset-password')->with('email',$email);
    }

    public function submit_reset_password(Request $request)
    {
      $email = !empty($request->email)?$request->email:'';
      $activation_code = !empty($request->activation_code)?$request->activation_code:'';
      $password = !empty($request->password)?$request->password:'';

      $user_obj = DB::table('mentor')->select('mentor.*','mentor_status.view_in_application')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', '=', $email)->where('mentor.activation_code', '=', $activation_code)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status',1)->first();

      if (!empty($user_obj)) {
          DB::table('mentor')->where('id', $user_obj->id)->update(['password' => Hash::make($password) , 'activation_code' => '']);

          session(['message' => 'Password changed successfully.','msg_class'=>'success']);
          return redirect('/mentor/login');

      }else{

          session(['message' => 'Please give correct data.','msg_class'=>'danger']);
          return redirect('/mentor/reset-password'.'/'.urlencode($email));
      }
    }

    public function login(Request $request)
    {
      date_default_timezone_set('America/New_York');
      $datetime = date('Y-m-d H:i:s');

		  // Validate the form data
		  $this->validate($request, [
									'email'   => 'required|email',
                  'password' => 'required|min:6',
									'waiver_statement_id' => 'required'
								]);

      $check_user = DB::table('mentor')->select('mentor.id','mentor.email','mentor.password','mentor.is_active','mentor_status.view_in_application','mentor.platform_status','mentor.assigned_by')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor.email', $request->email)->where('mentor.platform_status',1)->first();

    	if(!empty($check_user)){
      	if(!empty($check_user->view_in_application)){
        		if (Hash::check($request->password, $check_user->password)) {

              /* Check affiliate */

              $check_aff = DB::table('admins')->where('id', $check_user->assigned_by)->first();

              if(empty($check_aff->is_active)){
                return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('email' => 'You affiliate is inactive now. Please talk to admin to login.'));
              }

        			if (Auth::guard('mentor')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

                DB::table('waiver_acceptance')->where('user_type','mentor')->where('login_from', 'web')->where('user_id',$check_user->id)->where('status', 1)->update(['status'=>0]); /* Previous made not signed*/

                DB::table('waiver_acceptance')->insert([
                                                        'waiver_statement_id'=>$request->waiver_statement_id,
                                                        'user_type'=>'mentor',
                                                        'user_id'=>$check_user->id,
                                                        'status'=>1,
                                                        'login_from'=>'web',
                                                        'created_at'=>$datetime,
                                                        'updated_at'=>$datetime
                                                    ]);  /* New signed entry */



    	    			return redirect()->intended(route('mentor.dashboard'));
    	    		}

        		}else{
          		return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('password' => 'Wrong password'));
        		}
      	}else{
        		return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('email' => 'Inactive user'));
      	}

    	}else{
      	return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('email' => 'No user found'));
    	}

    }

    public function logout()
    {

        // echo Auth::guard('mentor')->user()->id; die;

        // DB::table('waiver_acceptance')->where('user_type','mentor')->where('login_from', 'web')->where('user_id', Auth::guard('mentor')->user()->id)->where('status', 1)->update(['status'=>0]); /* Previous made not signed*/


        Auth::guard('mentor')->logout();

        return redirect('/mentor/login');
    }
}
