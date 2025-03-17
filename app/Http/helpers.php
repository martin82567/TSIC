<?php

# Live ..

use App\Http\Controllers\Salesforce\MiddlewareController;
use App\Services\FCMServiceProvider;
use App\Services\SalesforceDBService;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_single_data_id($table, $id)
{
    $data = array();
    $data = DB::table($table)->where('id', $id)->first();
    return $data;

}

function unread_chat_message_staff($type = '', $table = '', $staff_id, $sender_id)
{

    $data = DB::table($table)->where('sender_id', $sender_id)->where('receiver_id', $staff_id)->where('receiver_is_read', 0)->count();

    return $data;

}

function total_unread_chat_message_staff($type = '', $table = '', $staff_id)
{
    $data = DB::table($table)->where('from_where', $type)->where('receiver_id', $staff_id)->where('receiver_is_read', 0)->count();
    return $data;
}

function getStarToken()
{
    $url = 'https://unison.tsic.org/api/Authentication';
    $fields = array(
        'username' => urlencode('webservices'),
        'password' => urlencode('8tQPNoO5tA')
    );
    $fields_string = '';
    //url-ify the data for the POST
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    //execute post
    $token = curl_exec($ch);
    //close connection
    curl_close($ch);

    $token_arr = json_decode($token);
    // echo '<pre>'; print_r($token_arr->token); die;

    return $token_arr->token;
}

function loginToSalesforce()
{
//    $url = env('SF_MIDDLEWARE_BASE_URL') . "/login";
//
//    $httpClient = new GuzzleClient();
//
//    $response = $httpClient->post($url, [
//        'headers' => [
//            'Accept' => 'application/json'
//        ]
//    ]);

    $response = app(MiddlewareController::class)->login();

    $responseObject = json_decode(json_encode($response));
    return $responseObject->original;

}

function getMentorsForOffice($office, $token, $baseURL)
{

    $response = app(MiddlewareController::class)->getMentorsByOfficeFunc($token, $baseURL, $office);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->data;
}

function getSchoolsForOffice($affiliateId, $token, $baseURL)
{

    $response = app(MiddlewareController::class)->getSchoolsForOfficeFunc($token, $baseURL, $affiliateId);

//    $url = env('SF_MIDDLEWARE_BASE_URL') . "/getSchoolsForOffice?affiliateId=" . $affiliateId;
//
//    $httpClient = new GuzzleClient();
//
//    $response = $httpClient->get($url, [
//        'headers'   =>  [
//            'Accept'   => 'application/json',
//            'token'   => $token,
//            'baseURL'   => $baseURL,
//        ]
//    ]);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->data;
}

function getStudentsForOffice($office, $token, $baseURL)
{
    $response = app(MiddlewareController::class)->getStudentsByOfficeFunc($token, $baseURL, $office);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->data;
}

function getScholarsForOffice($office, $token, $baseURL)
{
    $response = app(MiddlewareController::class)->getScholarsByOfficeFunc($token, $baseURL, $office);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->data;
}

function getStudentMatchForMentor($studentId, $token, $baseURL)
{
    $response = app(MiddlewareController::class)->getStudentMentorMatchFunc($token, $baseURL, $studentId);

//    $url = env('SF_MIDDLEWARE_BASE_URL') . "/getStudentMentorMatch?studentId=" . $studentId;
//
//    $httpClient = new GuzzleClient();
//
//    $response = $httpClient->get($url, [
//        'headers'   =>  [
//            'Accept'   => 'application/json',
//            'token'   => $token,
//            'baseURL'   => $baseURL,
//        ]
//    ]);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->data;
}

function getProgramsAndServicesForAgency($agencyId, $token, $baseURL)
{
    $url = env('SF_MIDDLEWARE_BASE_URL') . "/getProgramAndServicesForAgency?agencyId=" . $agencyId;

    $httpClient = new GuzzleClient();

    $response = $httpClient->get($url, [
        'headers' => [
            'Accept' => 'application/json',
            'token' => $token,
            'baseURL' => $baseURL,
        ]
    ]);

    $responseObject = json_decode($response->getBody());

    return $responseObject->data;
}

function createSession($session, $token, $baseURL)
{
    $url = env('SF_MIDDLEWARE_BASE_URL') . "/createSession";

    $httpClient = new GuzzleClient();

    $response = $httpClient->post($url, [
        'headers' => [
            'Accept' => 'application/json',
            'token' => $token,
            'baseURL' => $baseURL,
        ],
        'json' => $session
    ]);

    $responseObject = json_decode($response->getBody());

    return $responseObject->data;
}

