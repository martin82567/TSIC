<?php


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

use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    protected $user_id;
    protected $creationmethod;
    protected $externalId;

    public function __construct(Request $request)
    {
        $header = $request->header('Authorizations');
        if (empty($header)) {
            response()->json(array('status' => false, 'message' => "Authentication failed"))->send();
            exit();
        }
        if (substr($header, 0, 7) != "Bearer ") {
            response()->json(array('status' => false, 'message' => "Please add prefix and a space before the token"))->send();
            exit();
        }
        try {
            $this->header_str = substr($header, 7, strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);

            $chk_user = DB::table('mentee')->where('id', $this->user_id)->first();

            if (empty($chk_user)) {
                response()->json(array('status' => false, 'message' => "User not found"))->send();
                exit();
            }

            $this->assigned_by = $chk_user->assigned_by;
            $this->creationmethod = $chk_user->creationmethod;
            $this->externalId = $chk_user->externalId;

            mentee_last_activity($this->user_id);

        } catch (\Exception $e) {
            response()->json(array('status' => false, 'message' => "Token is invalid"))->send();
            exit();
        }

    }

    public function logout(Request $request)
    {
        $user_id = $this->user_id;

        if (!empty($user_id)) {
            $empty_firebase = DB::table('mentee')->where('id', $user_id)->update(['firebase_id' => '', 'voip_device_token' => '']);

            DB::table('waiver_acceptance')->where('user_type', 'mentee')->where('login_from', 'app')->where('user_id', $user_id)->where('status', 1)->update(['status' => 0]); /* Previous made not signed*/

            return response()->json(['status' => true, 'message' => "You have successfully logged out", 'data' => array()]);
        }

    }

    public function change_password(Request $request)
    {
        $user_id = $this->user_id;

        if (empty($request->current_password)) {
            return response()->json(['status' => false, 'message' => "Please give current password", 'data' => array()]);
        }

        if (empty($request->new_password)) {
            return response()->json(['status' => false, 'message' => "Please give new password", 'data' => array()]);
        }

        $password = $request->current_password;
        $new_password = $request->new_password;

        $user = DB::table('mentee')->select('mentee.*', 'student_status.view_in_application')->leftJoin('student_status', 'student_status.id', 'mentee.status')->where('mentee.id', '=', $user_id)->where('student_status.view_in_application', '=', 1)->first();
        // $user =DB::table('mentee')->where('id', '=', $user_id)->where('status', '=', 1)->first();

        if (empty($user->is_logged_out)) {
            return response()->json(['status' => false, 'message' => "Logged Out"]);
        }

        if (!empty($user)) {
            if (Hash::check($password, $user->password)) {
                DB::table('mentee')->where('id', $user->id)->update(['password' => Hash::make($new_password), 'is_new' => 0]);
                return response()->json(['status' => true, 'message' => "Password changed successfully.", 'data' => array()]);
            } else {
                return response()->json(['status' => false, 'message' => "Please give correct credentials.", 'data' => array()]);
            }
        } else {
            return response()->json(['status' => false, 'message' => "Please give correct credentials.", 'data' => array()]);
        }
    }


    public function update_user_details(Request $request)
    {

        $user_id = $this->user_id;
        $user = DB::table('mentee')->where('id', '=', $user_id)->first();

        if (empty($user->is_logged_out)) {
            return response()->json(['status' => false, 'message' => "Logged Out"]);
        }

        $image = '';
        if (!empty($request->userimage)) {
            $img = $request->userimage;
            // $storage_path = public_path() . '/uploads/userimage/';
            $image = time() . uniqid(rand());
            $image = $image . '.' . $img->getClientOriginalExtension();
            // $img->move($storage_path,$image);

            $OldfilePath = 'userimage/' . $user->image;
            Storage::delete($OldfilePath);

            $filePath = 'userimage/' . $image;
            Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');
        }


        $firstname = !empty($request->firstname) ? $request->firstname : $user->firstname;
        $middlename = $request->middlename;
        if (empty($middlename)) {
            $middlename = '';
        }
        $lastname = !empty($request->lastname) ? $request->lastname : $user->lastname;
        // if(empty($lastname)){
        //     $lastname = '';
        // }
        if (!empty($image)) {
            DB::table('mentee')->where('id', $user_id)->update(['image' => $image, 'firstname' => $firstname, 'middlename' => $middlename, 'lastname' => $lastname]);
        } else {
            DB::table('mentee')->where('id', $user_id)->update(['firstname' => $firstname, 'middlename' => $middlename, 'lastname' => $lastname]);
        }

        $updated_user = DB::table('mentee')->where('id', '=', $user_id)->first();


        return response()->json(['status' => true, 'message' => "Data Updated Successfully.", 'data' => $updated_user]);
    }

    public function user_details(Request $request)
    {
        $user_id = $this->user_id;

        $user = DB::table('mentee')
            ->select('mentee.id', 'mentee.firstname', 'mentee.middlename', 'mentee.lastname', 'mentee.email', 'mentee.current_living_details', 'mentee.image', 'mentee.assigned_by', 'mentee.is_logged_out', 'admins.firstname as linked_agency_firstname', 'admins.middlename as linked_agency_middlename', 'admins.lastname as linked_agency_lastname', 'admins.name as linked_agency_name')
            ->leftjoin('admins', 'mentee.assigned_by', '=', 'admins.id')
            ->where('mentee.id', '=', $user_id)
            ->first();

        if (!empty($user->is_logged_out)) {
            return response()->json(['status' => false, 'message' => "Logged Out"]);
        }


        $user->unread_goal = user_total_unread_goaltask_count('mentee', $user_id, 'goal');
        $user->unread_task = user_total_unread_goaltask_count('mentee', $user_id, 'task');

        $mentee_staff_chat_count = DB::table('mentee_staff_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        if (empty($mentee_staff_chat_count)) {
            $mentee_staff_chat_count = 0;
        }

        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'mentor')->where('receiver_is_read', 0)->count();
        if (empty($mentor_mentee_chat_count)) {
            $mentor_mentee_chat_count = 0;
        }

        $user->mentor_mentee_chat_count = $mentor_mentee_chat_count;
        $user->mentee_staff_chat_count = $mentee_staff_chat_count;
        // $user->unread_chat_count = $mentee_staff_chat_count + $mentor_mentee_chat_count;

        $user->schedule_session_count = schedule_session_count('mentee', $user_id);
        $user->message_center_count = user_unread_message_center_count('mentee', $user_id);

        $get_mentors = DB::table('assign_mentee')->select('assign_mentee.*', 'mentor.is_active', 'mentor.platform_status', 'mentor_status.view_in_application')->leftJoin('mentor', 'mentor.id', 'assign_mentee.assigned_by')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.platform_status', 1)->where('assign_mentee.mentee_id', $user_id)->get()->toarray();

        $mentor_ids = array();
        if (!empty($get_mentors)) {
            foreach ($get_mentors as $gm) {
                $mentor_ids[] = $gm->assigned_by;
            }
        }
        $sum_mentor_session_log_count = "0";
        $label = "No star";
        $label_no = 1;


        /*if(!empty($mentor_ids)){
            $sum_mentor_session_log_count = DB::table('mentor_session_log_count')->whereIn('mentor_id',$mentor_ids)->sum('count');

            if (($sum_mentor_session_log_count >= 0 && $sum_mentor_session_log_count <= 4)){
                $label = 'No star';
                $label_no = 1;
            }else if(($sum_mentor_session_log_count >= 5 && $sum_mentor_session_log_count <= 9)){
                $label = 'Bronze';
                $label_no = 2;
            }else if(($sum_mentor_session_log_count >= 10 && $sum_mentor_session_log_count <= 14)){
                $label = 'Silver';
                $label_no = 3;
            }else if(($sum_mentor_session_log_count >= 15)){
                $label = 'Gold Star';
                $label_no = 4;
            }
        }*/

        /*upcoming session*/
        $today = date('Y-m-d H:i:s');
        $upcoming_meeting = DB::table('meeting_users')
            ->select('meeting.*', 'meeting_status.status', 'session_method_location.method_value', 'meeting_mentor.user_id AS mentor_id', 'mentor.firstname', 'mentor.lastname')
            ->join('meeting', 'meeting.id', 'meeting_users.meeting_id')
            ->leftJoin('meeting_status', 'meeting_status.meeting_id', 'meeting.id')
            ->leftJoin('session_method_location', 'session_method_location.id', 'meeting.session_method_location_id')
            ->leftJoin('meeting_users AS meeting_mentor', function ($join) {
                $join->on('meeting_mentor.meeting_id', '=', 'meeting.id');
                $join->where('meeting_mentor.type', '=', 'mentor');
            })
            ->leftJoin('mentor', 'mentor.id', 'meeting_mentor.user_id')
            ->where('meeting_users.type', 'mentee')
            ->where('meeting_users.user_id', $user->id)
            ->where('meeting.schedule_time', '>=', $today)
            ->where('meeting_status.status', 1)
            ->orderBy('meeting.schedule_time', 'asc')
            ->first();

        $user->upcoming_meeting = $upcoming_meeting;
        try {
            if (!empty($this->externalId)) {
                $getCountByStudentId = getCountByStudentId($this->externalId);
                $sum_mentor_session_log_count = $getCountByStudentId;
                if (($sum_mentor_session_log_count >= 0 && $sum_mentor_session_log_count <= 4)) {
                    $label = 'No star';
                    $label_no = 1;
                } else if (($sum_mentor_session_log_count >= 5 && $sum_mentor_session_log_count <= 9)) {
                    $label = 'Bronze';
                    $label_no = 2;
                } else if (($sum_mentor_session_log_count >= 10 && $sum_mentor_session_log_count <= 14)) {
                    $label = 'Silver';
                    $label_no = 3;
                } else if (($sum_mentor_session_log_count >= 15)) {
                    $label = 'Gold Star';
                    $label_no = 4;
                }
            }


            $user->sum_mentor_session_log_count = $sum_mentor_session_log_count;
            $user->session_log_label = $label;
            $user->session_log_label_no = $label_no;
        } catch (\Exception $e) {
            if (!empty($mentor_ids)) {
                $sum_mentor_session_log_count = DB::table('mentor_session_log_count')->whereIn('mentor_id', $mentor_ids)->sum('count');

                if (($sum_mentor_session_log_count >= 0 && $sum_mentor_session_log_count <= 4)) {
                    $label = 'No star';
                    $label_no = 1;
                } else if (($sum_mentor_session_log_count >= 5 && $sum_mentor_session_log_count <= 9)) {
                    $label = 'Bronze';
                    $label_no = 2;
                } else if (($sum_mentor_session_log_count >= 10 && $sum_mentor_session_log_count <= 14)) {
                    $label = 'Silver';
                    $label_no = 3;
                } else if (($sum_mentor_session_log_count >= 15)) {
                    $label = 'Gold Star';
                    $label_no = 4;
                }
            }
            $user->sum_mentor_session_log_count = $sum_mentor_session_log_count;
            $user->session_log_label = $label;
            $user->session_log_label_no = $label_no;
        }

        /*+++++++++++++++++++++++++++++++++++++++++*/
        $today = date('Y-m-d H:i:s');

        $affiliate_system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");
        $affiliate_system_messaging = $affiliate_system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
            $join->on('appids.message_id', '=', 'msg.id');
            $join->where('appids.app_id', '=', 1);
        });
        $affiliate_system_messaging = $affiliate_system_messaging->where('appids.app_id', 1);
        // $affiliate_system_messaging = $affiliate_system_messaging->where('msg.created_by', $user->assigned_by);
        $affiliate_system_messaging = $affiliate_system_messaging->where('msg.is_expired', 0);
        $affiliate_system_messaging = $affiliate_system_messaging->orderBy('msg.id', 'desc');
        $affiliate_system_messaging = $affiliate_system_messaging->get()->toarray();
        $user->affiliate_system_messaging = $affiliate_system_messaging;
        /*+++++++++++++++++++++++++++++++++++++++++*/

        return response()->json(['status' => true, 'message' => "Here is the user details", 'data' => array('user_details' => $user)]);
    }

    public function user_details_bkp(Request $request)
    {
        $user_id = $this->user_id;

        $user = DB::table('mentee')
            ->select('mentee.id', 'mentee.firstname', 'mentee.middlename', 'mentee.lastname', 'mentee.email', 'mentee.current_living_details', 'mentee.image', 'mentee.assigned_by', 'admins.firstname as linked_agency_firstname', 'admins.middlename as linked_agency_middlename', 'admins.lastname as linked_agency_lastname', 'admins.name as linked_agency_name')
            // ->leftjoin('assign_victim', 'assign_victim.victim_id', '=', 'mentee.id')
            ->leftjoin('admins', 'mentee.assigned_by', '=', 'admins.id')
            ->where('mentee.id', '=', $user_id)
            ->first();


        $user->unread_goal = user_total_unread_goaltask_count('mentee', $user_id, 'goal');
        $user->unread_task = user_total_unread_goaltask_count('mentee', $user_id, 'task');

        $mentee_staff_chat_count = DB::table('mentee_staff_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        if (empty($mentee_staff_chat_count)) {
            $mentee_staff_chat_count = 0;
        }

        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'mentor')->where('receiver_is_read', 0)->count();
        if (empty($mentor_mentee_chat_count)) {
            $mentor_mentee_chat_count = 0;
        }

        $user->mentor_mentee_chat_count = $mentor_mentee_chat_count;
        $user->mentee_staff_chat_count = $mentee_staff_chat_count;
        // $user->unread_chat_count = $mentee_staff_chat_count + $mentor_mentee_chat_count;

        $user->schedule_session_count = schedule_session_count('mentee', $user_id);

        $get_mentors = DB::table('assign_mentee')->select('assign_mentee.*', 'mentor.is_active', 'mentor.platform_status', 'mentor_status.view_in_application')->leftJoin('mentor', 'mentor.id', 'assign_mentee.assigned_by')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.platform_status', 1)->where('assign_mentee.mentee_id', $user_id)->get()->toarray();

        $mentor_ids = array();
        if (!empty($get_mentors)) {
            foreach ($get_mentors as $gm) {
                $mentor_ids[] = $gm->assigned_by;
            }
        }
        $sum_mentor_session_log_count = "0";
        $label = "";
        $label_no = 0;
        if (!empty($mentor_ids)) {
            $sum_mentor_session_log_count = DB::table('mentor_session_log_count')->whereIn('mentor_id', $mentor_ids)->sum('count');

            if (($sum_mentor_session_log_count >= 0 && $sum_mentor_session_log_count <= 4)) {
                $label = 'No star';
                $label_no = 1;
            } else if (($sum_mentor_session_log_count >= 5 && $sum_mentor_session_log_count <= 9)) {
                $label = 'Bronze';
                $label_no = 2;
            } else if (($sum_mentor_session_log_count >= 10 && $sum_mentor_session_log_count <= 14)) {
                $label = 'Silver';
                $label_no = 3;
            } else if (($sum_mentor_session_log_count >= 15)) {
                $label = 'Gold Star';
                $label_no = 4;
            }
        }
        $user->sum_mentor_session_log_count = $sum_mentor_session_log_count;
        $user->session_log_label = $label;
        $user->session_log_label_no = $label_no;

        /*+++++++++++++++++++++++++++++++++++++++++*/
        $today = date('Y-m-d H:i:s');

        $affiliate_system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");
        $affiliate_system_messaging = $affiliate_system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
            $join->on('appids.message_id', '=', 'msg.id');
            $join->where('appids.app_id', '=', 1);
        });
        $affiliate_system_messaging = $affiliate_system_messaging->where('appids.app_id', 1);
        // $affiliate_system_messaging = $affiliate_system_messaging->where('msg.created_by', $user->assigned_by);
        $affiliate_system_messaging = $affiliate_system_messaging->where('msg.is_expired', 0);
        $affiliate_system_messaging = $affiliate_system_messaging->orderBy('msg.id', 'desc');
        $affiliate_system_messaging = $affiliate_system_messaging->get()->toarray();
        $user->affiliate_system_messaging = $affiliate_system_messaging;
        /*+++++++++++++++++++++++++++++++++++++++++*/

        return response()->json(['status' => true, 'message' => "Here is the user details", 'data' => array('user_details' => $user)]);
    }

    public function mentordetails(Request $request)
    {
        $user_id = $this->user_id;
        $chk_user = DB::table('mentee')->where('id', $user_id)->first();
        if (!empty($chk_user->is_logged_out)) {
            return response()->json(['status' => false, 'message' => "Logged Out"]);
        }

        $mentor_details = DB::table('mentor')
            ->select('mentor.*', 'assign_mentee.is_primary')
            ->join('assign_mentee', 'assign_mentee.assigned_by', '=', 'mentor.id')
            ->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')
            ->where('assign_mentee.mentee_id', '=', $user_id)
            // ->where('assign_mentee.is_primary', 1)
            ->where('mentor_status.view_in_application', '=', 1)
            ->where('mentor.platform_status', '=', 1)
            ->get()->toarray();

        if (!empty($mentor_details)) {
            foreach ($mentor_details as $md) {

                $last_session_date = '';
                $session = DB::table('session')->select('schedule_date')->where('mentee_id', $user_id)->where('mentor_id', $md->id)->orderby('id', 'desc')->first();
                if (!empty($session)) {
                    $last_session_date = !empty($session->schedule_date) ? date('m-d-Y', strtotime($session->schedule_date)) : '';
                }


                $session_count = DB::table('session')->select('schedule_date')->where('mentee_id', $user_id)->where('mentor_id', $md->id)->count();
                if (empty($session_count)) {
                    $session_count = 0;
                }

                $md->last_session_date = $last_session_date;
                $md->session_count = $session_count;

                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char . rand(10000000, 99999999);

                $channel_sid = "";

                $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('mentor_id', $md->id)->where('mentee_id', $user_id)->first();

                if (!empty($mentor_mentee_chat_codes)) {
                    $channel_sid = !empty($mentor_mentee_chat_codes->channel_sid) ? $mentor_mentee_chat_codes->channel_sid : "";
                    $code = !empty($mentor_mentee_chat_codes->code) ? $mentor_mentee_chat_codes->code : "";
                } else {
                    DB::table('mentor_mentee_chat_codes')->insert(['code' => $code, 'mentor_id' => $md->id, 'mentee_id' => $user_id]);
                }

                $md->code = $code;
                $md->channel_sid = $channel_sid;

                $mentor_session_log_count = mentor_session_log_count($md->id);
                $md->session_log_count = $mentor_session_log_count['count'];
                $md->session_log_label = $mentor_session_log_count['label'];
                $md->session_log_label_no = $mentor_session_log_count['label_no'];

            }
        }

        return response()->json(['status' => true, 'message' => "Mentor List Data.", 'data' => array('mentor_details' => $mentor_details)]);
    }


    public function get_timezone(Request $request)
    {
        $user_id = $this->user_id;

        $victim = DB::table('mentee')->select('assigned_by')->where('id', $user_id)->first();
        $affiliate_id = !empty($victim->assigned_by) ? $victim->assigned_by : '';

        if (!empty($affiliate_id)) {
            $affiliate_data = DB::table('admins')->select('admins.id', 'admins.timezone', 'timezones.timezone_offset')->leftjoin('timezones', 'timezones.value', 'admins.timezone')->where('admins.id', $affiliate_id)->first();

            $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : 'America/New_York';
            $timezone_offset = !empty($affiliate_data->timezone_offset) ? $affiliate_data->timezone_offset : '-4';

            return response()->json(['status' => true, 'message' => "This is timezone", 'data' => array('timezone' => $timezone, 'timezone_offset' => $timezone_offset)]);
        }
    }

    public function update_password(Request $request)
    {
        # code...
        $user_obj = DB::table('mentee')->find($this->user_id);
        if (!empty($user_obj->is_logged_out)) {
            return response()->json(['status' => false, 'message' => "Logged Out"]);
        }


        date_default_timezone_set('America/New_York');
        $now = date('Y-m-d H:i:s');

        $current_password = !empty($request->current_password) ? $request->current_password : '';
        $new_password = !empty($request->new_password) ? $request->new_password : '';
        $confirm_new_password = !empty($request->confirm_new_password) ? $request->confirm_new_password : '';

        if (empty($current_password)) {
            return response()->json(['status' => false, 'message' => "Current password is required", 'data' => array()]);
        }

        if (empty($new_password)) {
            return response()->json(['status' => false, 'message' => "New password is required", 'data' => array()]);
        }

        if (empty($confirm_new_password)) {
            return response()->json(['status' => false, 'message' => "Confirm new password is required", 'data' => array()]);
        }

        if (strlen($new_password) <= 5) {
            return response()->json(['status' => false, 'message' => "Please use minimum six characters for setting new password", 'data' => array()]);
        }

        if ($new_password != $confirm_new_password) {
            return response()->json(['status' => false, 'message' => "New and Confirm new password both must be same", 'data' => array()]);
        }

        if (Hash::check($current_password, $user_obj->password)) {
            DB::table('mentee')->where('id', $this->user_id)->update(['password' => Hash::make($new_password), 'updated_at' => $now]);
            return response()->json(['status' => true, 'message' => "Password updated successfully", 'data' => array('new_password' => $new_password)]);
        } else {
            return response()->json(['status' => false, 'message' => "Wrong current password", 'data' => array()]);
        }


    }

    public function update_tokens(Request $request)
    {

        $input = $request->all();

        $firebase_id = !empty($input['firebase_id']) ? $input['firebase_id'] : '';
        $voip_device_token = !empty($input['voip_device_token']) ? $input['voip_device_token'] : '';

        $data = [];
        if (!empty($firebase_id)) {
            $data['firebase_id'] = $firebase_id;
        }

        if (!empty($voip_device_token)) {
            $data['voip_device_token'] = $voip_device_token;
        }
        if ($firebase_id || $voip_device_token) {

            DB::table('mentee')->where('id', $this->user_id)->update(
                $data
            );

            return response()->json(['status' => true, 'message' => 'Successfully updated firebase id and voip token.']);
        } else {
            return response()->json(['status' => false, 'message' => "Error while updating."]);
        }

    }

}
