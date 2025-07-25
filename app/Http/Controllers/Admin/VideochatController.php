<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;

class VideochatController extends Controller
{
    use ZoomMeetingTrait;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {

        $search = $request->search;
        $sort_needed = 0;

        $sort = $request->sort;
        $column = $request->column;

        $data = DB::table(VIDEO_CHAT_ROOMS . ' AS room')
            ->select('room.*', 'admins.name AS affiliate_name', 'sender_mentor.firstname AS sender_mentor_firstname', 'sender_mentor.lastname AS sender_mentor_lastname', 'receiver_mentor.firstname AS receiver_mentor_firstname', 'receiver_mentor.lastname AS receiver_mentor_lastname', 'sender_mentee.firstname AS sender_mentee_firstname', 'sender_mentee.lastname AS sender_mentee_lastname', 'receiver_mentee.firstname AS receiver_mentee_firstname', 'receiver_mentee.lastname AS receiver_mentee_lastname');

        $data = $data->selectRaw("(SELECT p1.duration FROM video_chat_participants p1 WHERE p1.room_sid = room.room_sid ORDER BY p1.id DESC LIMIT 1) AS sender_duration , IF((SELECT COUNT(id) FROM video_chat_participants p WHERE p.room_sid=room.room_sid) > 1, (SELECT p2.duration FROM video_chat_participants p2 WHERE p2.room_sid = room.room_sid ORDER BY p2.id ASC LIMIT 1 ) , 0 ) AS receiver_duration");

        $data = $data->leftJoin('mentor AS sender_mentor', function ($sender_mentor) {
            $sender_mentor->on('sender_mentor.id', '=', 'room.sender_id');
            $sender_mentor->where('room.sender_type', '=', 'mentor');
        })
            ->leftJoin('mentor AS receiver_mentor', function ($receiver_mentor) {
                $receiver_mentor->on('receiver_mentor.id', '=', 'room.receiver_id');
                $receiver_mentor->where('room.receiver_type', '=', 'mentor');
            })
            ->leftJoin('mentee AS sender_mentee', function ($sender_mentee) {
                $sender_mentee->on('sender_mentee.id', '=', 'room.sender_id');
                $sender_mentee->where('room.sender_type', '=', 'mentee');
            })
            ->leftJoin('mentee AS receiver_mentee', function ($receiver_mentee) {
                $receiver_mentee->on('receiver_mentee.id', '=', 'room.receiver_id');
                $receiver_mentee->where('room.receiver_type', '=', 'mentee');
            })
            ->leftJoin('admins', 'admins.id', 'room.affiliate_id')
            ->where(function ($q) use ($search) {
                $q->where('sender_mentor.firstname', 'like', '%' . $search . '%')
                    ->orWhere('sender_mentor.lastname', 'like', '%' . $search . '%')
                    ->orWhere('receiver_mentor.firstname', 'like', '%' . $search . '%')
                    ->orWhere('receiver_mentor.lastname', 'like', '%' . $search . '%')
                    ->orWhere('sender_mentee.firstname', 'like', '%' . $search . '%')
                    ->orWhere('sender_mentee.lastname', 'like', '%' . $search . '%')
                    ->orWhere('receiver_mentee.firstname', 'like', '%' . $search . '%')
                    ->orWhere('receiver_mentee.lastname', 'like', '%' . $search . '%')
                    ->orWhere('room.status', 'like', '%' . $search . '%');
            });


        if (Auth::user()->type == 2) {
            $data = $data->where('room.affiliate_id', Auth::user()->id);
        } else if (Auth::user()->type == 3) {
            if (Auth::user()->parent_id != 1) {
                $data = $data->where('room.affiliate_id', Auth::user()->parent_id);
            }
        }

        if (!empty($sort) && !empty($column)) {
            $sort_needed = 1;
            if ($sort == 'asc') {
                if ($column == 'sender') {
                    $data = $data->orderBy('room.sender_id', 'asc')->paginate(10);
                } else if ($column == 'receiver') {
                    $data = $data->orderBy('room.receiver_id', 'asc')->paginate(10);
                } else if ($column == 'date') {
                    $data = $data->orderBy('room.created_at', 'asc')->paginate(10);
                } else if ($column == 'status') {
                    $data = $data->orderBy('room.status', 'asc')->paginate(10);
                }

            } else if ($sort == 'desc') {
                if ($column == 'sender') {
                    $data = $data->orderBy('room.sender_id', 'desc')->paginate(10);
                } else if ($column == 'receiver') {
                    $data = $data->orderBy('room.receiver_id', 'desc')->paginate(10);
                } else if ($column == 'date') {
                    $data = $data->orderBy('room.created_at', 'desc')->paginate(10);
                } else if ($column == 'status') {
                    $data = $data->orderBy('room.status', 'desc')->paginate(10);
                }
            }
            // $data = $data->orderBy('room.id','desc')->paginate(10);
        } else {
            $data = $data->orderBy('room.id', 'desc')->paginate(10);
        }


        if (empty($sort)) {
            $sort = 'asc';
        }

        $data->appends(array('search' => $search))->links();
        $data->appends(array('sort' => $sort))->links();
        $data->appends(array('column' => $column))->links();

        // echo '<pre>'; print_r($data); die;

        return view('admin.videochat.list', compact('data', 'sort', 'search', 'column', 'sort_needed'));
    }

