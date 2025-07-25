<?php

namespace App\Http\Controllers\Mentee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageCenterController extends Controller
{
    private $user_id;
    private $assigned_by;
    public function __construct()
    {
        $this->middleware('auth:mentee');

    }

    public function index(Request $request)
    {
        # code...

        $a_ids = $c_ids = $ids = array();

        $all_mentor_data = DB::table('message_center')->where('hidden',0)->where('is_mentor',1)->where(function ($query)               {
            $query->where('created_by', '=', 1)
                ->orWhere('created_by', '=', $this->assigned_by);
        })->get()->toarray();
        $custome_mentor_data = DB::table('message_center_users')->where('user_type','mentee')->where('user_id', $this->user_id)->get()->toarray();

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
            $data = DB::table('message_center')->selectRaw("id,message,created_by,DATE_FORMAT(created_at,'%D %b,%Y %H:%i %p') AS created_at")->whereIn('id', $ids)->take($take)->skip($skip)->orderBy('id','desc')->get()->toarray();
            $count_data = DB::table('message_center')->whereIn('id', $ids)->count();

            DB::table('message_center_notifications')->whereIn('message_id',$ids)->where('user_type','mentee')->where('user_id', $this->user_id)->update(['is_read'=>1]);
        }

        //dd($data);
        return view('mentee.message-center')->with('data',$data);
    }
}