function getCountByStudentId($studentId)
{
    $settings = get_single_data_id(SETTINGS, 1);
    $session_start_month = $settings->session_start_month;
    $session_start_day = $settings->session_start_day;
    if (date('m') >= $session_start_month) {
        $y = date('Y');
        $m = $session_start_month;
        $d = $session_start_day;
        $startDate = $y . '-' . $m . '-' . $d;
    } else {
        $y = (date('Y') - 1);
        $m = $session_start_month;
        $d = $session_start_day;
        $startDate = $y . '-' . $m . '-' . $d;
    }
    $endDate = date('Y-m-d');

    $loginResponse = loginToSalesforce();
    $token = $loginResponse->token;
    $baseURL = $loginResponse->baseURL;
    $request = new \Illuminate\Http\Request();
    $request->headers->set('token', $token);
    $request->headers->set('baseURL', $baseURL);
    $request->merge([
        'studentId' => $studentId,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);
    $response = app(MiddlewareController::class)->getStudentSessionCount($request, app(SalesforceDBService::class));
//    $url = env('SF_MIDDLEWARE_BASE_URL') . "/getStudentSessionCount?studentId=$studentId&startDate=$startDate&endDate=$endDate";
//
//    $httpClient = new GuzzleClient();
//
//    $response = $httpClient->get($url, [
//        'headers' => [
//            'Accept' => 'application/json',
//            'token' => $token,
//            'baseURL' => $baseURL,
//        ]
//    ]);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->original->data;
}

function getMentorStudentsCount($mentor_id)
{
    $settings = get_single_data_id(SETTINGS, 1);
    $session_start_month = $settings->session_start_month;
    $session_start_day = $settings->session_start_day;
    if (date('m') >= $session_start_month) {
        $y = date('Y');
        $m = $session_start_month;
        $d = $session_start_day;
        $startDate = $y . '-' . $m . '-' . $d;
    } else {
        $y = (date('Y') - 1);
        $m = $session_start_month;
        $d = $session_start_day;
        $startDate = $y . '-' . $m . '-' . $d;
    }
    $endDate = date('Y-m-d');

    $loginResponse = loginToSalesforce();
    $token = $loginResponse->token;
    $baseURL = $loginResponse->baseURL;
    $request = new \Illuminate\Http\Request();
    $request->headers->set('token', $token);
    $request->headers->set('baseURL', $baseURL);
    $request->merge([
        'mentorId' => $mentor_id,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);

    $response = app(MiddlewareController::class)->getMentorSessionCount($request, app(SalesforceDBService::class));
//    $url = env('SF_MIDDLEWARE_BASE_URL') . "/getMentorSessionCount?mentorId=$mentor_id&startDate=$startDate&endDate=$endDate";
//
//    $httpClient = new GuzzleClient();
//
//    $response = $httpClient->get($url, [
//        'headers' => [
//            'Accept' => 'application/json',
//            'token' => $token,
//            'baseURL' => $baseURL,
//        ]
//    ]);

    $responseObject = json_decode(json_encode($response));

    return $responseObject->original->data;
}

function email_send($to, $subject, $content)
{
    require_once('vendor/autoload.php');
    require_once('sendgrid-php/sendgrid-php.php');
    // die('Hi');

    $sendgrid_apikey = config('app.SENDGRID_API_KEY');

    $email = new SendGrid\Mail\Mail();
    $email->setFrom("info@em1219.appemailservice.com", "TSIC");
    $email->setSubject($subject);
    $email->addTo($to);
    $email->addContent("text/html", $content);
    $sendgrid = new SendGrid($sendgrid_apikey);
    try {
        $response = $sendgrid->send($email);

        // echo '<pre>'; print $to . "\n";
        // echo '<pre>'; print $response->statusCode() . "\n";
        // echo '<pre>'; print_r($response->headers());
        // echo '<pre>'; print $response->body() . "\n";
    } catch (Exception $e) {
        // echo 'Caught exception: '. $e->getMessage() ."\n";
    }

}

function email_send_old($to, $subject, $content)
{

    $message = array();

    try {
        Mail::send([], [], function ($message) use ($to, $subject, $content) {
            $message->to($to)->subject($subject)->from('no_reply@seeandsend.info')->setBody($content, 'text/html');
        });

        // echo 'Original';

    } catch (Exception $e) {
        $ch_keyword = curl_init();
        $curlConfig = array(
            CURLOPT_URL => "http://64.91.238.169/~theaquariousdev/DONOT_DELETE/tsic_mail.php",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => array(
                'to' => $to,
                'subject' => $subject,
                'content' => $content,
                'from' => 'no_reply@seeandsend.info',
            )
        );
        curl_setopt_array($ch_keyword, $curlConfig);
        $result = curl_exec($ch_keyword);
        curl_close($ch_keyword);

    }

}

function get_affiliate_staff_ids($affiliate_id)
{
    $data = DB::table('admins')->select('id', 'parent_id')->where('parent_id', $affiliate_id)->where('is_active', 1)->get()->toarray();

    $ids = array();
    if (!empty($data)) {
        foreach ($data as $d) {
            $ids[] = $d->id;
        }
    }
    $ids[] = $affiliate_id;

    return $ids;

}

function get_keyword_access($affiliate_id)
{
    $user_ids = get_affiliate_staff_ids($affiliate_id);
    $user_id_imp = implode(",", $user_ids);

    $data = DB::table('admins')->select('id', 'name', 'email', 'parent_id', 'timezone', 'type')->whereIn('id', $user_ids)->where('is_allow_keyword_notification', 1)->get()->toarray();

    return $data;


}

function mentor_last_activity($user_id)
{
    $mentor = get_single_data_id('mentor', $user_id);
    $affiliate_id = $mentor->assigned_by;
    $affiliate_data = get_single_data_id('admins', $affiliate_id);
    $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : '';
    // echo '<pre>'; echo $timezone;
    date_default_timezone_set($timezone);
    $cur_date = date('Y-m-d H:i:s');
    // echo '<pre>'; echo $cur_date;
    DB::table('mentor')->where('id', $user_id)->update(['last_activity_at' => $cur_date]);
}

function mentee_last_activity($user_id)
{
    $mentee = get_single_data_id('mentee', $user_id);
    $affiliate_id = $mentee->assigned_by;
    $affiliate_data = get_single_data_id('admins', $affiliate_id);
    $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : '';
    // echo '<pre>'; echo $timezone;
    date_default_timezone_set($timezone);
    $cur_date = date('Y-m-d H:i:s');
    // echo '<pre>'; echo $cur_date;
    DB::table('mentee')->where('id', $user_id)->update(['last_activity_at' => $cur_date]);
}

function mentor_session_log_count($mentor_id)
{
    $data = DB::table('mentor_session_log_count')->where('mentor_id', $mentor_id)->first();
    $count = 0;
    $label = '';
    $label_no = 1;
    if (!empty($data)) {
        $count = $data->count;
        if (($count >= 0 && $count <= 4)) {
            $label = 'No star';
            $label_no = 1;
        } else if (($count >= 5 && $count <= 9)) {
            $label = 'Bronze';
            $label_no = 2;
        } else if (($count >= 10 && $count <= 14)) {
            $label = 'Silver';
            $label_no = 3;
        } else if (($count >= 15)) {
            $label = 'Gold Star';
            $label_no = 4;
        }
    }
    return ['count' => $count, 'label' => $label, 'label_no' => $label_no];
}

function get_standard_datetime($date)
{
    $data = date('m-d-y h:i a ', strtotime($date));
    return $data;
}

function getFiscalYear($date)
{
    // Create a DateTime object from the given date
    $dateTime = new DateTime($date);

    // Get the year from the given date
    $year = $dateTime->format("Y");

    // Create a DateTime object for July 1st of the given year
    $fiscalStart = new DateTime("$year-07-01");

    // Compare the given date with July 1st
    if ($dateTime >= $fiscalStart) {
        // If the date is on or after July 1st, the fiscal year is next year
        return $year + 1;
    } else {
        // If the date is before July 1st, the fiscal year is the current year
        return $year;
    }
}

function get_standard_datetime_with_timezone($date)
{
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC')->timezone('America/New_York')->format("m-d-y h:i a");
    return $date;
    /* $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC');
     $date->setTimezone('America/New_York');
     return $date->format("m-d-y h:i a");*/
}

function twilio_messaging($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);

    require_once('Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;
    $twilio_number = config('app.twilio_number');


    // try{
    //     $client = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);
    //     $message = $client->messages
    //           ->create("+918961741161", // to
    //                    ["body" => "Test message for TSIC", "from" => $twilio_number]
    //           );

    //     print($message->sid);

    // }catch(\Twilio\Exceptions\RestException $e){
    //     echo $e->getCode() . ' : ' . $e->getMessage()."<br>";
    // }

    /*Fetch Room*/
    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);
    $recordings = $twilio->video->v1->rooms($room_sid)->recordings->read([], 100);
    $linkarr = array();
    if (!empty($recordings)) {
        foreach ($recordings as $record) {
            $roomSid = $room_sid;
            $recordingSid = $record->sid;
            $uri = "https://video.twilio.com/v1/" .
                "Rooms/$roomSid/" .
                "Recordings/$recordingSid/" .
                "Media/";
            $response = $twilio->request("GET", $uri);
            // echo '<pre>'; print_r($response->getContent());
            if (!empty($response->getContent())) {
                $mediaLocation = $response->getContent()["redirect_to"];
                $linkarr[] = $mediaLocation;
            }

        }
    }
    return ['linkarr' => $linkarr];

}

function twilio_access_token($identity, $roomname)
{
    $settings = get_single_data_id(SETTINGS, 1);
    // require_once('/path/to/vendor/autoload.php'); // Loads the library
    require_once('Twilio/autoload.php');
    // use Twilio\Jwt\AccessToken;
    // use Twilio\Jwt\Grants\VideoGrant;

    // Substitute your Twilio AccountSid and ApiKey details
    $accountSid = $settings->twilio_account_sid;
    $apiKeySid = $settings->twilio_apiKeySid;
    $apiKeySecret = $settings->twilio_apiKeySecret;

    // $identity = 'tsic-video-chat';

    // Create an Access Token
    $token = new Twilio\Jwt\AccessToken(
        $accountSid,
        $apiKeySid,
        $apiKeySecret,
        3600,
        $identity
    );

    // Grant access to Video
    $grant = new Twilio\Jwt\Grants\VideoGrant();
    $grant->setRoom($roomname);
    // $grant->setRoom('tsic-video-room');
    $token->addGrant($grant);

    // Serialize the token as a JWT
    return $token->toJWT();
}


function create_compositions($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('twilio-php-main/src/Twilio/autoload.php');


    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;

    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);


    /*$compositions = $twilio->video->compositions
        ->read([
        'status' => 'completed'
        ]);

    foreach ($compositions as $c) {
        echo $c->sid;echo '<pre>';
    }
    die;*/
    $room = $twilio->video->v1->rooms($room_sid)->fetch();
    try {
        $composition = $twilio->video->compositions->create($room_sid, [
            'audioSources' => '*',
            'videoLayout' => array(
                'grid' => array(
                    'video_sources' => array('*')
                )
            ),
            'statusCallback' => 'https://mentorappdev.tsic.org/',
            'format' => 'mp4'
        ]);

        return ['composition_id' => $composition->sid, 'room' => $room];
        // return $composition->sid;

    } catch (\Twilio\Rest\Client\Exception $e) {

    }


}

