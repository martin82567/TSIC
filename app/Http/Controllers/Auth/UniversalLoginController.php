<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class UniversalLoginController extends Controller
{
    public function showLoginForm()
    {
        $waiver_statement = DB::table('waiver_statement')->where('status', 1)->first();
        return view('auth.login', compact( 'waiver_statement'));
    }

    public function showSystemMessage(){
        $today = date('Y-m-d H:i:s');
        // echo $today; die;
        $system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id', 'msg.created_by')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");
        $system_messaging = $system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
            $join->on('appids.message_id', '=', 'msg.id');
            $join->where('appids.app_id', '=', 2);
        });
        $system_messaging = $system_messaging->where('appids.app_id', 2);
        $system_messaging = $system_messaging->where('msg.is_expired', 0);
        $system_messaging = $system_messaging->where('msg.created_by', 1);
        $system_messaging = $system_messaging->orderBy('msg.id', 'desc');
        $system_messaging = $system_messaging->get()->toarray();
    }

    public function login(Request $request)
    {
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

        date_default_timezone_set('America/New_York');
        $datetime = date('Y-m-d H:i:s');

        $is_waiver_checked = !empty($request->is_waiver_checked) ? $request->is_waiver_checked : 0;
        $waiver_statement_id = !empty($request->waiver_statement_id) ? $request->waiver_statement_id : '';

        // echo $waiver_statement_id; die;

        $waiver_req = '';
        $waiver_req_msg = '';

        if (empty($is_waiver_checked) && empty($waiver_statement_id)) {
            $waiver_req = 'required';
            $waiver_req_msg = 'Please acknowledge waiver statement';
        }

        // Validate the form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'waiver_statement_id' => $waiver_req
        ],
            [
                'email.required' => 'Email is required',
                'email.email' => 'Improper email format',
                'password.required' => 'Password is required',
                'password.min' => 'Password length is minimum six',
                'waiver_statement_id.required' => $waiver_req_msg
            ]
        );

        if($mentor){
            return $this->mentorLogin($request, $is_waiver_checked, $waiver_statement_id, $datetime);
        }
        if($mentee){
            return $this->menteeLogin($request, $is_waiver_checked, $waiver_statement_id, $datetime);
        }

        return redirect()->back()->withInput(Input::all())->withErrors(array('email' => 'No user found'));

    }

    public function showForgetPassword()
    {
        return view('auth.forget-password');
    }

    public function submit_forget_password(Request $request)
    {
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

        if($mentor && !empty($mentor)) {
            return $this->mentorForgotPassword($request);
        }
        if($mentee && !empty($mentee)) {
            return $this->menteeForgotPassword($request);
        }

        session(['message' => 'Please give email id', 'msg_class' => 'danger']);
        return redirect('/forget-password');

    }

    public function showResetPassword($email)
    {
        return view('auth.reset-password')->with('email', $email);
    }

    public function submit_reset_password(Request $request)
    {
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();

        if($mentor && !empty($mentor)) {
            return $this->mentorResetPassword($request);
        }
        if($mentee && !empty($mentee)) {
            return $this->menteeResetPassword($request);
        }

        session(['message' => 'Please give correct data.', 'msg_class' => 'danger']);
        return redirect('/reset-password' . '/' . urlencode($email));
    }

    public function logout()
    {
        if(Auth::guard('mentor')){
            Auth::guard('mentor')->logout();
//            Log::info("mentor is logged out");
        }
        if(Auth::guard('mentee')){
            Auth::guard('mentee')->logout();
//            Log::info("mentee is logged out");
        }

        return redirect('/login');
    }

    public function check_waiver(Request $request)
    {
        $mentee =DB::table('mentee')->select('mentee.email')->where('mentee.email', '=', $request->email)->first();
        $mentor =DB::table('mentor')->select('mentor.email')->where('mentor.email', '=', $request->email)->first();
        $today = date('Y-m-d H:i:s');
        // echo $today; die;
        $system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id', 'msg.created_by')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");

        $email = !empty($request->email) ? $request->email : '';

        $latest_waiver = DB::table('waiver_statement')->where('status', 1)->first();
        $waiver_statement_id = !empty($latest_waiver) ? $latest_waiver->id : 0;

        if (empty($waiver_statement_id)) {
            return ['is_waiver' => false, 'message' => "No statement"];
        }

        if($mentee) {
            $system_messaging = $system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
                $join->on('appids.message_id', '=', 'msg.id');
                $join->where('appids.app_id', '=', 1);
            });
            $system_messaging = $system_messaging->where('appids.app_id', 1);
            $system_messaging = $system_messaging->where('msg.is_expired', 0);
            $system_messaging = $system_messaging->where('msg.created_by', 1);
            $system_messaging = $system_messaging->orderBy('msg.id', 'desc');
            $system_messaging = $system_messaging->get()->toarray();

            $check_mentee = DB::table('mentee')->select('mentee.id', 'mentee.email', 'mentee.password', 'mentee.status', 'student_status.view_in_application', 'mentee.platform_status', 'mentee.assigned_by')->leftJoin('student_status', 'student_status.id', 'mentee.status')->where('mentee.platform_status', 1)->where('mentee.email', $email)->first();
            if (!empty($check_mentee)) {
                if (!empty($waiver_statement_id)) {
                    $check_waiver_acceptance = DB::table('waiver_acceptance')->where('waiver_statement_id', $waiver_statement_id)->where('user_type', 'mentee')->where('login_from', 'web')->where('user_id', $check_mentee->id)->first();

                    if (!empty($check_waiver_acceptance)) {
                        return ['is_waiver' => false, 'message' => "Waiver acknowledged", 'waiver_statement_id' => $waiver_statement_id, 'user_type' => 'mentee', 'system_messaging'=>$system_messaging];
                    } else {
                        return ['is_waiver' => true, 'message' => "Not acknowledged"];
                    }
                }
            }
        }

        if($mentor) {
            $system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id', 'msg.created_by')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");
            $system_messaging = $system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
                $join->on('appids.message_id', '=', 'msg.id');
                $join->where('appids.app_id', '=', 2);
            });
            $system_messaging = $system_messaging->where('appids.app_id', 2);
            $system_messaging = $system_messaging->where('msg.is_expired', 0);
            $system_messaging = $system_messaging->where('msg.created_by', 1);
            $system_messaging = $system_messaging->orderBy('msg.id', 'desc');
            $system_messaging = $system_messaging->get()->toarray();

            $check_mentor = DB::table('mentor')->select('mentor.id', 'mentor.email', 'mentor.password', 'mentor.is_active', 'mentor_status.view_in_application', 'mentor.platform_status', 'mentor.assigned_by')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor.email', $email)->where('mentor.platform_status', 1)->first();
            if (!empty($check_mentor)) {
                if (!empty($waiver_statement_id)) {
                    $check_waiver_acceptance = DB::table('waiver_acceptance')->where('waiver_statement_id', $waiver_statement_id)->where('user_type', 'mentor')->where('login_from', 'web')->where('user_id', $check_mentor->id)->first();

                    if (!empty($check_waiver_acceptance)) {
                        return ['is_waiver' => false, 'message' => "Waiver acknowledged", 'waiver_statement_id' => $waiver_statement_id, 'user_type' => 'mentor', 'system_messaging'=>$system_messaging];
                    } else {
                        return ['is_waiver' => true, 'message' => "Not acknowledged"];
                    }
                }
            }
        }

        return ['is_waiver' => false, 'message' => "Not found"];

    }

    private function mentorLogin($request, $is_waiver_checked, $waiver_statement_id, $datetime){
        $check_user = DB::table('mentor')->select('mentor.id', 'mentor.email', 'mentor.password', 'mentor.is_active', 'mentor_status.view_in_application', 'mentor.platform_status', 'mentor.assigned_by')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor.email', $request->email)->where('mentor.platform_status', 1)->first();

        if (!empty($check_user)) {
            if (!empty($check_user->view_in_application)) {
                if (Hash::check($request->password, $check_user->password)) {

                    /* Check affiliate */

                    $check_aff = DB::table('admins')->where('id', $check_user->assigned_by)->first();

                    if (empty($check_aff->is_active)) {
                        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('email' => 'You affiliate is inactive now. Please talk to admin to login.'));
                    }

                    if (Auth::guard('mentor')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

                        DB::table('waiver_acceptance')->where('user_type', 'mentor')->where('login_from', 'web')->where('user_id', $check_user->id)->where('status', 1)->update(['status' => 0]); /* Previous made not signed*/

                        if (empty($is_waiver_checked) && !empty($waiver_statement_id)) {
                            DB::table('waiver_acceptance')->insert([
                                'waiver_statement_id' => $waiver_statement_id,
                                'user_type' => 'mentor',
                                'user_id' => $check_user->id,
                                'status' => 1,
                                'login_from' => 'web',
                                'created_at' => $datetime,
                                'updated_at' => $datetime
                            ]);  /* New signed entry */
                        }

                        return redirect(route('mentor.dashboard'));
                    }

                } else {
                    return redirect()->back()->withInput(Input::all())->withErrors(array('password' => 'Wrong password'));
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrors(array('email' => 'Inactive user'));
            }
        } else {
            return redirect()->back()->withInput(Input::all())->withErrors(array('email' => 'No user found-mentor'));
        }
    }

    private function menteeLogin($request, $is_waiver_checked, $waiver_statement_id, $datetime){
        $check_user = DB::table('mentee')->select('mentee.id', 'mentee.email', 'mentee.password', 'mentee.status', 'student_status.view_in_application', 'mentee.platform_status', 'mentee.assigned_by')->leftJoin('student_status', 'student_status.id', 'mentee.status')->where('mentee.platform_status', 1)->where('mentee.email', $request->email)->first();

        if (!empty($check_user)) {
            if (!empty($check_user->view_in_application)) {
                if (Hash::check($request->password, $check_user->password)) {

                    /* Check affiliate */

                    $check_aff = DB::table('admins')->where('id', $check_user->assigned_by)->first();

                    if (empty($check_aff->is_active)) {
                        return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(array('email' => 'You affiliate is inactive now. Please talk to admin to login.'));
                    }

                    if (Auth::guard('mentee')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {

                        DB::table('waiver_acceptance')->where('user_type', 'mentee')->where('login_from', 'web')->where('user_id', $check_user->id)->where('status', 1)->update(['status' => 0]); /* Previous made not signed*/

                        if (empty($is_waiver_checked) && !empty($waiver_statement_id)) {
                            DB::table('waiver_acceptance')->insert([
                                'waiver_statement_id' => $request->waiver_statement_id,
                                'user_type' => 'mentee',
                                'user_id' => $check_user->id,
                                'status' => 1,
                                'login_from' => 'web',
                                'created_at' => $datetime,
                                'updated_at' => $datetime
                            ]);  /* New signed entry */
                        }

                        return redirect(route('mentee.dashboard'));
                    }

                } else {
                    return redirect()->back()->withInput(Input::all())->withErrors(array('password' => 'Wrong password'));
                }
            } else {
                return redirect()->back()->withInput(Input::all())->withErrors(array('email' => 'Inactive user'));
            }

        } else {
            return redirect()->back()->withInput(Input::all())->withErrors(array('email' => 'No user found-mentee'));
        }
    }

    private function mentorForgotPassword($request){
        $email = !empty($request->email) ? $request->email : '';
        if (!empty($email)) {
            $user = DB::table('mentor')->select('mentor.*', 'mentor_status.view_in_application')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor.email', '=', $email)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status', 1)->first();

            if (!empty($user)) {
                $activation_code = rand(100000000, 999999999);
                DB::table('mentor')->where('id', $user->id)->update(['activation_code' => $activation_code]);
                $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
                $to = $email;
                $subject = 'TSIC Mentor forgot password';
                email_send($to, $subject, $content);
                session(['message' => 'A new OTP sent to your email address', 'msg_class' => 'success']);
                return redirect('/reset-password' . '/' . urlencode($email));
            } else {
                session(['message' => 'Please give correct credentials.', 'msg_class' => 'danger']);
                return redirect('/forget-password');
            }
        } else {
            session(['message' => 'Please give email id', 'msg_class' => 'danger']);
            return redirect('/forget-password');
        }
    }

    private function menteeForgotPassword($request){
        $email = !empty($request->email) ? $request->email : '';
        if (!empty($email)) {
            $user = DB::table('mentee')->select('mentee.*', 'student_status.view_in_application')->leftJoin('student_status', 'student_status.id', 'mentee.status')->where('mentee.email', '=', $email)->where('student_status.view_in_application', '=', 1)->where('mentee.platform_status', 1)->first();

            if (!empty($user)) {
                $activation_code = rand(100000000, 999999999);
                DB::table('mentee')->where('id', $user->id)->update(['activation_code' => $activation_code]);
                $content = "Hi {$user->firstname} {$user->lastname}! Your OTP to change password is {$activation_code} .";
                $to = $email;
                $subject = 'TSIC Mentee forgot password';
                email_send($to, $subject, $content);
                session(['message' => 'A new OTP sent to your email address', 'msg_class' => 'success']);
                return redirect('/reset-password' . '/' . urlencode($email));
            } else {

                session(['message' => 'Please give correct credentials.', 'msg_class' => 'danger']);
                return redirect('/forget-password');
            }
        } else {
            session(['message' => 'Please give email id', 'msg_class' => 'danger']);
            return redirect('/forget-password');
        }
    }

    private function mentorResetPassword($request){

        $email = !empty($request->email) ? $request->email : '';
        $activation_code = !empty($request->activation_code) ? $request->activation_code : '';
        $password = !empty($request->password) ? $request->password : '';

        $user_obj = DB::table('mentor')->select('mentor.*', 'mentor_status.view_in_application')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor.email', '=', $email)->where('mentor.activation_code', '=', $activation_code)->where('mentor_status.view_in_application', '=', 1)->where('mentor.platform_status', 1)->first();

        if (!empty($user_obj)) {
            DB::table('mentor')->where('id', $user_obj->id)->update(['password' => Hash::make($password), 'activation_code' => '']);

            session(['message' => 'Password changed successfully.', 'msg_class' => 'success']);
            return redirect('/login');

        } else {

            session(['message' => 'Please give correct data.', 'msg_class' => 'danger']);
            return redirect('/reset-password' . '/' . urlencode($email));
        }
    }

    private function menteeResetPassword($request){
        $email = !empty($request->email) ? $request->email : '';
        $activation_code = !empty($request->activation_code) ? $request->activation_code : '';
        $password = !empty($request->password) ? $request->password : '';

        $user_obj = DB::table('mentee')->select('mentee.*', 'student_status.view_in_application')->leftJoin('student_status', 'student_status.id', 'mentee.status')->where('mentee.email', '=', $email)->where('mentee.activation_code', '=', $activation_code)->where('student_status.view_in_application', '=', 1)->where('mentee.platform_status', 1)->first();

        if (!empty($user_obj)) {
            DB::table('mentee')->where('id', $user_obj->id)->update(['password' => Hash::make($password), 'activation_code' => '0']);

            session(['message' => 'Password changed successfully.', 'msg_class' => 'success']);
            return redirect('/login');

        } else {

            session(['message' => 'Please give correct data.', 'msg_class' => 'danger']);
            return redirect('/reset-password' . '/' . urlencode($email));
        }
    }
}
