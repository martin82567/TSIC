<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

class SystemMessagingController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	# code...

        $system_messaging = false;
        if(Auth::user()->type == 1){
            $system_messaging = true;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $system_messaging = true;
        }

        if(!$system_messaging){
            return redirect('/404');
        }

        date_default_timezone_set('America/New_York');
		$date_time = date('Y-m-d H:i:s');

		$expired_data = DB::table(SYSTEM_MESSAGING.' AS m')->where('m.is_expired',0)->where('m.end_datetime', '<', $date_time)->get()->toarray();

		if(!empty($expired_data)){
			foreach($expired_data as $d){
				DB::table(SYSTEM_MESSAGING.' AS m')->where('m.id',$d->id)->update(['is_expired'=>1]);
			}
		}

		// echo '<pre>'; print_r($expired_data); die;


    	$search = !empty($request->search)?$request->search:'';
    	$data = array();
    	// echo SYSTEM_MESSAGING_APPS; die;
    	$data = DB::table(SYSTEM_MESSAGING.' AS msg');
    	$data = $data->select('msg.*','a.name AS created',DB::raw("(SELECT GROUP_CONCAT(apps.apps) FROM ".SYSTEM_MESSAGING_APPIDS." AS appids LEFT JOIN ".SYSTEM_MESSAGING_APPS." AS apps ON apps.id = appids.app_id  WHERE appids.message_id = msg.id) AS app_titles"));
        $data = $data->leftJoin('admins AS a','a.id','msg.created_by');
    	if(!empty($search)){
    		$data = $data->where('msg.message','LIKE','%'.$search.'%');
    	}
        if(Auth::user()->type == 2){
            $data = $data->where('msg.created_by',Auth::user()->id);
        }
    	$data = $data->orderBy('msg.id','desc')->paginate(15);

    	// echo '<pre>'; print_r($data); die;
    	$data->appends(array('search'=>$search))->links();
    	return view('admin.system_messaging.list', compact('data','search'));

    }

    public function add(Request $request)
    {
    	# code...

        $system_messaging = false;
        if(Auth::user()->type == 1){
            $system_messaging = true;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $system_messaging = true;
        }

        if(!$system_messaging){
            return redirect('/404');
        }

    	$id = !empty($request->id)?$request->id:'';
    	if(!empty($id)){
    		try{
    			$id = Crypt::decrypt($id);
    			$system_messaging = array();
    			$system_messaging_appids = array();
    			if(!empty($id)){
    				$system_messaging = DB::table(SYSTEM_MESSAGING)->where('id',$id)->first();
    				$system_messaging_appids = DB::table(SYSTEM_MESSAGING_APPIDS)->where('message_id',$id)->get()->toarray();

    			}

    			$apps = DB::table(SYSTEM_MESSAGING_APPS)->get()->toarray();
    			return view('admin.system_messaging.add', compact('apps','system_messaging','system_messaging_appids'));

    		} catch (\ DecryptException $e) {
    			return redirect('/admin/system-messaging/list');
    		}

    	}else{
    		return redirect('/admin/system-messaging/list');
    	}

    }

    public function save(Request $request)
    {
    	date_default_timezone_set('America/New_York');
		$date_time = date('Y-m-d H:i:s');

    	$id = !empty($request->id)?$request->id:'';
    	$app_ids = !empty($request->app_ids)?$request->app_ids:'';

    	if(empty($id)){

            if(Auth::user()->type == 1){
                $created_by = Auth::user()->id;
            }else if(Auth::user()->type == 2){
                $created_by = Auth::user()->id;
            }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
                $created_by = Auth::user()->parent_id;
            }

    		$message = !empty($request->message)?$request->message:'';
    		$start_datetime = !empty($request->start_datetime)?$request->start_datetime:'';
    		$end_datetime = !empty($request->end_datetime)?$request->end_datetime:'';

    		$start_datetime = str_replace("-", "/", $start_datetime);
        	$start_datetime = date("Y-m-d H:i:s", strtotime($start_datetime));

        	$end_datetime = str_replace("-", "/", $end_datetime);
        	$end_datetime = date("Y-m-d H:i:s", strtotime($end_datetime));

    		// echo $start_datetime; die;

    		$id = DB::table(SYSTEM_MESSAGING)->insertGetId(['message'=>$message,'start_datetime'=>$start_datetime,'end_datetime'=>$end_datetime,'created_at'=>$date_time,'updated_at'=>$date_time,'created_by'=>$created_by]);

    		if(!empty($app_ids)){
    			foreach($app_ids as $a){
    				DB::table(SYSTEM_MESSAGING_APPIDS)->insert(['message_id'=>$id,'app_id'=>$a,'created_at'=>$date_time,'updated_at'=>$date_time]);
    			}
    		}
    		$m = "Messaging created successfully";
    	}else{
    		$system_messaging = DB::table(SYSTEM_MESSAGING)->where('id',$id)->first();
    		$message = !empty($request->message)?$request->message:$system_messaging->message;
    		$start_datetime = !empty($request->start_datetime)?$request->start_datetime:$system_messaging->start_datetime;
    		$end_datetime = !empty($request->end_datetime)?$request->end_datetime:$system_messaging->end_datetime;

    		$start_datetime = str_replace("-", "/", $start_datetime);
        	$start_datetime = date("Y-m-d H:i:s", strtotime($start_datetime));

        	$end_datetime = str_replace("-", "/", $end_datetime);
        	$end_datetime = date("Y-m-d H:i:s", strtotime($end_datetime));

        	DB::table(SYSTEM_MESSAGING)->where('id',$id)->update(['message'=>$message,'start_datetime'=>$start_datetime,'end_datetime'=>$end_datetime,'updated_at'=>$date_time]);



    		$message_app_ids = DB::table(SYSTEM_MESSAGING_APPIDS)->where('message_id',$id)->get()->toarray();

    		$exist_id_arr = array();

            if(!empty($app_ids)){
                foreach($app_ids as $key => $app_id){
                    $user_project = DB::table(SYSTEM_MESSAGING_APPIDS)->where('message_id',$id)->where('app_id',$app_id)->first();
                    if(empty($user_project)){
                        DB::table(SYSTEM_MESSAGING_APPIDS)->insert(['message_id'=>$id,'app_id'=>$app_id,'created_at'=>$date_time,'updated_at'=>$date_time]);
                    }
                    $exist_id_arr[] = $app_id;
                }
            }

            if(!empty($message_app_ids)){
                foreach($message_app_ids as $a){
                    if(!in_array($a->app_id,$exist_id_arr)){
                        DB::table(SYSTEM_MESSAGING_APPIDS)->where('message_id',$id)->where('app_id',$a->app_id)->delete();
                    }
                }
            }

    		$m = "Messaging updated successfully";
    	}

    	session(['success_message'=>$m]);
    	return redirect('/admin/system-messaging/list');

    }

    public function delete(Request $request)
    {
    	$id = !empty($request->id)?$request->id:'';

    	if(!empty($id)){
    		try{
    			$id = Crypt::decrypt($id);
    			DB::table(SYSTEM_MESSAGING)->where('id',$id)->delete();
    			DB::table(SYSTEM_MESSAGING_APPIDS)->where('message_id',$id)->delete();

    			session(['success_message'=>'Removed successfully']);
    			return redirect('/admin/system-messaging/list');

    		} catch (\ DecryptException $e) {
    			return redirect('/admin/system-messaging/list');
    		}

    	}else{
    		return redirect('/admin/system-messaging/list');
    	}
    }

    public function expire(Request $request)
    {
        date_default_timezone_set('America/New_York');
        $date_time = date('Y-m-d H:i:s');

        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            try{
                $id = Crypt::decrypt($id);
                DB::table(SYSTEM_MESSAGING)->where('id',$id)->update(['is_expired'=>1,'updated_at'=>$date_time]);

                session(['success_message'=>'Expired successfully']);
                return redirect('/admin/system-messaging/list');

            } catch (\ DecryptException $e) {
                return redirect('/admin/system-messaging/list');
            }

        }else{
            return redirect('/admin/system-messaging/list');
        }
    }
}