function getmedia($id)
{
    $settings = get_single_data_id(SETTINGS, 1);

    require_once('twilio-php-main/src/Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;

    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);
    $compositionSid = $id;
    $uri = "https://video.twilio.com/v1/Compositions/$compositionSid/Media?Ttl=3600";
    $response = $twilio->request("GET", $uri);
    // $mediaLocation = $response->getContent();
    $mediaLocation = $response->getContent()["redirect_to"];

    // For example, download media to a local file
    // file_put_contents("myFile1.mp4", fopen($mediaLocation, 'r'));
    // echo '<pre>'; print_r($mediaLocation);
    return $mediaLocation;
}

function fetch_room($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;

    /*Fetch Room*/
    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);
    $room = $twilio->video->v1->rooms($room_sid)->fetch();

    $linkarr = array();
    if ($room->status === 'completed') {

        /*Retrieve Recording*/
        $recordings = $twilio->video->v1->rooms($room_sid)->recordings->read([], 100);
        if (!empty($recordings)) {
            foreach ($recordings as $record) {
                $roomSid = $room_sid;
                $recordingSid = $record->sid;
                $uri = "https://video.twilio.com/v1/" .
                    "Rooms/$roomSid/" .
                    "Recordings/$recordingSid/" .
                    "Media/";
                $response = $twilio->request("GET", $uri);
                if (!empty($response->getContent())) {
                    $mediaLocation = $response->getContent()["redirect_to"];
                    $linkarr[] = $mediaLocation;
                }
            }
        }
    }

    // $room->video_url = $mediaLocation;
    // dd($linkarr);
    // die;
    return ['linkarr' => $linkarr, 'room' => $room];
}


