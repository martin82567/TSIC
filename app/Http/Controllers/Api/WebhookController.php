<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function twilio_disconnect_bkp(Request $request)
    {
        // echo 'Hi';

        $data_val = $request->all();

//    $myfile = fopen("twilio-callback.txt", "w") or die("Unable to open file!");
//    fwrite($myfile, json_encode($data_val));
//    fclose($myfile);

        $room_sid = $request->input('RoomSid');
        $StatusCallbackEvent = $request->input('StatusCallbackEvent');

        if ($StatusCallbackEvent == 'room-ended') {

            $duration = get_room_participants($room_sid);

            if ($duration == 0) {
                $status = 'Missed Call';
            } else {
                $status = 'Completed';
            }

            DB::table('video_chat_rooms')->where('room_sid', $room_sid)->update(['duration' => $duration, 'status' => $status]);

            $video_chat_rooms = DB::table('video_chat_rooms')->where('room_sid', $room_sid)->first();
            $chat_code = $video_chat_rooms->chat_code;
            $video_chat_user = DB::table('video_chat_user')->where('chat_code', $chat_code)->first();
            $remaining_time = $video_chat_user->remaining_time;

            $final_remaining = ($remaining_time - $duration);
            $final_remaining = ($final_remaining < 0 ? 0 : $final_remaining);

            /*Weekly record*/

            $incompleted_chats = DB::select(DB::raw("SELECT SUM(duration) AS sum_value FROM video_chat_rooms WHERE chat_code = '" . $chat_code . "' AND is_week_completed = 0 "));

            $used = 0;
            if (!empty($incompleted_chats)) {
                $used = $incompleted_chats[0]->sum_value;
            }

            $start_date = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
            $end_date = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');
            // $end_date = (date('D') != 'Fri') ? date('Y-m-d', strtotime('next Friday')) : date('Y-m-d');

            $exist_week = DB::table('video_chat_week')->where('chat_code', $chat_code)->where('start_date', $start_date)->first();


            if (empty($exist_week)) {
                DB::table('video_chat_week')->insert(['chat_code' => $chat_code, 'start_date' => $start_date, 'end_date' => $end_date, 'used' => $used, 'remaining' => $final_remaining]);
            } else {
                DB::table('video_chat_week')->where('id', $exist_week->id)->update(['used' => $used, 'remaining' => $final_remaining]);
            }

            /*+++++++++++++*/

            DB::table('video_chat_user')->where('chat_code', $chat_code)->update(['remaining_time' => $final_remaining, 'updated_at' => date('Y-m-d H:i:s')]);

        }

    }

    public function twilio_disconnect(Request $request)
    {
        // echo 'Hi';

        $data_val = $request->all();

        $myfile = fopen("twilio-callback.txt", "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($data_val));
        fclose($myfile);

        $room_sid = $request->input('RoomSid');
        $StatusCallbackEvent = $request->input('StatusCallbackEvent');

        if ($StatusCallbackEvent == 'room-ended') {
            $RoomDuration = $request->input('RoomDuration');
            $duration = $RoomDuration;

            // if($duration == 0){
            //   $status = 'Missed Call';
            // }else{
            //   $status = 'Completed';
            // }

            DB::table('video_chat_rooms')->where('room_sid', $room_sid)->update(['duration' => $duration]);

            get_room_participants_new($room_sid);

            $video_chat_rooms = DB::table('video_chat_rooms')->where('room_sid', $room_sid)->first();
            $chat_code = $video_chat_rooms->chat_code;
            $video_chat_user = DB::table('video_chat_user')->where('chat_code', $chat_code)->first();
            $remaining_time = $video_chat_user->remaining_time;

            $final_remaining = ($remaining_time - $duration);
            $final_remaining = ($final_remaining < 0 ? 0 : $final_remaining);

            /*Weekly record*/

            $incompleted_chats = DB::select(DB::raw("SELECT SUM(duration) AS sum_value FROM video_chat_rooms WHERE chat_code = '" . $chat_code . "' AND is_week_completed = 0 "));

            $used = 0;
            if (!empty($incompleted_chats)) {
                $used = $incompleted_chats[0]->sum_value;
            }

            $start_date = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
            $end_date = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');
            // $end_date = (date('D') != 'Fri') ? date('Y-m-d', strtotime('next Friday')) : date('Y-m-d');

            $exist_week = DB::table('video_chat_week')->where('chat_code', $chat_code)->where('start_date', $start_date)->first();


            if (empty($exist_week)) {
                DB::table('video_chat_week')->insert(['chat_code' => $chat_code, 'start_date' => $start_date, 'end_date' => $end_date, 'used' => $used, 'remaining' => $final_remaining]);
            } else {
                DB::table('video_chat_week')->where('id', $exist_week->id)->update(['used' => $used, 'remaining' => $final_remaining]);
            }

            /*+++++++++++++*/
            DB::table('video_chat_user')->where('chat_code', $chat_code)->update(['remaining_time' => $final_remaining, 'updated_at' => date('Y-m-d H:i:s')]);

        }

    }

    public function twilio_chat(Request $request)
    {
        # code...
        $data_val = $request->all();

//    $myfile = fopen("twilio-chat-callback.txt", "w") or die("Unable to open file!");
//    fwrite($myfile, json_encode($data_val));
//    fclose($myfile);

        $EventType = $request->input('EventType');
        $ChannelSid = $request->input('ChannelSid');
        $ClientIdentity = $request->input('ClientIdentity');
        $Body = $request->input('Body');

        $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('channel_sid', $ChannelSid)->first();
        $mentee_staff_chat_codes = DB::table('mentee_staff_chat_codes')->where('channel_sid', $ChannelSid)->first();
        $mentor_staff_chat_codes = DB::table('mentor_staff_chat_codes')->where('channel_sid', $ChannelSid)->first();

        $chat_code = '';
        $thread_table = '';

        if (!empty($mentor_mentee_chat_codes)) {
            $chat_code = $mentor_mentee_chat_codes->code;
            $thread_table = "mentor_mentee_chat_threads";
        }
        if (!empty($mentee_staff_chat_codes)) {
            $chat_code = $mentee_staff_chat_codes->code;
            $thread_table = "mentee_staff_chat_threads";
        }
        if (!empty($mentor_staff_chat_codes)) {
            $chat_code = $mentor_staff_chat_codes->code;
            $thread_table = "mentor_staff_chat_threads";
        }


        if ($EventType == 'onMessageSent') {
            $exp = explode("_", $ClientIdentity);
            $from_where = $exp[0];
            $sender_id = $exp[1];

            if ($thread_table == "mentor_mentee_chat_threads") {

                if ($sender_id == $mentor_mentee_chat_codes->mentor_id) {
                    $receiver_id = $mentor_mentee_chat_codes->mentee_id;
                } else if ($sender_id == $mentor_mentee_chat_codes->mentee_id) {
                    $receiver_id = $mentor_mentee_chat_codes->mentor_id;
                }

            }

            if ($thread_table == "mentee_staff_chat_threads") {

                if ($sender_id == $mentee_staff_chat_codes->mentee_id) {
                    $receiver_id = $mentee_staff_chat_codes->staff_id;
                } else if ($sender_id == $mentee_staff_chat_codes->staff_id) {
                    $receiver_id = $mentee_staff_chat_codes->mentee_id;
                }

            }

            if ($thread_table == "mentor_staff_chat_threads") {

                if ($sender_id == $mentor_staff_chat_codes->mentor_id) {
                    $receiver_id = $mentor_staff_chat_codes->staff_id;
                } else if ($sender_id == $mentor_staff_chat_codes->staff_id) {
                    $receiver_id = $mentor_staff_chat_codes->mentor_id;
                }

            }

            if (!empty($chat_code) && !empty($thread_table)) {
                DB::table($thread_table)->insert(['chat_code' => $chat_code, 'sender_id' => $sender_id, 'receiver_id' => $receiver_id, 'message' => $Body, 'from_where' => $from_where, 'receiver_is_read' => 0, 'created_date' => date('Y-m-d H:i:s')]);
            }


        }

        if ($EventType == 'onMemberUpdated') {
            $exp = explode("_", $ClientIdentity);

            $receiver_id = $exp[1];
            if (!empty($chat_code) && !empty($thread_table)) {
                DB::table($thread_table)->where('chat_code', $chat_code)->where('receiver_id', $receiver_id)->where('receiver_is_read', 0)->update(['receiver_is_read' => 1]);
            }
        }


    }

    public function zoom(Request $request)
    {
        $data = $request->all();

        // Ensure that the request has been made with the correct event type
        if ($data['event'] === 'endpoint.url_validation') {
            // Replace 'ZOOM_WEBHOOK_SECRET_TOKEN' with your actual secret token
            $secretToken = 'BuaFVRDPQqupoOgDi7PKeQ';

            // Retrieve the plain token from the request body
            $plainToken = $data['payload']['plainToken'];

            // Generate the HMAC using SHA256
            $encryptedToken = hash_hmac('sha256', $plainToken, $secretToken);

            // Set the response status and JSON data
            return response()->json([
                'plainToken' => $plainToken,
                'encryptedToken' => $encryptedToken
            ], '200');
        }
//        Log::info(json_encode($data['event']));

        if ($data['event'] == 'session.ended') {
            $object = $data['payload']['object'];
            $unique_name = $object['session_name'];
            $start_time = Carbon::parse($object['start_time']);
            $end_time = Carbon::parse($object['end_time']);
            $diff = $end_time->diffInSeconds($start_time);

            if ($diff == 0) {
                $status = 'Missed Call';
            } else {
                $status = 'Completed';
            }

            DB::table('video_chat_rooms')->where('unique_name', $unique_name)->update(['duration' => $diff, 'status' => $status]);

//            get_room_participants_new($room_sid);

            $video_chat_rooms = DB::table('video_chat_rooms')->where('unique_name', $unique_name)->first();
            $chat_code = $video_chat_rooms->chat_code;
            $video_chat_user = DB::table('video_chat_user')->where('chat_code', $chat_code)->first();
            $remaining_time = $video_chat_user->remaining_time;

            $final_remaining = ($remaining_time - $diff);
            $final_remaining = ($final_remaining < 0 ? 0 : $final_remaining);

            /*Weekly record*/

            $incompleted_chats = DB::select(DB::raw("SELECT SUM(duration) AS sum_value FROM video_chat_rooms WHERE chat_code = '" . $chat_code . "' AND is_week_completed = 0 "));

            $used = 0;
            if (!empty($incompleted_chats)) {
                $used = $incompleted_chats[0]->sum_value;
            }

            $start_date = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
            $end_date = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');
            // $end_date = (date('D') != 'Fri') ? date('Y-m-d', strtotime('next Friday')) : date('Y-m-d');

            $exist_week = DB::table('video_chat_week')->where('chat_code', $chat_code)->where('start_date', $start_date)->first();


            if (empty($exist_week)) {
                DB::table('video_chat_week')->insert(['chat_code' => $chat_code, 'start_date' => $start_date, 'end_date' => $end_date, 'used' => $used, 'remaining' => $final_remaining]);
            } else {
                DB::table('video_chat_week')->where('id', $exist_week->id)->update(['used' => $used, 'remaining' => $final_remaining]);
            }

            /*+++++++++++++*/
            DB::table('video_chat_user')->where('chat_code', $chat_code)->update(['remaining_time' => $final_remaining, 'updated_at' => date('Y-m-d H:i:s')]);
        }


    }

    public function zoom_recording(Request $request)
    {
        $data = $request->all();

        // Ensure that the request has been made with the correct event type
        if ($data['event'] === 'endpoint.url_validation') {
            // Replace 'ZOOM_WEBHOOK_SECRET_TOKEN' with your actual secret token
            $secretToken = 'BuaFVRDPQqupoOgDi7PKeQ';

            // Retrieve the plain token from the request body
            $plainToken = $data['payload']['plainToken'];

            // Generate the HMAC using SHA256
            $encryptedToken = hash_hmac('sha256', $plainToken, $secretToken);

            // Set the response status and JSON data
            return response()->json([
                'plainToken' => $plainToken,
                'encryptedToken' => $encryptedToken
            ], '200');
        }

        if ($data['event'] == 'session.recording_completed') {
//            Log::info('Inside recording event');
            $object = $data['payload']['object'];
            $session_id = $object ? $object['session_id'] : '';
            $unique_name = $object['session_name'];
            $recording_url = $object ? $object['recording_files'][0]['download_url'] : '';
            if ($unique_name) {
//                Log::info('Inside final loop');
                DB::table('video_chat_rooms')->where('unique_name', $unique_name)->update(['recording_url' => $recording_url, 'session_id' => $session_id]);
            }
        }
    }
}
