<?php

namespace App\Http\Controllers\Api;

use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;

class VideochatController extends Controller
{
    use ZoomMeetingTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function initiate_chat(Request $request)
    {
        $user_type = !empty($request->user_type) ? $request->user_type : 'mentor-mentee';
        $sender_id = !empty($request->sender_id) ? $request->sender_id : '';
        $sender_type = !empty($request->sender_type) ? $request->sender_type : '';
        $receiver_id = !empty($request->receiver_id) ? $request->receiver_id : '';
        $receiver_type = !empty($request->receiver_type) ? $request->receiver_type : '';

        if (empty($sender_id))
            return Response::json(['status' => false, 'message' => "Sender id is required", 'data' => (object)array()]);

        if (empty($sender_type))
            return Response::json(['status' => false, 'message' => "Sender type is required", 'data' => (object)array()]);

        if (empty($receiver_id))
            return Response::json(['status' => false, 'message' => "Receiver id is required", 'data' => (object)array()]);

        if (empty($receiver_type))
            return Response::json(['status' => false, 'message' => "Receiver type is required", 'data' => (object)array()]);

        if ($sender_type == 'mentor') {
            $sender_table = MENTOR;
        } else if ($sender_type == 'mentee') {
            $sender_table = MENTEE;
        }

        if ($receiver_type == 'mentor') {
            $receiver_table = MENTOR;
        } else if ($receiver_type == 'mentee') {
            $receiver_table = MENTEE;
        }

        $sender_details = DB::table($sender_table)->where('id', $sender_id)->first();
        $receiver_details = DB::table($receiver_table)->where('id', $receiver_id)->first();

        if (empty($sender_details))
            return Response::json(['status' => false, 'message' => "No sender found", 'data' => (object)array()]);

        if (empty($receiver_details))
            return Response::json(['status' => false, 'message' => "No receiver found", 'data' => (object)array()]);

        if (empty($sender_details->is_chat_video))
            return Response::json(['status' => false, 'message' => "Sender has no permission to initiate chat", 'data' => (object)array()]);

        if (empty($receiver_details->is_chat_video))
            return Response::json(['status' => false, 'message' => "Receiver has no permission to receive chat", 'data' => (object)array()]);

        $affiliate_id = $sender_details->assigned_by;

        $affiliate_data = DB::table('admins')->where('id', $affiliate_id)->first();
        $timezone = 'America/New_York';
        date_default_timezone_set($timezone);
        $created_at = date('Y-m-d H:i:s');
        $cur_date = date('Y-m-d');
        $cur_time = date('H:i:s');
        $cur_hour = date('H');
        /*Settings*/

        $settings = DB::table('settings')->first();
        $video_chat_duration = $settings->video_chat_duration;
        $video_chat_start_time = $settings->video_chat_start_time;
        $video_chat_end_time = $settings->video_chat_end_time;

        $start_time_text = date('g:i A', strtotime($video_chat_start_time));
        $end_time_text = date('g:i A', strtotime($video_chat_end_time));

        $total_duration_seconds = (60 * $video_chat_duration);

        $exist_video_chat_user = DB::select(DB::raw(" SELECT * FROM " . VIDEO_CHAT_USER . " WHERE user_type = '" . $user_type . "' AND (sender_id = " . $sender_id . " AND sender_type = '" . $sender_type . "' AND receiver_id = " . $receiver_id . " AND receiver_type = '" . $receiver_type . "') OR (sender_id = " . $receiver_id . " AND sender_type = '" . $receiver_type . "' AND receiver_id = " . $sender_id . " AND receiver_type = '" . $sender_type . "') "));

        if (!empty($exist_video_chat_user)) {
            $chat_code = $exist_video_chat_user[0]->chat_code;
        } else {
            $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');
            shuffle($characters);
            $rand_char = '';
            foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
            $chat_code = $rand_char . rand(10000000, 99999999);

            DB::table(VIDEO_CHAT_USER)->insert(['affiliate_id' => $affiliate_id, 'user_type' => $user_type, 'sender_id' => $sender_id, 'sender_type' => $sender_type, 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'chat_code' => $chat_code, 'remaining_time' => $total_duration_seconds, 'created_at' => $created_at, 'updated_at' => $created_at, 'week_date' => $cur_date]);

        }

        DB::table(VIDEO_CHAT_ROOMS)->where('chat_code', $chat_code)->where('duration', '=', '')->update(['duration' => 0]);

//		if(!empty($check_previous_room_completed)) {
//            return Response::json(['status' => false, 'message' => "You cannot initiate new chat until your previous call has been completed", 'data' => (object)array()]);
//        }
        /*++++++Check another user trying to call with them+++++++*/

        $check_receiver_another_chat_users = $another_chat_codes = $check_receiver_busy = array();

        $check_receiver_another_chat_users = DB::select(DB::raw(" SELECT * FROM " . VIDEO_CHAT_USER . " WHERE user_type = '" . $user_type . "' AND (sender_id = " . $receiver_id . " AND sender_type = '" . $receiver_type . "' ) OR ( receiver_id = " . $receiver_id . " AND receiver_type = '" . $receiver_type . "') "));

        if (!empty($check_receiver_another_chat_users)) {
            foreach ($check_receiver_another_chat_users as $ch) {
                $another_chat_codes[] = $ch->chat_code;

                if (!empty($another_chat_codes)) {
                    $check_receiver_busy = DB::table(VIDEO_CHAT_ROOMS)->whereIn('chat_code', $another_chat_codes)->where('duration', '=', '')->get()->toarray();

                    if (!empty($check_receiver_busy)) {
                        return Response::json(['status' => false, 'message' => "Receiver busy in another call", 'data' => (object)array()]);
                    }
                }
            }
        }


        $unique_name = $sender_type . '-' . $sender_id . '-' . $receiver_type . '-' . $receiver_id . '-' . time();

        $isweekend = isweekend();
        // if(!$isweekend){
        if ($cur_time >= $video_chat_start_time && $cur_time <= $video_chat_end_time) {

            $video_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code', $chat_code)->first();
            $remaining_time = $video_chat_user->remaining_time;

            if ($remaining_time == '0') {
                return Response::json(['status' => false, 'message' => "Video call Session expired", 'data' => (object)array()]);
            }

            return Response::json(['status' => true, 'message' => "Chat initiated successfully", 'data' => array('chat_code' => $chat_code, 'unique_name' => $unique_name, 'remaining_time' => $remaining_time)]);


        } else {
            return Response::json(['status' => false, 'message' => "The Video Chat feature is allowed between the hours of " . $start_time_text . " AND " . $end_time_text . " EST", 'data' => (object)array()]);

        }
        // }else{
        // 	return Response::json(['status'=>false,'message'=>"Video call only allows on week days",'data'=> (object) array()  ]);
        // }


    }