function twilio_create_video_room($uniqueName)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;


    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);
    $room = $twilio->video->v1->rooms->create(["uniqueName" => $uniqueName, 'StatusCallback' => url('api/webhook/twilio_disconnect'), 'StatusCallbackMethod' => 'POST']);


    return $room->sid;
    // print($room->sid);
}

function twilio_disconnect_room($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;
    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);

    try {
        $complete_room = $twilio->video->v1->rooms($room_sid)->update("completed");
        // $room = $twilio->video->v1->rooms($room_sid)->fetch();

        // $duration = $room->duration;

        // DB::table('video_chat_rooms')->where('room_sid',$room_sid)->update(['duration'=>$duration]);

        // $video_chat_rooms = DB::table('video_chat_rooms')->where('room_sid',$room_sid)->first();
        // $chat_code = $video_chat_rooms->chat_code;
        // $video_chat_user = DB::table('video_chat_user')->where('chat_code',$chat_code)->first();
        // $remaining_time = $video_chat_user->remaining_time;

        // $final_remaining = ($remaining_time - $duration);

        // DB::table('video_chat_user')->where('chat_code',$chat_code)->update(['remaining_time'=>$final_remaining,'updated_at'=>date('Y-m-d H:i:s')]);

    } catch (Exception $e) {
        // echo $e->getCode() . ' : ' . $e->getMessage()."<br>";
        // die;
        return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => array()]);

    }


}

function get_room_participants($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('twilio-php-main/src/Twilio/autoload.php');
    // require_once('Twilio/autoload.php');

    $apiKeySid = $settings->twilio_apiKeySid;
    $apiKeySecret = $settings->twilio_apiKeySecret;

    $twilio = new Twilio\Rest\Client($apiKeySid, $apiKeySecret);


    $participants = $twilio->video->rooms($room_sid)->participants->read();
    // $participants = $twilio->video->rooms($room_sid)->participants->read(array("status" => "connected"));

    foreach ($participants as $p) {
        echo '<pre>';
        echo 'Participant Sid:- ' . $p->sid;
        echo '<pre>';
        echo 'Participant Identity:- ' . $p->identity;
        if ($p->status != 'disconnected') {
            $twilio->video->rooms($room_sid)->participants($p->identity)->update(array("status" => "disconnected"));
        }

        echo '<pre>';
        echo 'Participant Status:- ' . $p->status;
        echo '<pre>';
        echo 'Participant Duration:- ' . $p->duration;
        echo '<pre>';
        echo '---------------------';
    }

    die;


    /*++++++++++++++++++++++++++*/

    $participants = $twilio->video->rooms($room_sid)
        ->participants->read(array("status" => "disconnected"));

    // echo '<pre>'; print_r($participants);
    $duration_arr = array();

    $duration = 0;

    if (!empty($participants)) {
        foreach ($participants as $participant) {
            echo '<pre>';
            echo 'Participant Id:- ' . $participant->sid;
            echo '<pre>';
            echo 'Participant Duration:- ' . $participant->duration;
            // if(count($participants) == 2){
            //     $duration_arr[] = $participant->duration;
            //     // echo '<pre>'; echo 'Yes';
            // }
        }

    }

    // echo '<pre>'; print_r($duration_arr);
    // echo '<pre>'; echo min($duration_arr);

    // if(!empty($duration_arr)){
    //  $duration = min($duration_arr);
    // }

    // return $duration;


}

