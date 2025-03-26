<?php
namespace App\Http\Controllers\Mentor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class VideochatController extends Controller
{
    use ZoomMeetingTrait;

	/**
     * Create a new controller instance.
     *
     * @return void  Scheduled Session ...
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

    public function test(Request $request)
    {

        // die('Hi');
        return view('mentor.videochat-test');
    }

    public function initiate(Request $request)
    {
        if(is_numeric($request->mentee_id)) {
            $mentee_id = !empty($request->mentee_id) ? $request->mentee_id : '';
        } else{
            $mentee_id = !empty(Crypt::decrypt($request->mentee_id)) ? Crypt::decrypt($request->mentee_id) : '';
        }
        $mentee_data = DB::table(MENTEE)->find($mentee_id);
        $mentee_device_type = !empty($mentee_data->device_type)?$mentee_data->device_type:'';
        $mentee_firebase_id = !empty($mentee_data->firebase_id)?$mentee_data->firebase_id:'';
        $mentee_voip_device_token = !empty($mentee_data->voip_device_token)?$mentee_data->voip_device_token:'';

        // Generate Zoom JWT Access Token
        $unique_name = 'mentor-' . Auth::user()->id . '-mentee-' . $mentee_id . '-' . time();
        $signature = $this->generateMentorZoomToken($unique_name);
        // $receiver_jwt = $this->generateMenteeZoomToken($unique_name);
        // $room_sid = '';

        return view('mentor.videochat-initiate', compact('mentee_id','mentee_device_type','mentee_firebase_id','mentee_voip_device_token', 'unique_name', 'signature'));
    }




}
