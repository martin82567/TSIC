<?php

namespace App\Http\Controllers\Admin;

use Auth;
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
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	# code...
    	$message_center = false;
    	if(Auth::user()->type == 1){

	        $message_center = true;
	    }else if(Auth::user()->type == 2){
	        $message_center = true;
	    }

	    if(!$message_center){
	    	return redirect(404);
	    }
    	$search = !empty($request->search)?$request->search:'';
    	$data = DB::table('message_center');
        $data = $data->select('message_center.*','admins.name')->leftJoin('admins','admins.id','message_center.created_by');
    	if(!empty($search)){
    		$data = $data->where('message_center.message','LIKE','%'.$search.'%');
    	}

    	if(Auth::user()->type == 2){
    		$data = $data->where('message_center.created_by', Auth::user()->id);
    	}
    	$data = $data->orderBy('message_center.id','desc')->paginate(10);

    	$data->appends(array('search'=>$search))->links();

    	// echo '<pre>'; print_r($data);
    	return view('admin.message_center.list', compact('data','search'));
    }

    public function add(Request $request)
    {
    	# code...
    	$affiliate_id = 0;
    	if(Auth::user()->type == 2){
    		$affiliate_id = Auth::user()->id;
    	}

    	return view('admin.message_center.add', compact('affiliate_id'));
    }

    public function save(Request $request)
    {
    	date_default_timezone_set('America/New_York');
		$date_time = date('Y-m-d H:i:s');

		$insert = array();
    	$insert['message'] = !empty($request->message)?$request->message:'';
    	$insert['is_mentor'] = !empty($request->is_mentor)?$request->is_mentor:0;
        $insert['is_mentee'] = !empty($request->is_mentee)?$request->is_mentee:0;
        $mentor_id_val = !empty($request->mentor_id_val)?$request->mentor_id_val:0;
    	$mentee_id_val = !empty($request->mentee_id_val)?$request->mentee_id_val:0;
    	$insert['created_at'] = $date_time;
    	$insert['created_by'] = Auth::user()->id;


        $mentor_arr = explode(",",$mentor_id_val);
        $mentee_arr = explode(",",$mentee_id_val);

        $is_admin = 0;
        if(Auth::user()->type == 1){
            $is_admin = 1;
        }

        // $this->sendNotification('mentor',$is_admin,0,array());
        // sendNotification('mentee',array(),array());
        // die;



    	$id = DB::table('message_center')->insertGetId($insert);

        if(!empty($insert['is_mentor'])){
            $this->sendNotification('mentor',$is_admin,$id,$insert['message'],array());
        }

        if(!empty($insert['is_mentee'])){
            $this->sendNotification('mentee',$is_admin,$id,$insert['message'],array());
        }

        if($insert['is_mentor'] == 0){
            if(!empty($mentor_id_val)){
                if(!empty($mentor_arr)){
                    $this->sendNotification('mentor',$is_admin,$id,$insert['message'],$mentor_arr);
                    foreach($mentor_arr as $r){
                        DB::table('message_center_users')->insert(['message_id'=>$id,'user_type'=>'mentor','user_id'=>$r,'created_at'=>$date_time]);
                    }
                }
            }
        }
        if($insert['is_mentee'] == 0){
            if(!empty($mentee_id_val)){
                if(!empty($mentee_arr)){
                    $this->sendNotification('mentee',$is_admin,$id,$insert['message'],$mentee_arr);
                    foreach($mentee_arr as $e){
                        DB::table('message_center_users')->insert(['message_id'=>$id,'user_type'=>'mentee','user_id'=>$e,'created_at'=>$date_time]);
                    }
                }
            }
        }



    	session(['success_message'=>"Message created successfully"]);
    	return redirect('/admin/message-center/list');
    }

    private function sendNotification($type='',$is_admin=0,$id,$message,$user_arr=[])
    {
        # code...
        $mentors = $mentees = array();


        if($type == 'mentor'){
            if(!empty($user_arr)){
                $mentors = DB::table('mentor')->select('mentor.id','mentor.device_type','mentor.firebase_id','mentor.assigned_by')->whereIn('id',$user_arr)->get()->toarray();
            }else{
                if(empty($is_admin)){
                    $mentors = DB::table('mentor')->select('mentor.id','mentor.device_type','mentor.firebase_id','mentor.assigned_by','mentor.is_active')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.assigned_by', Auth::user()->id)->get()->toarray();

                }else{
                    $mentors = DB::table('mentor')->select('mentor.id','mentor.device_type','mentor.firebase_id','mentor.assigned_by','mentor.is_active')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor_status.view_in_application', 1)->get()->toarray();
                }

            }
        }else if($type == 'mentee'){
            if(!empty($user_arr)){
                $mentees = DB::table('mentee')->select('mentee.id','mentee.device_type','mentee.firebase_id','mentee.assigned_by')->whereIn('id',$user_arr)->get()->toarray();
            }else{
                if(empty($is_admin)){
                    $mentees = DB::table('mentee')->select('mentee.id','mentee.device_type','mentee.firebase_id','mentee.assigned_by','mentee.status')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->where('mentee.assigned_by', Auth::user()->id)->get()->toarray();
                }else{
                    $mentees = DB::table('mentee')->select('mentee.id','mentee.device_type','mentee.firebase_id','mentee.assigned_by','mentee.status')->leftJoin('student_status','student_status.id','mentee.status')->where('student_status.view_in_application', 1)->get()->toarray();
                }

            }
        }

        // echo '<pre>'; echo $type;
        // echo '<pre>'; print_r($mentees); die;


        $title = "Announcements";


        if(!empty($mentors)){
            foreach($mentors as $m){
                $user_id = $m->id;
                $firebase_id = $m->firebase_id;
                $device_type = $m->device_type;
                $success_msg_id = '';
                if(!empty($m->firebase_id)){
                    $mentor_schedule_session_count = schedule_session_count('mentor',$m->id);
                    $mentor_user_total_chat_count = user_total_chat_count('mentor',$m->id);
                    $mentor_total_unread_goaltask_count = user_total_unread_goaltask_count('mentor',$m->id);

                    $unread_task_count = $mentor_schedule_session_count+$mentor_total_unread_goaltask_count;
                    $send_data = array('title' => $title,'type' => 'message_center' , 'message_id' => "$id",'message'=>$message,'firebase_token' => $firebase_id , 'unread_chat'=> "$mentor_user_total_chat_count", 'unread_task' => "$unread_task_count");

                    $data_arr = array('meeting_data' => json_encode($send_data));

                    if($device_type == 'iOS'){
                        $msg = array('message' => $message,'title' => $title, 'sound'=>"default" , 'badge'=> ($mentor_schedule_session_count+$mentor_user_total_chat_count+$mentor_total_unread_goaltask_count)  );
                        $fields = array('to' => $m->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS
                    }else{
                        $fields = array('to' => $m->firebase_id,'data' => $send_data ); // For Android
                    }

                    $result = sendPushNotificationWithV1($fields);

                    if(!empty($result['name'])){
                        $success_msg_id = $result['name'];
                    }
                }

                DB::table('message_center_notifications')->insert(['message_id'=>$id,'user_type'=>'mentor','user_id'=>$user_id,'device_type'=>$device_type,'firebase_token'=>$firebase_id,'success_msg_id'=>$success_msg_id,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }
        }
        /*++++++++++++++++++++++++*/
        if(!empty($mentees)){
            foreach($mentees as $m){
                $user_id = $m->id;
                $firebase_id = $m->firebase_id;
                $device_type = $m->device_type;
                $success_msg_id = '';
                if(!empty($m->firebase_id)){
                    $mentee_schedule_session_count = schedule_session_count('mentee',$m->id);
                    $mentee_user_total_chat_count = user_total_chat_count('mentee',$m->id);
                    $mentee_total_unread_goaltask_count = user_total_unread_goaltask_count('mentee',$m->id);

                    $unread_task_count = $mentee_schedule_session_count+$mentee_total_unread_goaltask_count;
                    $send_data_mentee = array('title' => $title,'type' => 'message_center' , 'message_id' => "$id",'message'=>$message,'firebase_token' => $m->firebase_id , 'unread_chat'=> "$mentee_user_total_chat_count", 'unread_task' => "$unread_task_count" );
                    $data_arr = array('meeting_data' => json_encode($send_data_mentee));

                    if($device_type == "iOS"){

                        $msg = array('message' => $message,'title' => $title, 'sound'=>"default" , 'badge'=> ($mentee_schedule_session_count+$mentee_user_total_chat_count+$mentee_total_unread_goaltask_count) );
                        $fields1 = array('to' => $m->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

                    }else if($device_type == "android"){

                        $fields1 = array('to' => $m->firebase_id,'data' => $send_data_mentee ); // For Android
                    }

                    $result = sendPushNotificationWithV1($fields1);

                    if(!empty($result['name'])){
                        $success_msg_id = $result['name'];
                    }
                }
                DB::table('message_center_notifications')->insert(['message_id'=>$id,'user_type'=>'mentee','user_id'=>$user_id,'device_type'=>$device_type,'firebase_token'=>$firebase_id,'success_msg_id'=>$success_msg_id,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }
        }
    }

    public function delete(Request $request)
    {
    	# code...
    	$id = !empty($request->id)?$request->id:'';

    	if(!empty($id)){
    		try{
    			$id = Crypt::decrypt($id);
    			DB::table('message_center')->where('id',$id)->delete();
    			DB::table('message_center_users')->where('message_id',$id)->delete();

    			session(['success_message'=>'Deleted successfully']);
    			return redirect('/admin/message-center/list');

    		} catch (\ DecryptException $e) {
    			return redirect('/admin/message-center/list');
    		}

    	}else{
    		return redirect('/admin/message-center/list');
    	}
    }

    public function hide(Request $request)
    {
    	# code...
    	$id = !empty($request->id)?$request->id:'';

    	if(!empty($id)){
    		try{
    			$id = Crypt::decrypt($id);
    			DB::table('message_center')->where('id',$id)->update(['hidden'  =>  1]);

    			session(['success_message'=>'Hidden successfully']);
    			return redirect('/admin/message-center/list');

    		} catch (\ DecryptException $e) {
    			return redirect('/admin/message-center/list');
    		}

    	}else{
    		return redirect('/admin/message-center/list');
    	}
    }

    public function unhide(Request $request)
    {
    	# code...
    	$id = !empty($request->id)?$request->id:'';

    	if(!empty($id)){
    		try{
    			$id = Crypt::decrypt($id);
    			DB::table('message_center')->where('id',$id)->update(['hidden'  =>  0]);

    			session(['success_message'=>'Message unhidden successfully']);
    			return redirect('/admin/message-center/list');

    		} catch (\ DecryptException $e) {
    			return redirect('/admin/message-center/list');
    		}

    	}else{
    		return redirect('/admin/message-center/list');
    	}
    }

    public function get_user_search(Request $request)
    {
    	# code...
    	$affiliate_id = !empty($request->affiliate_id)?$request->affiliate_id:'';
    	$user_type = !empty($request->user_type)?$request->user_type:'';
    	$search = !empty($request->search)?$request->search:'';

        $data = array();

        if(!empty($search)){
            $data = DB::table($user_type.' AS m')->select('m.id','m.firstname','m.lastname')
                                        ->where(function($query) use ($search) {
                                            $query->where('m.firstname', 'LIKE', $search.'%')->orWhere('m.lastname', 'LIKE', $search.'%');
                                        });

            if(!empty($affiliate_id)){
            	$data = $data->where('m.assigned_by', $affiliate_id);
            }

            if($user_type == 'mentor'){
                $data = $data->leftJoin('mentor_status','mentor_status.id','m.is_active')->where('mentor_status.view_in_application', 1);

            }else if($user_type == 'mentee'){
                $data = $data->leftJoin('student_status','student_status.id','m.status')->where('student_status.view_in_application', 1);
            }

            $data = $data->orderBy('m.firstname','asc')->get()->toarray();
        }

        return $data;
    	// return Response::json(['user_arr'=>$data]);

    }

    public function message_users(Request $request)
    {
    	$id = !empty($request->id)?$request->id:'';
    	$check_message = DB::table('message_center')->find($id);
    	$message_mentors = DB::table('message_center_users AS m')->select('m.user_id','mentor.firstname','mentor.lastname')->leftJoin('mentor','mentor.id','m.user_id')->where('m.message_id',$id)->where('m.user_type','mentor')->get()->toarray();
    	$message_mentees = DB::table('message_center_users AS m')->select('m.user_id','mentee.firstname','mentee.lastname')->leftJoin('mentee','mentee.id','m.user_id')->where('m.message_id',$id)->where('m.user_type','mentee')->get()->toarray();

    	// echo '<pre>'; print_r($check_message);
    	// echo '<pre>'; print_r($message_mentors);
    	// echo '<pre>'; print_r($message_mentees);

    	return Response::json(['check_message'=>$check_message,'message_mentors'=>$message_mentors,'message_mentees'=>$message_mentees]);

    }
}