function get_room_participants_new_bkp($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('twilio-php-main/src/Twilio/autoload.php');
    // require_once('Twilio/autoload.php');

    $apiKeySid = $settings->twilio_apiKeySid;
    $apiKeySecret = $settings->twilio_apiKeySecret;

    date_default_timezone_set('America/New_York');
    $created_at = date('Y-m-d H:i:s');

    $twilio = new Twilio\Rest\Client($apiKeySid, $apiKeySecret);

    $all_participants = $twilio->video->rooms($room_sid)->participants->read();
    // $participants = $twilio->video->rooms($room_sid)->participants->read(array("status" => "connected"));
    if (!empty($all_participants)) {
        foreach ($all_participants as $p) {

            if ($p->status != 'disconnected') {
                $twilio->video->rooms($room_sid)->participants($p->identity)->update(array("status" => "disconnected"));
            }

        }
    }

    $disconnected_participants = $twilio->video->rooms($room_sid)->participants->read(array("status" => "disconnected"));


    if (!empty($disconnected_participants)) {
        foreach ($disconnected_participants as $participant) {

            DB::table('video_chat_participants')->insert(['room_sid' => $room_sid, 'participant_sid' => $participant->sid, 'participant_identity' => $participant->identity, 'duration' => $participant->duration, 'created_at' => $created_at, 'updated_at' => $created_at]);

        }

    }

    if (count($disconnected_participants) <= 1) {
        $status = "Missed Call";
    } else {
        $status = "Completed";
    }


    DB::table('video_chat_rooms')->where('room_sid', $room_sid)->update(['status' => $status]);

}

function get_room_participants_new($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('twilio-php-main/src/Twilio/autoload.php');
    // require_once('Twilio/autoload.php');

    $apiKeySid = $settings->twilio_apiKeySid;
    $apiKeySecret = $settings->twilio_apiKeySecret;

    $twilio = new Twilio\Rest\Client($apiKeySid, $apiKeySecret);

    $participants = $twilio->video->rooms($room_sid)
        ->participants->read(array("status" => "disconnected"));

    // echo '<pre>'; print_r($participants);

    date_default_timezone_set('America/New_York');
    $created_at = date('Y-m-d H:i:s');

    if (!empty($participants)) {
        foreach ($participants as $participant) {

            DB::table('video_chat_participants')->insert(['room_sid' => $room_sid, 'participant_sid' => $participant->sid, 'participant_identity' => $participant->identity, 'duration' => $participant->duration, 'created_at' => $created_at, 'updated_at' => $created_at]);

        }

    }

    if (count($participants) <= 1) {
        $status = "Missed Call";
    } else {
        $status = "Completed";
    }

    DB::table('video_chat_rooms')->where('room_sid', $room_sid)->update(['status' => $status]);

}

function twilio_delete_recordings($room_sid)
{
    $settings = get_single_data_id(SETTINGS, 1);
    require_once('twilio-php-main/src/Twilio/autoload.php');
    // require_once('Twilio/autoload.php');

    $twilio_account_sid = $settings->twilio_account_sid;
    $twilio_auth_token = $settings->twilio_auth_token;
    $twilio = new Twilio\Rest\Client($twilio_account_sid, $twilio_auth_token);

    $recordings = $twilio->video->v1->rooms($room_sid)
        ->recordings
        ->read([], 20);

    $recording_id_arr = array();
    foreach ($recordings as $record) {
        // print($record->sid);
        // $recording_id_arr[] =  $record->sid;
        $twilio->video->v1->recordings($record->sid)->delete();
    }
    // echo '<pre>'; print_r($recording_id_arr);
    // if(!empty($recording_id_arr)){
    //     $recording_ids = implode(",",$recording_id_arr);
    //     $twilio->video->v1->recordings($recording_ids)->delete();
    // }


    // return $recordings;
}


function schedule_session_count($user_type = '', $user_id)
{
    $data = DB::table(MEETING_NOTIFICATION)->where('user_type', $user_type)->where('user_id', $user_id)->where('is_read', 0)->count();
    return $data;
}

