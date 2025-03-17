<?php

namespace App\Http\Controllers\Mentee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use DB;
use Crypt;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:mentee');
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
//        if(!empty($externalId)){
//            $sessionLogCount = getCountByStudentId($externalId);
//        }else{
//            $sessionLogCount = 0;
//        }
        $get_mentors = DB::table('assign_mentee')->select('assign_mentee.*', 'mentor.is_active', 'mentor.platform_status', 'mentor_status.view_in_application')->leftJoin('mentor', 'mentor.id', 'assign_mentee.assigned_by')->leftJoin('mentor_status', 'mentor_status.id', 'mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.platform_status', 1)->where('assign_mentee.mentee_id', $user_id)->get()->toarray();

        $mentor_ids = array();
        if (!empty($get_mentors)) {
            foreach ($get_mentors as $gm) {
                $mentor_ids[] = $gm->assigned_by;
            }
        }
        $sessionLogCount = 0;
        if (!empty($mentor_ids)) {
            $sessionLogCount = DB::table('mentor_session_log_count')->whereIn('mentor_id', $mentor_ids)->sum('count');
        }


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
            ->where('meeting_users.user_id', Auth::user()->id)
            ->where('meeting.schedule_time', '>=', $today)
            ->where('meeting_status.status', 1)
            ->orderBy('meeting.schedule_time', 'asc')
            ->get()->toarray();

        /*previous session history*/
        $past_meeting = DB::table('meeting_users')
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
            ->where('meeting_users.user_id', Auth::user()->id)
            ->where('meeting.schedule_time', '<=', $today)
            ->orderBy('meeting.schedule_time', 'desc')
            ->get()->toarray();

        $mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('mentee_id', Auth::user()->id)->get()->toarray();

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


        // echo '<pre>'; print_r($upcoming_meeting);
        // echo '<pre>'; print_r($past_meeting);
        // echo '<pre>'; print_r($chats);
        // die;


        return view('mentee.home', compact('upcoming_meeting', 'past_meeting', 'chats', 'chat_time', 'sessionLogCount'));
    }

}