    public function generate_room(Request $request)
    {
        $chat_code = !empty($request->chat_code) ? $request->chat_code : '';
        $unique_name = !empty($request->unique_name) ? $request->unique_name : '';


        if (empty($chat_code))
            return Response::json(['status' => false, 'message' => "Chat code is required", 'data' => (object)array()]);


        if (empty($unique_name))
            return Response::json(['status' => false, 'message' => "Unique Name is required", 'data' => (object)array()]);


        $video_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code', $chat_code)->first();

        if (empty($video_chat_user))
            return Response::json(['status' => false, 'message' => "No chat code found", 'data' => (object)array()]);

        $remaining_time = $video_chat_user->remaining_time;
        $affiliate_id = $video_chat_user->affiliate_id;
        $affiliate_data = DB::table('admins')->where('id', $affiliate_id)->first();
        $timezone = 'America/New_York';
        date_default_timezone_set($timezone);
        $created_at = date('Y-m-d H:i:s');

        $room_sid = '';

        // echo $room_sid; die;

        // Generate Zoom JWT Access Token
        $sender_jwt = $this->generateMentorZoomToken($unique_name);
        $receiver_jwt = $this->generateMenteeZoomToken($unique_name);
//        return $jwt;

        if (!empty($sender_jwt)) {

            $explode_unique_name = explode("-", $unique_name);

            $sender_type = $explode_unique_name[0];
            $sender_id = $explode_unique_name[1];
            $receiver_type = $explode_unique_name[2];
            $receiver_id = $explode_unique_name[3];


            DB::table(VIDEO_CHAT_ROOMS)->insert([
                'affiliate_id' => $affiliate_id,
                'chat_code' => $chat_code,
                'unique_name' => $unique_name,
                'sender_type' => $sender_type,
                'sender_id' => $sender_id,
                'receiver_type' => $receiver_type,
                'receiver_id' => $receiver_id,
                'room_sid' => $room_sid,
                'created_at' => $created_at
            ]);


//            $sender_identity = $sender_type . '-' . $sender_id;
            $sender_accesstoken = $sender_jwt;
//            $receiver_identity = $receiver_type . '-' . $receiver_id;
            $receiver_accesstoken = $receiver_jwt;


            /*++++Send push notification for receiver++++*/

            if ($receiver_type == 'mentee') {
                $receiver_data = get_single_data_id(MENTEE, $receiver_id);
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            } else if ($receiver_type == 'mentor') {
                $receiver_data = get_single_data_id(MENTOR, $receiver_id);
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            }

            if ($sender_type == 'mentee') {
                $sender_data = DB::table(MENTEE)->select('firstname', 'lastname')->where('id', $sender_id)->first();
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;

            } else if ($sender_type == 'mentor') {
                $sender_data = DB::table(MENTOR)->select('firstname', 'lastname')->where('id', $sender_id)->first();;
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;
            }


            if (!empty($receiver_device_type) && !empty($receiver_firebase_id)) {

                if ($receiver_device_type == 'android') {

                    $message = "Request for video chat";

                    $time = time();
                    $send_data = array('title' => "Incoming Video call", 'type' => 'video_chat', 'receiver_accesstoken' => $receiver_accesstoken, 'message' => $message, 'firebase_token' => $receiver_firebase_id, 'unique_name' => $unique_name, 'sender_name' => $sender_name, 'room_sid' => $room_sid, 'remaining_time' => "$remaining_time", 'timestamp' => "$time", 'created_at' => $created_at);
                    $data_arr = array('meeting_data' => json_encode($send_data));

                    if ($receiver_device_type == "iOS") {
                        $msg = array('message' => $message, 'title' => "Incoming Video call", 'sound' => "default");
                        $fields = array('to' => $receiver_firebase_id, 'notification' => $msg, 'data' => $data_arr);

                    } else if ($receiver_device_type == "android") {
                        $fields = array('to' => $receiver_firebase_id, 'data' => $send_data); // For Android
                    }

                    $result = sendPushNotificationWithV1($fields);

                    if ($result) {
                        if (!empty($result['name'])) {
                            DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['room_sid' => $room_sid, 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => $receiver_device_type, 'receiver_firebase_id' => $receiver_firebase_id]);
                        }
                    }

                } else if ($receiver_device_type == 'iOS') {
                    if (!empty($receiver_voip_device_token)) {
                        $this->ios_voip_push_connect($receiver_voip_device_token, $receiver_accesstoken, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid, $remaining_time);
                    }

                }

            }

            /*++++++++Weekly Record+++++++++++*/

            $start_date = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
            $end_date = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');
            // $end_date = (date('D') != 'Fri') ? date('Y-m-d', strtotime('next Friday')) : date('Y-m-d');

            $exist_week = DB::table('video_chat_week')->where('chat_code', $chat_code)->where('start_date', $start_date)->first();

            $settings = DB::table('settings')->first();
            $video_chat_duration = $settings->video_chat_duration;
            $total_duration_seconds = (60 * $video_chat_duration);

            if (empty($exist_week)) {
                DB::table('video_chat_week')->insert(['chat_code' => $chat_code, 'start_date' => $start_date, 'end_date' => $end_date, 'used' => 0, 'remaining' => $total_duration_seconds, 'created_at' => $created_at]);
            }

            /*+++++++++++++++++++*/

            return Response::json(['status' => true, 'message' => "Chat room created successfully", 'data' => array('unique_name' => $unique_name, 'sender_accesstoken' => $sender_accesstoken, 'receiver_accesstoken' => $receiver_accesstoken, 'created_at' => $created_at)]);


        } else {
            return Response::json(['status' => false, 'message' => "Oops!Something went wrong. Room is not created", 'data' => (object)array()]);
        }


    }

    public function disconnect_room(Request $request)
    {
        $room_sid = '';
        $unique_name = !empty($request->unique_name) ? $request->unique_name : '';
        $disconnect_type = !empty($request->disconnect_type) ? $request->disconnect_type : 'miss_call';

        if (empty($unique_name))
            return Response::json(['status' => false, 'message' => "Please mention the unique name", 'data' => (object)array()]);

        if (!empty($disconnect_type)) {
            if ($disconnect_type != 'miss_call' && $disconnect_type != 'end_call') {
                return Response::json(['status' => false, 'message' => "Please mention disconnect type", 'data' => (object)array()]);
            }
        }

        $video_chat_rooms = DB::table(VIDEO_CHAT_ROOMS)->where('unique_name', $unique_name)->first();
        if (!empty($video_chat_rooms)) {
            if ($video_chat_rooms->duration != '') {
                return Response::json(['status' => false, 'message' => "This call already disconnected", 'data' => (object)array()]);
            }
        }

//        twilio_disconnect_room($room_sid);

        $video_chat_rooms = DB::table(VIDEO_CHAT_ROOMS)->where('unique_name', $unique_name)->first();
        $chat_code = !empty($video_chat_rooms) ? $video_chat_rooms->chat_code : '';
        $unique_name = !empty($video_chat_rooms) ? $video_chat_rooms->unique_name : '';

        // $explode_unique_name = explode("-",$unique_name);
        // $sender_type = $explode_unique_name[0];
        // $sender_id = $explode_unique_name[1];
        // $receiver_type = $explode_unique_name[2];
        // $receiver_id = $explode_unique_name[3];
        $sender_type = $video_chat_rooms->sender_type;
        $sender_id = $video_chat_rooms->sender_id;
        $receiver_type = $video_chat_rooms->receiver_type;
        $receiver_id = $video_chat_rooms->receiver_id;


        // $video_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code',$chat_code)->first();
        if ($receiver_type == 'mentee') {
            $receiver_data = DB::table(MENTEE)->where('id', $receiver_id)->first();
            $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
            $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
            $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
        } else if ($receiver_type == 'mentor') {
            $receiver_data = DB::table(MENTOR)->where('id', $receiver_id)->first();
            $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
            $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
            $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
        }

        if ($sender_type == 'mentee') {
            $sender_data = DB::table(MENTEE)->select('firstname', 'lastname')->where('id', $sender_id)->first();
            $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;

        } else if ($sender_type == 'mentor') {
            $sender_data = DB::table(MENTOR)->select('firstname', 'lastname')->where('id', $sender_id)->first();
            $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;
        }


        if (!empty($receiver_device_type) && !empty($receiver_firebase_id) && ($disconnect_type == 'miss_call')) {

            if ($receiver_device_type == 'android') {
                $message = "Video call has been cancelled";

                $time = time();
                $send_data = array('title' => "Missed video call", 'message' => $message, 'type' => $disconnect_type, 'firebase_token' => $receiver_firebase_id, 'unique_name' => $unique_name, 'sender_name' => $sender_name, 'timestamp' => "$time");
                $data_arr = array('meeting_data' => $send_data);

                if ($receiver_device_type == "iOS") {
                    $msg = array('message' => $message, 'title' => "Missed video call", 'sound' => "default");
                    $fields = array('to' => $receiver_firebase_id, 'notification' => $msg, 'data' => $data_arr, 'priority' => "high");

                } else if ($receiver_device_type == "android") {
                    $fields = array('to' => $receiver_firebase_id, 'data' => $send_data, 'priority' => "high"); // For Android
                }

                $result = sendPushNotificationWithV1($fields);

                if ($result) {
                    if (!empty($result['name'])) {
                        DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['notification_for' => 'disconnect_chat', 'room_sid' => '', 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => $receiver_device_type, 'receiver_firebase_id' => $receiver_firebase_id]);
                    }
                }
            } else if ($receiver_device_type == 'iOS') {
                if (!empty($receiver_voip_device_token)) {
                    $this->ios_voip_push_disconnect($receiver_voip_device_token, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid);
                }

            }

        }

        return Response::json(['status' => true, 'message' => "Video call has been ended", 'data' => array('room_sid' => $room_sid, 'unique_name' => $unique_name)]);


    }

    public function denied_call(Request $request)
    {
        $room_sid = '';
        $unique_name = !empty($request->unique_name) ? $request->unique_name : '';
        $denied_by = !empty($request->denied_by) ? $request->denied_by : '';
        $denied_by_type = !empty($request->denied_by_type) ? $request->denied_by_type : '';

        if (empty($unique_name))
            return Response::json(['status' => false, 'message' => "Please mention the unique name", 'data' => (object)array()]);

        if (empty($denied_by))
            return Response::json(['status' => false, 'message' => "Please mention the user who denied the call", 'data' => (object)array()]);

        if (empty($denied_by_type))
            return Response::json(['status' => false, 'message' => "Please mention the type of user who denied the call", 'data' => (object)array()]);


        $video_chat_rooms = DB::table(VIDEO_CHAT_ROOMS)->where('unique_name', $unique_name)->first();
        if (!empty($video_chat_rooms)) {
            if ($video_chat_rooms->duration != '') {
                return Response::json(['status' => false, 'message' => "This call already disconnected", 'data' => (object)array()]);
            }
        }

//        twilio_disconnect_room($room_sid);

        $video_chat_rooms = DB::table(VIDEO_CHAT_ROOMS)->where('unique_name', $unique_name)->first();
        $chat_code = !empty($video_chat_rooms) ? $video_chat_rooms->chat_code : '';
        $unique_name = !empty($video_chat_rooms) ? $video_chat_rooms->unique_name : '';

        // $explode_unique_name = explode("-",$unique_name);
        // $sender_type = $explode_unique_name[0];
        // $sender_id = $explode_unique_name[1];
        // $receiver_type = $explode_unique_name[2];
        // $receiver_id = $explode_unique_name[3];
        $sender_type = $video_chat_rooms->sender_type;
        $sender_id = $video_chat_rooms->sender_id;
        $receiver_type = $video_chat_rooms->receiver_type;
        $receiver_id = $video_chat_rooms->receiver_id;

        if ($sender_id == $denied_by && $sender_type == $denied_by_type) {

            if ($receiver_type == 'mentee') {
                $receiver_data = DB::table(MENTEE)->where('id', $receiver_id)->first();
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            } else if ($receiver_type == 'mentor') {
                $receiver_data = DB::table(MENTOR)->where('id', $receiver_id)->first();
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            }

            if ($sender_type == 'mentee') {
                $sender_data = DB::table(MENTEE)->select('firstname', 'lastname')->where('id', $sender_id)->first();
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;

            } else if ($sender_type == 'mentor') {
                $sender_data = DB::table(MENTOR)->select('firstname', 'lastname')->where('id', $sender_id)->first();
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;
            }
        } elseif ($receiver_id == $denied_by && $receiver_type == $denied_by_type) {
            if ($sender_type == 'mentee') {
                $receiver_data = DB::table(MENTEE)->where('id', $sender_id)->first();
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            } else if ($sender_type == 'mentor') {
                $receiver_data = DB::table(MENTOR)->where('id', $sender_id)->first();
                $receiver_device_type = !empty($receiver_data->device_type) ? $receiver_data->device_type : '';
                $receiver_firebase_id = !empty($receiver_data->firebase_id) ? $receiver_data->firebase_id : '';
                $receiver_voip_device_token = !empty($receiver_data->voip_device_token) ? $receiver_data->voip_device_token : '';
            }

            if ($receiver_type == 'mentee') {
                $sender_data = DB::table(MENTEE)->select('firstname', 'lastname')->where('id', $receiver_id)->first();
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;

            } else if ($receiver_type == 'mentor') {
                $sender_data = DB::table(MENTOR)->select('firstname', 'lastname')->where('id', $receiver_id)->first();
                $sender_name = $sender_data->firstname . ' ' . $sender_data->lastname;
            }
        }


        if (!empty($receiver_device_type) && !empty($receiver_firebase_id)) {

            if ($receiver_device_type == 'android') {
                $message = "Video call has been denied";

                $time = time();
                $send_data = array('title' => "Denied video call", 'message' => $message, 'type' => 'denied_call', 'firebase_token' => $receiver_firebase_id, 'unique_name' => $unique_name, 'sender_name' => $sender_name, 'timestamp' => "$time");
                $data_arr = array('meeting_data' => $send_data);

                if ($receiver_device_type == "iOS") {
                    $msg = array('message' => $message, 'title' => "Denied video call", 'sound' => "default");
                    $fields = array('to' => $receiver_firebase_id, 'notification' => $msg, 'data' => $data_arr, 'priority' => "high");

                } else
                    if ($receiver_device_type == "android") {
                        $fields = array('to' => $receiver_firebase_id, 'data' => $send_data, 'priority' => "high"); // For Android
                    }

                $result = sendPushNotificationWithV1($fields);

                if ($result) {
                    if (!empty($result['name'])) {
                        DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['notification_for' => 'denied_chat', 'room_sid' => '', 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => $receiver_device_type, 'receiver_firebase_id' => $receiver_firebase_id]);
                    }
                }

            } else if ($receiver_device_type == 'iOS') {

//                    if (!empty($receiver_voip_device_token)) {
//                        $this->ios_voip_push_denied($receiver_voip_device_token, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid);
//                    }

                $message = "Video call has been denied";

                $time = time();
                $send_data = array('title' => "Denied video call", 'message' => $message, 'type' => 'denied_call', 'firebase_token' => $receiver_firebase_id, 'unique_name' => $unique_name, 'sender_name' => $sender_name, 'timestamp' => "$time");
                $data_arr = array('meeting_data' => $send_data);

                if ($receiver_device_type == "iOS") {
                    $msg = array('message' => $message, 'title' => "Denied video call", 'sound' => "default");
                    $fields = array('to' => $receiver_firebase_id, 'notification' => $msg, 'data' => $data_arr, 'priority' => "high");

                } else if ($receiver_device_type == "android") {
                    $fields = array('to' => $receiver_firebase_id, 'data' => $send_data, 'priority' => "high"); // For Android
                }

                $result = sendPushNotificationWithV1($fields);

                if ($result) {
                    if (!empty($result['name'])) {
                        DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['notification_for' => 'denied_chat', 'room_sid' => '', 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => $receiver_device_type, 'receiver_firebase_id' => $receiver_firebase_id]);
                    }
                }
            }

        }
        return Response::json(['status' => true, 'message' => "Video call has been ended", 'data' => array('room_sid' => $room_sid, 'unique_name' => $unique_name)]);
    }

    private
    function ios_voip_push_connect($voip_device_token, $receiver_accesstoken, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid, $remaining_time)
    {

        $timezone = 'America/New_York';
        date_default_timezone_set($timezone);
        $created_at = date('Y-m-d H:i:s');

        $send_data = array('title' => "Incoming Video call", 'type' => 'video_chat', 'receiver_accesstoken' => $receiver_accesstoken, 'message' => 'Incoming Video call', 'voip_device_token' => $voip_device_token, 'unique_name' => $unique_name, 'sender_name' => $sender_name, 'room_sid' => $room_sid, 'remaining_time' => $remaining_time, 'created_at' => $created_at);

        $data_arr = array('meeting_data' => $send_data);

        if (!empty($voip_device_token)) {
            // $pemfilename = public_path('/videochat_new'.'/pushcert.pem');
            $pemfilename = public_path('/videochat_new' . '/pushcerttwo.pem');
            $message = 'message';
            ////////////////////////////////////////////////////////////////////////////////
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfilename);

            $fp = stream_socket_client(
//                'ssl://gateway.push.apple.com:2195', $err,
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'content-available' => 1,
                'data' => $data_arr
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $voip_device_token) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if ($result) {
                // echo 'Message not delivered' . PHP_EOL;
                DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['room_sid' => $room_sid, 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => 'iOS', 'receiver_firebase_id' => '', 'receiver_voip_device_token' => $voip_device_token]);

            }
            // Close the connection to the server
            fclose($fp);
        }
    }

    private
    function ios_voip_push_disconnect($voip_device_token, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid)
    {
        $send_data = array('title' => "Missed video call", 'message' => "Video call has been cancelled", 'voip_device_token' => $voip_device_token, 'type' => 'miss_call', 'unique_name' => $unique_name, 'sender_name' => $sender_name);

        $data_arr = array('meeting_data' => $send_data);

        if (!empty($voip_device_token)) {
            // $pemfilename = public_path('/videochat_new'.'/pushcert.pem');
            $pemfilename = public_path('/videochat_new' . '/pushcerttwo.pem');
            $message = 'message';
            ////////////////////////////////////////////////////////////////////////////////
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfilename);

            $fp = stream_socket_client(
//                'ssl://gateway.push.apple.com:2195', $err,
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'content-available' => 1,
                'data' => $data_arr
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $voip_device_token) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if ($result) {
                // echo 'Message not delivered' . PHP_EOL;
                DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['notification_for' => 'disconnect_chat', 'room_sid' => $room_sid, 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => 'iOS', 'receiver_firebase_id' => '', 'receiver_voip_device_token' => $voip_device_token]);

            }
            // Close the connection to the server
            fclose($fp);
        }
    }

    private
    function ios_voip_push_denied($voip_device_token, $receiver_id, $receiver_type, $unique_name, $sender_name, $room_sid)
    {
        $send_data = array('title' => "Denied video call", 'message' => "Video call has been denied", 'voip_device_token' => $voip_device_token, 'type' => 'denied_call', 'unique_name' => $unique_name, 'sender_name' => $sender_name);

        $data_arr = array('meeting_data' => $send_data);

        if (!empty($voip_device_token)) {
            // $pemfilename = public_path('/videochat_new'.'/pushcert.pem');
            $pemfilename = public_path('/videochat_new' . '/pushcerttwo.pem');
            $message = 'message';
            ////////////////////////////////////////////////////////////////////////////////
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfilename);

            $fp = stream_socket_client(
//                'ssl://gateway.push.apple.com:2195', $err,
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'content-available' => 1,
                'data' => $data_arr
            );
            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $voip_device_token) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if ($result) {
                // echo 'Message not delivered' . PHP_EOL;
                DB::table(VIDEO_CHAT_PUSH_NOTIFICATION)->insert(['notification_for' => 'denied_chat', 'room_sid' => $room_sid, 'receiver_id' => $receiver_id, 'receiver_type' => $receiver_type, 'receiver_device_type' => 'iOS', 'receiver_firebase_id' => '', 'receiver_voip_device_token' => $voip_device_token]);

            }
            // Close the connection to the server
            fclose($fp);
        }
    }

    public
    function apn_push(Request $request)
    {
        # code...
        $deviceToken = !empty($request->deviceToken) ? $request->deviceToken : '';

        $send_data = array('title' => "Missed video call", 'Video call has been cancelled' => 'Video call has been cancelled', 'type' => 'incoming_call', 'voip_device_token' => $deviceToken, 'unique_name' => 'fgdfgfdg', 'sender_name' => 'fgfgfg', 'room_sid' => 'hhghghgfhgh');

        // $send_data = array('title' => "Missed video call",'message'=>"Video call has been cancelled",'voip_device_token' => $voip_device_token,'type' => 'miss_call', 'unique_name'=>$unique_name, 'sender_name' => $sender_name);

        $data_arr = array('meeting_data' => $send_data);

        if (!empty($deviceToken)) {
            // Put your private key's passphrase here:
            // $passphrase = 'passphrase';
            // $pemfilename = public_path('/videochat_new'.'/pushcert.pem');
            $pemfilename = public_path('/videochat_new' . '/pushcerttwo.pem');
            // $pemfilename = public_path('/videochat'.'/pushcertdev.pem');

            // Put your private key's passphrase here:
            // $passphrase = 'passphrase';

            // Put your alert message here:
            $message = 'message';

            // Put the full path to your .pem file
            // $pemFile = 'pemFile.pem';

            ////////////////////////////////////////////////////////////////////////////////

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfilename);
            // stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
//                'ssl://gateway.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

            echo 'Connected to APNS' . PHP_EOL;

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'content-available' => 1,
                'data' => $data_arr
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            echo '<pre>';
            print_r($body);
            echo '<pre>';
            print_r($payload);
            echo '<pre>';
            print_r($result);

            if (!$result)
                echo 'Message not delivered' . PHP_EOL;
            else
                echo 'Message successfully delivered' . PHP_EOL;

            // Close the connection to the server
            fclose($fp);
        }


    }

    public
    function apn_push_bkp(Request $request)
    {
        $deviceToken = !empty($request->deviceToken) ? $request->deviceToken : '';
        /*if(!empty($deviceToken)){

            // Provide the Host Information.
            $tHost = 'gateway.sandbox.push.apple.com';
            $tPort = 2195;
            // Provide the Certificate and Key Data.
            $tCert = public_path('/videochat'.'/pushcertdev.pem');
            // $tCert = url('public/videochat/').'pushcertdev.pem';
            // Provide the Private Key Passphrase (alternatively you can keep this secrete
            // and enter the key manually on the terminal -> remove relevant line from code).
            // Replace XXXXX with your Passphrase
            // $tPassphrase = 'XXXXX';
            // Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).
            // Replace this token with the token of the iOS device that is to receive the notification.
            // $tToken = 'b3d7a96d5bfc73f96d5bfc73f96d5bfc73f7a06c3b0101296d5bfc73f38311b4';
            // The message that is to appear on the dialog.
            $tAlert = 'You have a LiveCode APNS Message';
            // The Badge Number for the Application Icon (integer >=0).
            $tBadge = 8;
            // Audible Notification Option.
            $tSound = 'default';
            // The content that is returned by the LiveCode "pushNotificationReceived" message.
            $tPayload = 'APNS Message Handled by LiveCode';
            // Create the message content that is to be sent to the device.
            $tBody['aps'] = array (
                'alert' => $tAlert,
                'badge' => $tBadge,
                'sound' => $tSound,
                );
            $tBody ['payload'] = $tPayload;
            // Encode the body to JSON.
            $tBody = json_encode ($tBody);
            // Create the Socket Stream.
            $tContext = stream_context_create ();
            stream_context_set_option ($tContext, 'ssl', 'local_cert', $tCert);
            // Remove this line if you would like to enter the Private Key Passphrase manually.
            // stream_context_set_option ($tContext, 'ssl', 'passphrase', $tPassphrase);
            // Open the Connection to the APNS Server.
            $tSocket = stream_socket_client ('ssl://'.$tHost.':'.$tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $tContext);
            // Check if we were able to open a socket.
            if (!$tSocket)
                exit ("APNS Connection Failed: $error $errstr" . PHP_EOL);
            // Build the Binary Notification.
            $tMsg = chr (0) . chr (0) . chr (32) . pack ('H*', $deviceToken) .  pack ('n', strlen ($tBody)) . $tBody;
            // Send the Notification to the Server.
            $tResult = fwrite ($tSocket, $tMsg, strlen ($tMsg));

            echo '<pre>'; print_r($tMsg);
            echo '<pre>'; print_r($tResult);

            if ($tResult){

                echo '<pre>'; echo 'Delivered Message to APNS' . PHP_EOL;
            }
            else{
                echo 'Could not Deliver Message to APNS' . PHP_EOL;
            }
            // Close the Connection to the Server.
            fclose ($tSocket);

        }*/

        if (!empty($deviceToken)) {
            // Put your private key's passphrase here:
            // $passphrase = 'passphrase';
            $pemfilename = public_path('/videochat' . '/pushcertdev.pem');

            // SIMPLE PUSH
            $body['aps'] = array(
                'alert' => array(
                    'title' => "You have a notification",
                    'body' => "Body of the message",
                ),
                'badge' => 1,
                'sound' => 'default',
            ); // Create the payload body


            ////////////////////////////////////////////////////////////////////////////////

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $pemfilename);
            // stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); // Open a connection to the APNS server
            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            echo 'Connected to APNS' . PHP_EOL;
            $payload = json_encode($body); // Encode the payload as JSON
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload; // Build the binary notification
            $result = fwrite($fp, $msg, strlen($msg)); // Send it to the server

            echo '<pre>';
            print_r($body);
            echo '<pre>';
            print_r($msg);
            echo '<pre>';
            print_r($result);

            if (!$result) {
                echo '<pre>';
                echo 'Message not delivered' . PHP_EOL;
            } else {
                echo '<pre>';
                echo 'Message successfully delivered' . PHP_EOL;
            }
            fclose($fp); // Close the connection to the server
        }


    }

    public
    function apn_push_bkp_30_12(Request $request)
    {
        $deviceToken = !empty($request->deviceToken) ? $request->deviceToken : '';

        if (!defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }
        // open connection
        $http2ch = curl_init();
        curl_setopt($http2ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
        // send push
        $apple_cert = public_path('/videochat' . '/pushcertdev.pem');
        $message = '{"aps":{"action":"message","title":"your_title","body":"your_message_body"}}';
        $http2_server = 'https://api.development.push.apple.com'; // or 'api.push.apple.com' if production
        $app_bundle_id = 'com.app.TakeStockInChildren';
        $status = sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $deviceToken);
        echo $status;
        // close connection
        curl_close($http2ch);

    }
}