    public function details($room)
    {
        # code...
        if (!empty($room)) {
            $recordings = null;
            $exist_chat = DB::table(VIDEO_CHAT_ROOMS)->where('unique_name', $room)->first();
            if (!empty($exist_chat)) {
                $type = 'zoom';
                $download_url = $exist_chat->recording_url;
                if ($download_url) {
                    $download_token = $this->generateDownloadRecordingToken();
                    $recordings = $download_url . '?access_token=' . $download_token;
                }
                $video_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code', $exist_chat->chat_code)->first();


                $participants = DB::table('video_chat_participants')->where('room_sid', $room)->get()->toarray();

                $i = 0;
                $len = count($participants);

                $sender_duration = '';
                $receiver_duration = '';

                if (!empty($participants)) {
                    foreach ($participants as $p) {
                        if ($len > 1) {
                            if ($i == 0) {
                                $receiver_duration = $p->duration;
                            } else if ($i == $len - 1) {
                                $sender_duration = $p->duration;
                            }
                        } else {
                            $sender_duration = $p->duration;
                            $receiver_duration = '0';
                        }
                        $i++;
                    }
                }


                return view('admin.videochat.view', compact('exist_chat', 'recordings', 'video_chat_user', 'sender_duration', 'receiver_duration', 'type'));
            } else {

                $exist_chat = DB::table(VIDEO_CHAT_ROOMS)->where('room_sid', $room)->first();

                if (!empty($exist_chat)) {

                    $fetch_room = fetch_room($exist_chat->room_sid);
                    $recordings = $fetch_room['linkarr'];
                    $type = 'twilio';

                    $video_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code', $exist_chat->chat_code)->first();


                    $participants = DB::table('video_chat_participants')->where('room_sid', $room)->get()->toarray();

                    $i = 0;
                    $len = count($participants);

                    $sender_duration = '';
                    $receiver_duration = '';

                    if (!empty($participants)) {
                        foreach ($participants as $p) {
                            if ($len > 1) {
                                if ($i == 0) {
                                    $receiver_duration = $p->duration;
                                } else if ($i == $len - 1) {
                                    $sender_duration = $p->duration;
                                }
                            } else {
                                $sender_duration = $p->duration;
                                $receiver_duration = '0';
                            }
                            $i++;
                        }
                    }


                    return view('admin.videochat.view', compact('exist_chat', 'recordings', 'video_chat_user', 'sender_duration', 'receiver_duration', 'type'));

                } else {
                    return redirect('/admin/videochat/list');
                }
            }

        } else {
            return redirect('/admin/videochat/list');
        }

    }


    public
    function create_compositions($room)
    {


        $composition = create_compositions($room);
        $composition_id = $composition['composition_id'];
        echo $composition_id;

    }


    public
    function getmedia($compositionSid)
    {
        return Redirect::to(getmedia($compositionSid));
    }


}
