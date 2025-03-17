<?php


namespace App\Http\Controllers\Api\V1\mentor;

use Carbon\Carbon;
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
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

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

            $chk_user = DB::table('mentor')->where('id', $this->user_id)->first();

            if (empty($chk_user)) {
                response()->json(array('status' => false, 'message' => "User not found"))->send();
                exit();
            }

            if (!empty($chk_user->is_logged_out)) {
                response()->json(array('status' => false, 'message' => "Logged Out"))->send();
                exit();
            }

            mentor_last_activity($this->user_id);

            $this->creationmethod = $chk_user->creationmethod;
            $this->externalId = $chk_user->externalId;

        } catch (\Exception $e) {
            response()->json(array('status' => false, 'message' => "Token is invalid"))->send();
            exit();
        }

    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function my_profile()
    {
        $user_details = DB::table('mentor')->select('mentor.*', 'admins.name AS agency_name', 'admins.profile_pic AS linked_agency_image')->join('admins', 'admins.id', 'mentor.assigned_by')->where('mentor.id', $this->user_id)->first();
        unset($user_details->password);
        unset($user_details->created_at);
        unset($user_details->updated_at);
        $user_details->profile_pic = !empty($user_details->image) ? $user_details->image : '';
        unset($user_details->image);

        $mentor_staff_chat_count = DB::table('mentor_staff_chat_threads')->where('receiver_id', $this->user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        if (empty($mentor_staff_chat_count)) {
            $mentor_staff_chat_count = 0;
        }

        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $this->user_id)->where('from_where', 'mentee')->where('receiver_is_read', 0)->count();
        if (empty($mentor_mentee_chat_count)) {
            $mentor_mentee_chat_count = 0;
        }

        $user_details->mentor_mentee_chat_count = $mentor_mentee_chat_count;
        $user_details->mentor_staff_chat_count = $mentor_staff_chat_count;
        $user_details->message_center_count = user_unread_message_center_count('mentor', $this->user_id);

        /*$mentor_session_log_count = mentor_session_log_count($user_details->id);
        $user_details->session_log_count = $mentor_session_log_count['count'];
        $user_details->session_log_label = $mentor_session_log_count['label'];
        $user_details->session_log_label_no = $mentor_session_log_count['label_no'];*/

        $session_log_count = "0";
        $label = "No star";
        $label_no = 1;
        try {
            if (!empty($this->externalId)) {
                $getMentorStudentsCount = getMentorStudentsCount($this->externalId);
                $session_log_count = (string)$getMentorStudentsCount;

                if (($session_log_count >= 0 && $session_log_count <= 4)) {
                    $label = 'No star';
                    $label_no = 1;
                } else if (($session_log_count >= 5 && $session_log_count <= 9)) {
                    $label = 'Bronze';
                    $label_no = 2;
                } else if (($session_log_count >= 10 && $session_log_count <= 14)) {
                    $label = 'Silver';
                    $label_no = 3;
                } else if (($session_log_count >= 15)) {
                    $label = 'Gold Star';
                    $label_no = 4;
                }
            }

            $user_details->session_log_count = $session_log_count;
            $user_details->session_log_label = $label;
            $user_details->session_log_label_no = $label_no;
        } catch (\Exception $e) {
            $mentor_session_log_count = mentor_session_log_count($user_details->id);
            $user_details->session_log_count = $mentor_session_log_count['count'];
            $user_details->session_log_label = $mentor_session_log_count['label'];
            $user_details->session_log_label_no = $mentor_session_log_count['label_no'];
        }

        $today = date('Y-m-d H:i:s');

        $upcoming_meeting = DB::table('meeting_users')
            ->select('meeting.*', 'meeting_status.status', 'session_method_location.method_value', 'meeting_mentee.user_id AS mentee_id', 'mentee.firstname', 'mentee.lastname')
            ->join('meeting', 'meeting.id', 'meeting_users.meeting_id')
            ->leftJoin('meeting_status', 'meeting_status.meeting_id', 'meeting.id')
            ->leftJoin('session_method_location', 'session_method_location.id', 'meeting.session_method_location_id')
            ->leftJoin('meeting_users AS meeting_mentee', function ($join) {
                $join->on('meeting_mentee.meeting_id', '=', 'meeting.id');
                $join->where('meeting_mentee.type', '=', 'mentee');
            })
            ->leftJoin('mentee', 'mentee.id', 'meeting_mentee.user_id')
            ->where('meeting_users.type', 'mentor')
            ->where('meeting_users.user_id', $user_details->id)
            ->where('meeting.schedule_time', '>=', $today)
            ->where('meeting_status.status', 1)
            ->orderBy('meeting.schedule_time', 'asc')
            ->first();

        /*previous session history*/
        $past_meeting = \Illuminate\Support\Facades\DB::table('meeting_users')
            ->select('meeting.*', 'meeting_status.status', 'session_method_location.method_value', 'meeting_mentee.user_id AS mentee_id', 'mentee.firstname', 'mentee.lastname')
            ->join('meeting', 'meeting.id', 'meeting_users.meeting_id')
            ->leftJoin('meeting_status', 'meeting_status.meeting_id', 'meeting.id')
            ->leftJoin('session_method_location', 'session_method_location.id', 'meeting.session_method_location_id')
            ->leftJoin('meeting_users AS meeting_mentee', function ($join) {
                $join->on('meeting_mentee.meeting_id', '=', 'meeting.id');
                $join->where('meeting_mentee.type', '=', 'mentee');
            })
            ->leftJoin('mentee', 'mentee.id', 'meeting_mentee.user_id')
            ->where('meeting_users.type', 'mentor')
            ->where('meeting_users.user_id', $user_details->id)
            ->where('meeting.schedule_time', '<=', $today)
            ->where('meeting.schedule_time', '>=', Carbon::now()->addDays(-3))
            ->orderBy('meeting.schedule_time', 'desc')
            ->first();

        $user_details->past_meeting = $past_meeting;
        $user_details->upcoming_meeting = $upcoming_meeting;
        $user_details->schedule_session_count = schedule_session_count('mentor', $user_details->id);
        // $user_details->session_count = $session_count;

        /*+++++++++++++++++++++++++++++++++++++++++*/
        $today = date('Y-m-d H:i:s');

        $affiliate_system_messaging = DB::table(SYSTEM_MESSAGING . ' AS msg')->select('msg.id', 'msg.message', 'msg.start_datetime', 'msg.end_datetime', 'appids.app_id')->whereRaw("msg.start_datetime <= '" . $today . "' AND msg.end_datetime >= '" . $today . "'");
        $affiliate_system_messaging = $affiliate_system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS . ' AS appids', function ($join) {
            $join->on('appids.message_id', '=', 'msg.id');
            $join->where('appids.app_id', '=', 2);
        });
        $affiliate_system_messaging = $affiliate_system_messaging->where('appids.app_id', 2);
        // $affiliate_system_messaging = $affiliate_system_messaging->where('msg.created_by', $user_details->assigned_by);
        $affiliate_system_messaging = $affiliate_system_messaging->where('msg.is_expired', 0);
        $affiliate_system_messaging = $affiliate_system_messaging->orderBy('msg.id', 'desc');
        $affiliate_system_messaging = $affiliate_system_messaging->get()->toarray();
        $user_details->affiliate_system_messaging = $affiliate_system_messaging;
        /*+++++++++++++++++++++++++++++++++++++++++*/


        return response()->json(['status' => true, 'message' => "Here is your details", 'data' => $user_details]);
    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function my_profile_bkp()
    {
        $user_details = DB::table('mentor')->select('mentor.*', 'admins.name AS agency_name', 'admins.profile_pic AS linked_agency_image')->join('admins', 'admins.id', 'mentor.assigned_by')->where('mentor.id', $this->user_id)->first();
        unset($user_details->password);
        unset($user_details->created_at);
        unset($user_details->updated_at);
        $user_details->profile_pic = !empty($user_details->image) ? $user_details->image : '';
        // $user_details->linked_agency_image = !empty($user_details->linked_agency_image)?url('/public/uploads/agency_pic').'/'.$user_details->linked_agency_image:'';
        unset($user_details->image);

        // $month = date('m');
        // if($month >= 7){
        //     $year = date('Y');
        //     $start_date = $year.'-07-01';
        //     $year++;
        //     $end_date = $year.'-06-30';

        // }
        // if($month < 7){
        //     $year = date('Y');
        //     $year--;
        //     $start_date = $year.'-07-01';
        //     $year++;
        //     $end_date = $year.'-06-30';

        // }
        // $session_count = DB::table('session')->where('mentor_id',$this->user_id)->where('schedule_date', '>=', $start_date)->where('schedule_date', '<=', $end_date)->count();
        // if(empty($session_count)){
        //     $session_count = 0;
        // }


        $mentor_staff_chat_count = DB::table('mentor_staff_chat_threads')->where('receiver_id', $this->user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        if (empty($mentor_staff_chat_count)) {
            $mentor_staff_chat_count = 0;
        }

        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $this->user_id)->where('from_where', 'mentee')->where('receiver_is_read', 0)->count();
        if (empty($mentor_mentee_chat_count)) {
            $mentor_mentee_chat_count = 0;
        }

        $user_details->unread_chat_count = $mentor_staff_chat_count + $mentor_mentee_chat_count;

        $mentor_session_log_count = mentor_session_log_count($user_details->id);
        $user_details->session_log_count = $mentor_session_log_count['count'];
        $user_details->session_log_label = $mentor_session_log_count['label'];
        $user_details->session_log_label_no = $mentor_session_log_count['label_no'];


        $user_details->session_count = $mentor_session_log_count['count'];
        // $user_details->session_count = $session_count;
        return response()->json(['status' => true, 'message' => "Here is your details", 'data' => $user_details]);
    }

    /**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function update_profile(Request $request)
    {
        date_default_timezone_set('America/New_York');
        $now = date('Y-m-d H:i:s');

        $chk_user = DB::table('mentor')->where('id', $this->user_id)->first();

        $firstname = !empty($request->firstname) ? $request->firstname : $chk_user->firstname;
        $middlename = !empty($request->middlename) ? $request->middlename : '';
        $lastname = !empty($request->lastname) ? $request->lastname : $chk_user->lastname;
        $phone = !empty($request->phone) ? $request->phone : '';
        $image = !empty($request->image) ? $request->image : '';

        if (!empty($phone)) {
            // if (strlen($phone)>11) {
            //     return response()->json(['status'=>false, 'message' => "Phone number should be 10 digits", 'data' => array()]);
            // }

            // if(!is_numeric($phone)){
            //     return response()->json(['status'=>false, 'message' => "Phone number should be numeric value", 'data' => array()]);
            // }
        }

        if (!empty($image)) {
            // $filename = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
            $img = $image;
            $image_name = time() . uniqid(rand());
            $image_name = $image_name . '.' . $img->getClientOriginalExtension();

            // $storage_path = public_path() . '/uploads/mentor_pic/';
            // $image->move($storage_path,$image_name);

            $OldfilePath = 'mentor_pic/' . $chk_user->image;
            Storage::delete($OldfilePath);

            $filePath = 'mentor_pic/' . $image_name;
            Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');

        } else {
            // $filename = '';
            $image_name = $chk_user->image;
        }

        DB::table('mentor')->where('id', $this->user_id)->update(['firstname' => $firstname, 'middlename' => $middlename, 'lastname' => $lastname, 'phone' => $phone, 'image' => $image_name, 'updated_at' => $now]);

        $data = array('firstname' => $chk_user->firstname, 'middlename' => $chk_user->middlename, 'lastname' => $chk_user->lastname, 'phone' => $chk_user->phone, 'image' => $image_name);

        return response()->json(['status' => true, 'message' => "Profile updated successfully", 'data' => $data]);


    }

    public function update_password(Request $request)
    {
        # code...
        $user_obj = DB::table('mentor')->find($this->user_id);

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
            DB::table('mentor')->where('id', $this->user_id)->update(['password' => Hash::make($new_password), 'updated_at' => $now]);
            return response()->json(['status' => true, 'message' => "Password updated successfully", 'data' => array('new_password' => $new_password)]);
        } else {
            return response()->json(['status' => false, 'message' => "Wrong current password", 'data' => array()]);
        }


    }


}