function meeting_event_notification($user_type, $user_id, $meeting_id, $notification_for = '')
{
    if ($user_type == 'mentor') {
        $user_data = DB::table(MENTOR)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    } else if ($user_type == 'mentee') {
        $user_data = DB::table(MENTEE)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    }

    $schedule_session_count = schedule_session_count($user_type, $user_id);
    $user_total_chat_count = user_total_chat_count($user_type, $user_id);
    $user_total_unread_goaltask_count = user_total_unread_goaltask_count($user_type, $user_id);
    $result = '';
    if (!empty($firebase_id) && !empty($device_type)) {
        $message = "Schedule Session Notification";
        if (!empty($notification_for)) {
            if ($notification_for == 'reschedule_request') {
                $message = "Your Mentee has denied the mentor session request and is requesting to reschedule. Please review and update the the session request in the 'Session Management' navigation on the Take Stock App profile screen to reschedule the session.";
            } else if ($notification_for == 'accept') {
                $message = "Your Mentee has accepted their scheduled session.  You can review all your sessions in the 'Session Management' navigation on the Take Stock App profile screen.";
            } else if ($notification_for == 'deny_reschedule') {
                $message = "Your request for Resheduling an existing Mentor Session has been denied. Please review the 'Session Management' navigation on the Take Stock App profile screen to see if the mentor session has been rescheduled.";
            } else if ($notification_for == 'cancel') {
                $message = "An existing Mentor Session has been cancelled. Please review the 'Session Management' navigation on the Take Stock App profile screen to see if the mentor session has been rescheduled.";
            } else if ($notification_for == 'reschedule') {
                $message = "An existing Mentor Session has been rescheduled. Please review and confirm the new mentor session invite in the 'Session Management' navigation on the Take Stock App profile screen.";
            } else if ($notification_for == 'create') {
                $message = "A new session has been created. Please review and confirm the new mentor session invite in the 'Session Management' navigation on the Take Stock App profile screen.";
            } else if ($notification_for == 'update') {
                $message = "An existing session has been updated. Please review and confirm the new mentor session invite in the 'Session Management' navigation on the Take Stock App profile screen.";
            } else if ($notification_for == 'delete') {
                $message = "An existing session has been deleted. Please check the deleted session in 'Session Management' navigation on the Take Stock App profile screen.";
            }
        }
        $unreadCount = $schedule_session_count + $user_total_unread_goaltask_count;
        $send_data = array('title' => $message,
            'type' => 'meeting_notification',
            'meeting_id' => "$meeting_id",
            'message' => $message,
            'firebase_token' => $firebase_id,
            'unread_chat' => "$user_total_chat_count",
            'unread_task' => "$unreadCount");
        $data_arr = array('meeting_data' => json_encode($send_data));

        if ($device_type == "iOS") {

            $msg = array('message' => $message, 'title' => $message, 'sound' => "default", 'badge' => ($schedule_session_count + $user_total_chat_count + $user_total_unread_goaltask_count));
            // $msg = array('body' => $message,'title' => "Test"  );
            // $alert = array('alert' => $msg);
            // $fields = array('to' => $firebase_token,'aps' => $alert,'data' => $data_arr, 'priority'=>'high'); // For IOS

            $fields = array('to' => $firebase_id, 'notification' => $msg, 'data' => $data_arr);


        } else if ($device_type == "android") {

            $fields = array('to' => $firebase_id, 'data' => $send_data); // For Android
        }

        $result = sendPushNotificationWithV1($fields);
        // echo '<pre>'; print_r($fields);
        // echo '<pre>'; print_r($result_arr);
        if (!empty($result['name'])) {
            return json_encode($result);
        } else {
            return $result;
        }


    } else {
        return $result;
    }
}

