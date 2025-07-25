<?php

namespace App\Http\Controllers\Api\V1\mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Response;

class MessageCenterController extends Controller
{
    //
    private $user_id;
    private $assigned_by;
    public function __construct(Request $request)
    {
        $header = $request->header('Authorizations');
        if(empty($header)){
            response()->json(array('status'=>false,'message'=>"Authentication failed"))->send();
            exit();
        }
        if(substr($header, 0, 7) != "Bearer "){
            response()->json(array('status'=>false,'message'=>"Please add prefix and a space before the token"))->send();
            exit();
        }
        try{
            $this->header_str = substr($header,7,strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);

            $chk_user = DB::table('mentor')->select('*')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit();
            }

            if(!empty($chk_user->is_logged_out)){
                response()->json(array('status'=>false,'message'=>"Logged Out"))->send();
                exit();
            }

            mentor_last_activity($this->user_id);

            $user_id = $this->user_id;
            $this->assigned_by = $chk_user->assigned_by;
            $assigned_by = $this->assigned_by;


        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit();
        }
    }

    public function index(Request $request)
    {
    	# code...

        $a_ids = $c_ids = $ids = array();
        $all_mentor_data = DB::table('message_center')->where('is_mentor',1)->where(function ($query)               {
                                $query->where('created_by', '=', 1)
                                      ->orWhere('created_by', '=', $this->assigned_by);
                            })->get()->toarray();
        $custome_mentor_data = DB::table('message_center_users')->where('user_type','mentor')->where('user_id', $this->user_id)->get()->toarray();

        if(!empty($all_mentor_data)){
            foreach($all_mentor_data as $d){
                $a_ids[] = $d->id;
            }
        }
        if(!empty($custome_mentor_data)){
            foreach($custome_mentor_data as $d){
                $c_ids[] = $d->message_id;
            }
        }

        $ids = array_merge($a_ids,$c_ids);

    	$take = !empty($request->take)?$request->take:10;
    	$page = !empty($request->page)?$request->page:0;
    	$skip = ($take*$page);

    	$data = array();
        $count_data = 0;

        if(!empty($ids)){
            $data = DB::table('message_center')->selectRaw("id,message,created_by,DATE_FORMAT(created_at,'%D %b,%Y %H:%i %p') AS created_at")->whereIn('id', $ids)->where('hidden',0)->take($take)->skip($skip)->orderBy('id','desc')->get()->toarray();
            $count_data = DB::table('message_center')->whereIn('id', $ids)->where('hidden',0)->count();

            DB::table('message_center_notifications')->whereIn('message_id',$ids)->where('user_type','mentor')->where('user_id', $this->user_id)->update(['is_read'=>1]);
        }


    	return response()->json(['status'=>true, 'message'=>"All messages from message center" , 'data'=>array('count_messages'=>$count_data,'messages'=>$data)]);
    }

    public function index_bkp(Request $request)
    {
        # code...

        $take = !empty($request->take)?$request->take:10;
        $page = !empty($request->page)?$request->page:0;
        $skip = ($take*$page);

        $data = DB::table('message_center')->selectRaw("id,message,DATE_FORMAT(created_at,'%D %b,%Y %H:%i %p') AS created_at")->where('is_mentor', 1)->take($take)->skip($skip)->orderBy('id','desc')->get()->toarray();
        $count_data = DB::table('message_center')->where('is_mentor', 1)->count();

        // echo '<pre>'; print_r($data);
        return response()->json(['status'=>true, 'message'=>"All messages from message center" , 'data'=>array('count_messages'=>$count_data,'messages'=>$data)]);
    }
}
