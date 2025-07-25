<?php
namespace App\Http\Controllers\Mentee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Auth;
use Route;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class VideochatController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void  Scheduled Session ...
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

    public function test(Request $request)
    {

        // die('Hi');
        return view('mentee.videochat-test');
    }

    public function initiate(Request $request)
    {
        if(is_numeric($request->mentor_id)) {
            $mentor_id = !empty($request->mentor_id)?$request->mentor_id:'';
        } else{
            $mentor_id = !empty(Crypt::decrypt($request->mentor_id)) ? Crypt::decrypt($request->mentor_id) : '';
        }
        $mentor_data = DB::table(MENTOR)->find($mentor_id);
        $mentor_device_type = !empty($mentor_data->device_type)?$mentor_data->device_type:'';
        $mentor_firebase_id = !empty($mentor_data->firebase_id)?$mentor_data->firebase_id:'';
        $mentor_voip_device_token = !empty($mentor_data->voip_device_token)?$mentor_data->voip_device_token:'';
        return view('mentee.videochat-initiate', compact('mentor_id','mentor_device_type','mentor_firebase_id','mentor_voip_device_token'));
    }




}