function user_total_chat_count($user_type, $user_id)
{
    $unread_chat = 0;
    if ($user_type == 'mentor') {
        $mentor_staff_chat_count = DB::table('mentor_staff_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'mentee')->where('receiver_is_read', 0)->count();
        $unread_chat = $mentor_staff_chat_count + $mentor_mentee_chat_count;
    } else if ($user_type == 'mentee') {
        $mentee_staff_chat_count = DB::table('mentee_staff_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'staff')->where('receiver_is_read', 0)->count();
        $mentor_mentee_chat_count = DB::table('mentor_mentee_chat_threads')->where('receiver_id', $user_id)->where('from_where', 'mentor')->where('receiver_is_read', 0)->count();
        $unread_chat = $mentee_staff_chat_count + $mentor_mentee_chat_count;
    }
    return $unread_chat;
}

function goaltask_event_notification($user_type, $user_id, $goaltask_id, $type = '')
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $fcmApiKey = config('app.fcmApiKey');

    if ($user_type == 'mentor') {
        $user_data = DB::table(MENTOR)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    } else if ($user_type == 'mentee') {
        $user_data = DB::table(MENTEE)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    }

    $schedule_session_count = schedule_session_count($user_type, $user_id);
    $user_total_chat_count = user_total_chat_count($user_type, $user_id);
    $user_total_unread_goaltask_count = user_total_unread_goaltask_count($user_type, $user_id);
    $result = '';
    if (!empty($firebase_id) && !empty($device_type)) {
        $message = "Goaltask Notification";
        if ($type == 'goal_notification') {
            $message = "Your mentor created a goal for you";
        } else if ($type == 'task_notification') {
            $message = "Your mentor created a assignment for you";
        }


        $send_data = array('title' => $message, 'type' => $type, 'goaltask_id' => $goaltask_id, 'message' => $message, 'firebase_token' => $firebase_id, 'unread_chat' => $user_total_chat_count, 'unread_task' => ($schedule_session_count + $user_total_unread_goaltask_count));
        $data_arr = array('meeting_data' => $send_data);

        if ($device_type == "iOS") {

            $msg = array('message' => $message, 'title' => $message, 'sound' => "default", 'badge' => ($schedule_session_count + $user_total_chat_count + $user_total_unread_goaltask_count)); // For IOS

            $fields = array('to' => $firebase_id, 'notification' => $msg, 'data' => $data_arr);


        } else if ($device_type == "android") {

            $fields = array('to' => $firebase_id, 'data' => $send_data); // For Android
        }

        $headers = array(
            'Authorization: key=' . $fcmApiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);

        $result_arr = json_decode($result);
        // echo '<pre>'; print_r($fields);
        // echo '<pre>'; print_r($result_arr);

        return json_encode($result);


    } else {
        return $result;
    }


}

function goaltask_event_notification_new($user_type, $user_id, $goaltask_id, $type = '')
{
    $url = 'https://fcm.googleapis.com/fcm/send';
    $fcmApiKey = config('app.fcmApiKey');

    if ($user_type == 'mentor') {
        $user_data = DB::table(MENTOR)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    } else if ($user_type == 'mentee') {
        $user_data = DB::table(MENTEE)->where('id', $user_id)->first();
        $firebase_id = !empty($user_data->firebase_id) ? $user_data->firebase_id : '';
        $device_type = !empty($user_data->device_type) ? $user_data->device_type : '';
    }

    $schedule_session_count = schedule_session_count($user_type, $user_id);
    $user_total_chat_count = user_total_chat_count($user_type, $user_id);
    $user_total_unread_goaltask_count = user_total_unread_goaltask_count($user_type, $user_id);
    $result = '';
    // if(!empty($firebase_id) && !empty($device_type)){
    $message = "Goaltask Notification";

    $send_data = array('title' => $message, 'type' => $type, 'goaltask_id' => $goaltask_id, 'message' => $message, 'firebase_token' => $firebase_id, 'unread_chat' => $user_total_chat_count, 'unread_task' => ($schedule_session_count + $user_total_unread_goaltask_count));
    $data_arr = array('meeting_data' => $send_data);

    if ($device_type == "iOS") {

        $msg = array('message' => $message, 'title' => $message, 'sound' => "default", 'badge' => ($schedule_session_count + $user_total_chat_count + $user_total_unread_goaltask_count)); // For IOS

        $fields = array('to' => $firebase_id, 'notification' => $msg, 'data' => $data_arr);


    } else if ($device_type == "android") {

        $fields = array('to' => $firebase_id, 'data' => $send_data); // For Android
    }

    $headers = array(
        'Authorization: key=' . $fcmApiKey,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);

    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    // Close connection
    curl_close($ch);

    $result_arr = json_decode($result);
    echo '<pre>';
    print_r($fields);
    echo '<pre>';
    print_r($result_arr);

    // return json_encode($result);


    // }else{
    //     return $result;
    // }


}

function user_total_unread_goaltask_count($user_type, $user_id, $type = '')
{
    $data = 0;
    if (!empty($type)) {
        $data = DB::table(GOALTASK_NOTIFICATION)->where('type', $type)->where('user_type', $user_type)->where('user_id', $user_id)->where('is_read', 0)->count();
    } else {
        $data = DB::table(GOALTASK_NOTIFICATION)->where('user_type', $user_type)->where('user_id', $user_id)->where('is_read', 0)->count();
    }

    return $data;

}

function user_unread_message_center_count($user_type, $user_id)
{
    // $data = 0;

    $data = DB::table('message_center_notifications')->where('user_type', $user_type)->where('user_id', $user_id)->where('is_read', 0)->count();


    return $data;
}

function isweekend($date = '')
{
    $date = date('Y-m-d');
    $date = strtotime($date);
    $date = date("l", $date);
    $date = strtolower($date);
    // echo $date;
    if ($date == "saturday" || $date == "sunday") {
        return true;
    } else {
        return false;
    }
}

function convert_sec_to_min($value = '')
{
    $data = ($value / 60);
    return round($data, 2);
}

function sendHTTP2Push($http2ch, $http2_server, $apple_cert, $app_bundle_id, $message, $deviceToken)
{
    // url (endpoint)
    $url = "{$http2_server}/3/device/{$deviceToken}";
    $cert = realpath($apple_cert);
    // headers
    $headers = array(
        "apns-topic: {$app_bundle_id}",
        "User-Agent: My Sender"
    );
    curl_setopt_array($http2ch, array(
        CURLOPT_URL => $url,
        CURLOPT_PORT => 443,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => $message,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSLCERT => $cert,
        CURLOPT_HEADER => 1
    ));
    $result = curl_exec($http2ch);
    if ($result === FALSE) {
        throw new Exception("Curl failed: " . curl_error($http2ch));
    }
    // get response
    $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
    if ($status == "200")
        echo "SENT|NA";
    else
        echo "FAILED|$status";

}

/* Twilio Chat */

function tw_generate_access_token($user_type = '', $user_id = '', $user_name = '')
{
    require_once('Twilio/autoload.php');

    $settings = get_single_data_id(SETTINGS, 1);
    $TWILIO_ACCOUNT_SID = $settings->twilio_account_sid;
    $TWILIO_API_KEY = config('app.twilio_chat_apiKeySid');
    $TWILIO_API_SECRET = config('app.twilio_chat_apiKeySecret');
    $TWILIO_CHAT_SERVICE_SID = config('app.twilio_chat_serviceSid');


    $chatGrant = new Twilio\Jwt\Grants\ChatGrant;
    $chatGrant->setServiceSid($TWILIO_CHAT_SERVICE_SID);
    // $accessToken = new Twilio\Jwt\AccessToken;
    $accessToken = new Twilio\Jwt\AccessToken(
        $TWILIO_ACCOUNT_SID,
        $TWILIO_API_KEY,
        $TWILIO_API_SECRET,
        3600
    );


    // $appName = "TwilioChat";
    // $identity = time();
    $identity = $user_type . '_' . $user_id . '_' . $user_name;
    $accessToken->setIdentity($identity);
    $accessToken->addGrant($chatGrant);

    return $accessToken->toJWT();

    // $response = array(
    //     'identity' => $identity,
    //     'token' => $accessToken->toJWT()
    // );

    // return response()->json($response);


}

function tw_chat_send_notification($user_type = '', $user_id = '', $message = '', $device_type = '', $firebase_id = '', $sender_name = '', $comes_from = '')
{

    $schedule_session_count = schedule_session_count($user_type, $user_id);
    $user_total_chat_count = user_total_chat_count($user_type, $user_id);
    $user_total_unread_goaltask_count = user_total_unread_goaltask_count($user_type, $user_id);


    $unread_task_count = $schedule_session_count + $user_total_unread_goaltask_count;
    $send_data = array('title' => $sender_name . " send 1 Message", 'type' => 'twilio_chat', 'comes_from' => "$comes_from", 'message' => $message, 'firebase_token' => $firebase_id, 'unread_chat' => "$user_total_chat_count", 'unread_task' => "$unread_task_count");
    $data_arr = array('meeting_data' => json_encode($send_data));

    if ($device_type == "iOS") {

        $msg = array('message' => $message, 'title' => $sender_name . " send 1 Message", 'comes_from' => $comes_from, 'sound' => "default", 'badge' => ($schedule_session_count + $user_total_chat_count + $user_total_unread_goaltask_count));
        // For IOS

        $fields = array('to' => $firebase_id, 'notification' => $msg, 'data' => $data_arr);


    } else if ($device_type == "android") {

        $fields = array('to' => $firebase_id, 'data' => $send_data); // For Android
    }

    $result = sendPushNotificationWithV1($fields);


    if (!empty($result['name'])) {
        // DB::table('twilio_chat_notification')->insert([
        //                                             'user_type' => $user_type,
        //                                             'user_id' =>  $user_id,
        //                                             'message' =>  $message,
        //                                             'device_type' => $device_type,
        //                                             'firebase_token' =>  $firebase_id,
        //                                             'notification_response' => json_encode($result) ,
        //                                             'created_at' => date('Y-m-d H:i:s')
        //                                         ]);
        return ['status' => true, 'message' => $result['name'], 'data' => array('fields' => $fields)];
    } else {
        // Handle case when results are not set or empty
        return ['status' => false, 'message' => 'No results found.'];
    }

}

function remove_emoji($string)
{

    // Match Enclosed Alphanumeric Supplement
    $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
    $clear_string = preg_replace($regex_alphanumeric, '', $string);

    // Match Miscellaneous Symbols and Pictographs
    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, '', $clear_string);

    // Match Emoticons
    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, '', $clear_string);

    // Match Transport And Map Symbols
    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, '', $clear_string);

    // Match Supplemental Symbols and Pictographs
    $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
    $clear_string = preg_replace($regex_supplemental, '', $clear_string);

    // Match Miscellaneous Symbols
    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '', $clear_string);

    // Match Dingbats
    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

    return $clear_string;
}

function sendPushNotification($body, $recipients)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(2419200);
    $notificationBuilder = new PayloadNotificationBuilder();
    $notificationBuilder->setBody($body)
        ->setSound('default');
    $dataBuilder = new PayloadDataBuilder();
    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();
    return FCM::sendTo($recipients, $option, $notification, $data);
}

function sendPushNotificationNew($title, $message, $recipients)
{
    $optionBuilder = new OptionsBuilder();
    $optionBuilder->setTimeToLive(2419200);
    $notificationBuilder = new PayloadNotificationBuilder($title);
    $notificationBuilder->setBody($message)
        ->setSound('default');
    $dataBuilder = new PayloadDataBuilder();
    $option = $optionBuilder->build();
    $notification = $notificationBuilder->build();
    $data = $dataBuilder->build();
    FCM::sendTo($recipients, $option, $notification, $data);
}

function sendPushNotificationWithV1($payload)
{
    $firebase = new FCMServiceProvider('storage/app/configs/firebase.json');
    try {
        $result = $firebase->sendNotification($payload);
    } catch (Exception $e) {
        $result = null;
    }

    return $result;

}
