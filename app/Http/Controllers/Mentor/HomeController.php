<?php

namespace App\Http\Controllers\Mentor;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:mentor');
    }

    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $externalId = Auth::user()->externalId;
        $user_id = Auth::user()->id;
//    	if(!empty($externalId)){
//    		$sessionLogCount = getMentorStudentsCount($externalId);
//    	}else{
//    		$sessionLogCount = 0;
//    	}

        $sessionLogCount = 0;
        // $data = DB::table('mentor_session_log_count')->where('mentor_id', $user_id)->first();
        // if (!empty($data)) {
        //     $sessionLogCount = $data->count;
        // }

        $sessionLogCount = DB::table('session')->where('mentor_id', $user_id)
            ->where('status', 1)
            ->count();

        $chat_time = !empty($request->chat_time) ? $request->chat_time : 'today';


        $affiliate_data = get_single_data_id('admins', Auth::user()->assigned_by);
        $timezone = !empty($affiliate_data->timezone) ? $affiliate_data->timezone : 'America/New_York';
        date_default_timezone_set($timezone);
        $today = date('Y-m-d H:i:s');
        $today_start = date('Y-m-d');
        $last_week = date('Y-m-d', strtotime("-1 week"));
        $last_month = date('Y-m-d', strtotime("-1 month"));

        if ($chat_time == 'today') {
            $chat_duration = " threads.created_date BETWEEN '" . $today_start . "' AND '" . $today . "' ";
        } else if ($chat_time == 'lastweek') {
            $chat_duration = " threads.created_date BETWEEN '" . $last_week . "' AND '" . $today . "' ";
        } else if ($chat_time == 'lastmonth') {
            $chat_duration = " threads.created_date BETWEEN '" . $last_month . "' AND '" . $today . "' ";
        } else {
            $chat_duration = " threads.created_date BETWEEN '" . $today_start . "' AND '" . $today . "' ";
        }

        $upcoming_meeting = $past_meeting = $chats = array();

        /*upcoming session*/
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
            ->where('meeting_users.user_id', Auth::user()->id)
            ->where('meeting.schedule_time', '>=', $today)
            ->where('meeting_status.status', 1)
            ->orderBy('meeting.schedule_time', 'asc')
            ->get()->toarray();

        /*previous session history*/
        $past_meeting = DB::table('meeting_users')
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
            ->where('meeting_users.user_id', Auth::user()->id)
            ->where('meeting.schedule_time', '<=', $today)
            ->where('meeting.schedule_time', '>=', Carbon::now()->addDays(-3))
            ->orderBy('meeting.schedule_time', 'desc')
            ->get()->toarray();

        $recently_logged_sessions = DB::table('session AS s')->select('s.*', 'mentee.firstname', 'mentee.lastname')->leftJoin('mentee', 'mentee.id', 's.mentee_id')->where('s.mentor_id', Auth::user()->id)->whereRaw(" DATE(s.created_date) BETWEEN '" . $last_week . "' AND '" . $today . "'  ")->orderBy('s.id', 'desc')->get()->toarray();

        $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('mentor_id', Auth::user()->id)->get()->toarray();

        if (!empty($mentor_mentee_chat_codes)) {
            foreach ($mentor_mentee_chat_codes as $code) {
                $code_arr[] = $code->code;
            }
        }

        if (!empty($code_arr)) {
            $chats = DB::table('mentor_mentee_chat_threads AS threads')
                ->select('threads.*')
                ->whereIn('threads.chat_code', $code_arr)
                ->whereRaw($chat_duration)
                ->orderBy('threads.id', 'desc')
                ->get()->toarray();


        }


        // echo '<pre>'; print_r($recently_logged_sessions); die;
        // echo '<pre>'; print_r($chats);
        // die;

        return view('mentor.home', compact('upcoming_meeting', 'past_meeting', 'chats', 'chat_time', 'recently_logged_sessions', 'sessionLogCount'));
    }


}