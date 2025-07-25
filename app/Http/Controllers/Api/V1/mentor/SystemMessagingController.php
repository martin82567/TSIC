<?php

namespace App\Http\Controllers\Api\V1\mentor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SystemMessagingController extends Controller
{
    //
    public function index(Request $request)
    {
    	$today = date('Y-m-d H:i:s');
    	// echo $today; die;
    	$data = DB::table(SYSTEM_MESSAGING.' AS msg')->select('msg.id','msg.message','msg.start_datetime','msg.end_datetime','appids.app_id','msg.created_by')->whereRaw("msg.start_datetime <= '".$today."' AND msg.end_datetime >= '".$today."'");
    	$data = $data->leftJoin(SYSTEM_MESSAGING_APPIDS.' AS appids',function ($join) {
                $join->on('appids.message_id', '=' , 'msg.id') ;
                $join->where('appids.app_id', '=' , 2) ;
            });
    	$data = $data->where('appids.app_id', 2);
        $data = $data->where('msg.is_expired',0);
        $data = $data->where('msg.created_by',1);
        $data = $data->orderBy('msg.id','desc');
    	$data = $data->get()->toarray();

    	return Response::json(['status'=>true,'message'=>"Mentor System messaging",'data'=>array('messaging'=>$data)]);
    }
}
