<?php


namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Hash;
use Artisan;
use Config;
use Auth;
use DB;
use DateTime;
use DateTimeZone;

use Illuminate\Support\Facades\Storage;


class GoaltaskController extends Controller
{
    protected $user_id;

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

            $chk_user = DB::table('mentee')->select('*')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit(); 
            }

            if(!empty($chk_user->is_logged_out)){
                response()->json(array('status'=>false,'message'=>"Logged Out"))->send();
                exit(); 
            }

            mentee_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    }

    public function add(Request $request)
    { 
        $user_id = $this->user_id;

        $id = Input::get('id');
        $type = Input::get('type');
        $name = Input::get('name');
        $description = Input::get('description');
        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type.", 'data' => array() ]);
        }

        if(empty($name)){
            return response()->json(['status'=>false, 'message' => "Please give name.", 'data' => array() ]);
        }

        if(empty($description)){
            return response()->json(['status'=>false, 'message' => "Please give description.", 'data' => array() ]);
        }

        if(empty($start_date)){
            return response()->json(['status'=>false, 'message' => "Please give start date.", 'data' => array() ]);
        }

        if(empty($end_date)){
            return response()->json(['status'=>false, 'message' => "Please give end date.", 'data' => array() ]);
        }

        
        if(empty($start_date)){
            $start_date = '';
        }else{
            $start_date = DateTime::createFromFormat("m-d-Y" , $start_date);
            $start_date->format('Y-m-d');
        }
        
        if(empty($end_date)){
            $end_date = '';
        }else{
            $end_date = DateTime::createFromFormat("m-d-Y" , $end_date);
            $end_date->format('Y-m-d');
        }

        if($start_date>$end_date){
            return response()->json(['status'=>false, 'message' => "End date must be greater then Start date.", 'data' => array() ]);
        }

        $status = 1;

        
        $created_by = $user_id;
        
        $mentee_data = DB::table('mentee')->where('id',$created_by)->first();

        $agency_data = DB::table('admins')->where('id',$mentee_data->assigned_by)->first();

        $timezone = $agency_data->timezone;

        

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s') ;
            $updated_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
            $updated_date = date('Y-m-d H:i:s') ;
        }
        

        if($type == "goal"){

            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $agency_data->id,'staff_id' => $created_by, 'created_date'=> $created_date, 'updated_date'=>$updated_date, 'user_type' => 'mentee']); 
                $aid = DB::table('assign_goal')->insertGetId(['victim_id' => $created_by , 'goaltask_id' => $id , 'point' => 0 , 'status' => 0 , 'note' => '' , 'begin_time' => '0000-00-00 00:00:00' , 'complated_time' => '0000-00-00 00:00:00']);
                $message =  'Data added successfully';
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description , 'updated_date'=>$updated_date]);
                $message =  'Data updated successfully';
            }
        }else if($type == "task"){
            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $agency_data->id,'staff_id' => $created_by, 'created_date'=> $created_date , 'updated_date'=>$updated_date , 'user_type' => 'mentee']); 
                $id = DB::table('assign_task')->insertGetId(['victim_id' => $created_by , 'goaltask_id' => $id , 'point' => 0 , 'status' => 0 , 'note' => '' , 'begin_time' => '0000-00-00 00:00:00' , 'complated_time' => '0000-00-00 00:00:00']);
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description , 'updated_date'=>$updated_date]);
                $message =  'Data updated successfully';
            }

        }

        if(!empty($request->images)) {    
            $i = 0;  
            foreach($request->images as $all_images){                        
                $img = $all_images;

                $image_name = time().uniqid(rand());
                $image_name = $image_name.'.'.$img->getClientOriginalExtension();

                // $storage_path = public_path() . '/uploads/goaltask/';
                // $img->move($storage_path,$image_name);
                $filePath = 'goaltask/' . $image_name;
                Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');


                DB::table('task_files')->insertGetId(['goaltask_id' => $id, 'file_name' => $image_name, 'added_by' => Auth::user()->id]); 
                $i++;
            }                     
        }
        return response()->json(['status'=>true, 'message' => $message, 'data' => array() ]);
    }

    public function getlist(Request $request)
    {
        $user_id = $this->user_id;

        $type = $request->type;

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $search_text = $request->search_text;
        if(empty($search_text)){
            $search_text = '';
        }

        if($type == "task"){
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_task.id as assign_id','assign_task.status as datastatus','assign_task.begin_time','assign_task.complated_time','assign_task.note')
                    ->where('assign_task.victim_id',$user_id)
                    ->where('goaltask.status',1)
                    ->where('assign_task.status','!=',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
            DB::table(GOALTASK_NOTIFICATION)->where('user_type','mentee')->where('user_id',$user_id)->where('type','task')->update(['is_read' => 1]);
        }else if($type == "goal"){
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_goal', 'assign_goal.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_goal.id as assign_id','assign_goal.status as datastatus','assign_goal.begin_time','assign_goal.complated_time','assign_goal.note')
                    ->where('assign_goal.victim_id',$user_id)
                    ->where('goaltask.status',1)
                    ->where('assign_goal.status','!=',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
            DB::table(GOALTASK_NOTIFICATION)->where('user_type','mentee')->where('user_id',$user_id)->where('type','goal')->update(['is_read' => 1]);
        }else{
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_challenge', 'assign_challenge.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_challenge.id as assign_id','assign_challenge.status as datastatus','assign_challenge.begin_time','assign_challenge.complated_time','assign_challenge.note')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->where('goaltask.status',1)
                    ->where('assign_challenge.status','!=',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
        }

        if(!empty($data_list->toarray())){
            $data_list = $data_list->toarray();
        }else{
            $data_list = array();
        }

        if(!empty($data_list)){
            foreach($data_list as $d){
                if($d->start_date != '0000-00-00'){
                    $start_date = DateTime::createFromFormat("Y-m-d" , $d->start_date);                
                    $d->start_date = $start_date->format('m-d-Y'); 
                }
                if($d->end_date != '0000-00-00'){
                    $end_date = DateTime::createFromFormat("Y-m-d" , $d->end_date);                
                    $d->end_date = $end_date->format('m-d-Y'); 
                }
                if($d->created_date != '0000-00-00 00:00:00'){
                    $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $d->created_date);                
                    $d->created_date = $created_date->format('m-d-Y H:i:s'); 
                }
                if($d->updated_date != '0000-00-00 00:00:00'){
                    $updated_date = DateTime::createFromFormat("Y-m-d H:i:s" , $d->updated_date);                
                    $d->updated_date = $updated_date->format('m-d-Y H:i:s'); 
                }
                if($d->begin_time != '0000-00-00 00:00:00'){
                    $begin_time = DateTime::createFromFormat("Y-m-d H:i:s" , $d->begin_time);                
                    $d->begin_time = $begin_time->format('m-d-Y H:i:s'); 
                }
                if($d->complated_time != '0000-00-00 00:00:00'){
                    $complated_time = DateTime::createFromFormat("Y-m-d H:i:s" , $d->complated_time);                
                    $d->complated_time = $complated_time->format('m-d-Y H:i:s'); 
                }


                /*++++++++++Add mentor name++++++++*/
                $mentor_name = "";
                if($d->user_type == 'mentor' && !empty($d->staff_id)){
                    $mentor_data = DB::table('mentor')->select('*')->where('id',$d->staff_id)->first();

                    $mentor_firstname = !empty($mentor_data->firstname)?$mentor_data->firstname:'';
                    $mentor_middlename = !empty($mentor_data->middlename)?$mentor_data->middlename:'';
                    $mentor_lastname = !empty($mentor_data->lastname)?$mentor_data->lastname:'';
                    
                    $mentor_name = $mentor_firstname.' '.$mentor_middlename.' '.$mentor_lastname;

                }

                $d->mentor_name = $mentor_name;
                /*+++++++++++++++++++++++++++++++++*/

            }
        }
        
        $data = array();
        $data['datalist'] = $data_list;


        return response()->json(['status'=>true, 'message' => '', 'data' => $data]);
    }


    public function goaltaskcompltelist(Request $request)
    {
        $user_id = $this->user_id;

        $type = $request->type;

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $search_text = $request->search_text;
        if(empty($search_text)){
            $search_text = '';
        }

        if($type == "task"){
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_task.id as assign_id','assign_task.status as datastatus','assign_task.begin_time','assign_task.complated_time','assign_task.note')
                    ->where('assign_task.victim_id',$user_id)
                    ->where('assign_task.status',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
        }else if($type == "goal"){
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_goal', 'assign_goal.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_goal.id as assign_id','assign_goal.status as datastatus','assign_goal.begin_time','assign_goal.complated_time','assign_goal.note')
                    ->where('assign_goal.victim_id',$user_id)
                    ->where('assign_goal.status',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
        }else{
            $data_list = DB::table('goaltask')
                    ->leftjoin('assign_challenge', 'assign_challenge.goaltask_id', '=', 'goaltask.id')
                    ->select('goaltask.*','assign_challenge.id as assign_id','assign_challenge.status as datastatus','assign_challenge.begin_time','assign_challenge.complated_time','assign_challenge.note')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->where('assign_challenge.status',2)
                    ->where(function ($q) use ($search_text) {
                        $q->where('goaltask.name', 'like', '%'.$search_text.'%');
                    })
                    ->orderBy('goaltask.updated_date','desc')
                    ->get();
        }

        if(!empty($data_list->toarray())){
            $data_list = $data_list->toarray();
        }else{
            $data_list = array();
        }

        if(!empty($data_list)){
            foreach($data_list as $d){
                if($d->start_date != '0000-00-00'){
                    $start_date = DateTime::createFromFormat("Y-m-d" , $d->start_date);                
                    $d->start_date = $start_date->format('m-d-Y'); 
                }
                if($d->end_date != '0000-00-00'){
                    $end_date = DateTime::createFromFormat("Y-m-d" , $d->end_date);                
                    $d->end_date = $end_date->format('m-d-Y'); 
                }
                if($d->created_date != '0000-00-00 00:00:00'){
                    $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $d->created_date);                
                    $d->created_date = $created_date->format('m-d-Y H:i:s'); 
                }
                if($d->updated_date != '0000-00-00 00:00:00'){
                    $updated_date = DateTime::createFromFormat("Y-m-d H:i:s" , $d->updated_date);                
                    $d->updated_date = $updated_date->format('m-d-Y H:i:s'); 
                }
                if($d->begin_time != '0000-00-00 00:00:00'){
                    $begin_time = DateTime::createFromFormat("Y-m-d H:i:s" , $d->begin_time);                
                    $d->begin_time = $begin_time->format('m-d-Y H:i:s'); 
                }
                if($d->complated_time != '0000-00-00 00:00:00'){
                    $complated_time = DateTime::createFromFormat("Y-m-d H:i:s" , $d->complated_time);                
                    $d->complated_time = $complated_time->format('m-d-Y H:i:s'); 
                }

                /*++++++++++Add mentor name++++++++*/
                $mentor_name = "";
                if($d->user_type == 'mentor' && !empty($d->staff_id)){
                    $mentor_data = DB::table('mentor')->select('*')->where('id',$d->staff_id)->first();

                    $mentor_firstname = !empty($mentor_data->firstname)?$mentor_data->firstname:'';
                    $mentor_middlename = !empty($mentor_data->middlename)?$mentor_data->middlename:'';
                    $mentor_lastname = !empty($mentor_data->lastname)?$mentor_data->lastname:'';
                    
                    $mentor_name = $mentor_firstname.' '.$mentor_middlename.' '.$mentor_lastname;

                }

                $d->mentor_name = $mentor_name;
                /*+++++++++++++++++++++++++++++++++*/

            }
        }


        return response()->json(['status'=>true, 'message' => '', 'data' => array('datalist' => $data_list)]);
    }


    public function getdetails(Request $request)
    {
        $user_id = $this->user_id;
        $type = $request->type;
        
        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $assign_id = $request->assign_id;

        // if(empty($assign_id)){
        //     return response()->json(['status'=>false, 'message' => "Please give assign id", 'data' => array()]);
        // }

        if($type == 'task'){
            $datadetails = DB::table('goaltask')
                ->join('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                ->select('goaltask.*','assign_task.id as assign_id','assign_task.status as datastatus','assign_task.begin_time','assign_task.complated_time')
                ->where('assign_task.victim_id',$user_id)
                ->where('goaltask.type',$type)
                ->where('assign_task.id',$assign_id)
                ->first();
             
        }else if($type == "goal"){
            $datadetails = DB::table('goaltask')
                ->join('assign_goal', 'assign_goal.goaltask_id', '=', 'goaltask.id')
                ->select('goaltask.*','assign_goal.id as assign_id','assign_goal.status as datastatus','assign_goal.begin_time','assign_goal.complated_time')
                ->where('assign_goal.victim_id',$user_id)
                ->where('goaltask.type',$type)
                ->where('assign_goal.id',$assign_id)
                ->first();
        }else{
            $datadetails = DB::table('goaltask')
                ->join('assign_challenge', 'assign_challenge.goaltask_id', '=', 'goaltask.id')
                ->select('goaltask.*','assign_challenge.id as assign_id','assign_challenge.status as datastatus','assign_challenge.begin_time','assign_challenge.complated_time')
                ->where('assign_challenge.victim_id',$user_id)
                ->where('goaltask.type',$type)
                ->where('assign_challenge.id',$assign_id)
                ->first();
        }

        if(empty($datadetails)){
            return response()->json(['status'=>false, 'message' => "Please give  assign id", 'data' => array()]);
        }

        
        if($datadetails->start_date != '0000-00-00'){
            $start_date = DateTime::createFromFormat("Y-m-d" , $datadetails->start_date);                
            $datadetails->start_date = $start_date->format('m-d-Y'); 
        }
        if($datadetails->end_date != '0000-00-00'){
            $end_date = DateTime::createFromFormat("Y-m-d" , $datadetails->end_date);                
            $datadetails->end_date = $end_date->format('m-d-Y'); 
        }
        if($datadetails->created_date != '0000-00-00 00:00:00'){
            $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $datadetails->created_date);                
            $datadetails->created_date = $created_date->format('m-d-Y H:i:s'); 
        }
        if($datadetails->updated_date != '0000-00-00 00:00:00'){
            $updated_date = DateTime::createFromFormat("Y-m-d H:i:s" , $datadetails->updated_date);                
            $datadetails->updated_date = $updated_date->format('m-d-Y H:i:s'); 
        }
        if($datadetails->begin_time != '0000-00-00 00:00:00'){
            $begin_time = DateTime::createFromFormat("Y-m-d H:i:s" , $datadetails->begin_time);                
            $datadetails->begin_time = $begin_time->format('m-d-Y H:i:s'); 
        }
        if($datadetails->complated_time != '0000-00-00 00:00:00'){
            $complated_time = DateTime::createFromFormat("Y-m-d H:i:s" , $datadetails->complated_time);                
            $datadetails->complated_time = $complated_time->format('m-d-Y H:i:s'); 
        }

        /*++++++++++Add mentor name++++++++*/
        $mentor_name = "";
        if($datadetails->user_type == 'mentor' && !empty($datadetails->staff_id)){
            $mentor_data = DB::table('mentor')->select('*')->where('id',$datadetails->staff_id)->first();

            $mentor_firstname = !empty($mentor_data->firstname)?$mentor_data->firstname:'';
            $mentor_middlename = !empty($mentor_data->middlename)?$mentor_data->middlename:'';
            $mentor_lastname = !empty($mentor_data->lastname)?$mentor_data->lastname:'';
            
            $mentor_name = $mentor_firstname.' '.$mentor_middlename.' '.$mentor_lastname;

        }

        $datadetails->mentor_name = $mentor_name;
        /*+++++++++++++++++++++++++++++++++*/

        

        $datalist = DB::table('goaltaskuserfiles')
            ->select('*')
            ->where('goaltaskuserfiles.goaltask_id',$datadetails->id)
            ->where('goaltaskuserfiles.added_by',$user_id)
            ->get();
        $useruploadedfile = $datalist->toarray();
        if(!empty($useruploadedfile)){
            foreach($useruploadedfile as $ud){
                if($ud->created_date != '0000-00-00 00:00:00'){
                    $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $ud->created_date);                
                    $ud->created_date = $created_date->format('m-d-Y H:i:s'); 
                }
            }
            $datadetails->useruploadedfile = $useruploadedfile;
        }else{
            $datadetails->useruploadedfile = array();
        }  

        $datalist = DB::table('task_files')
                    ->select('*')
                    ->where('task_files.goaltask_id',$datadetails->id)
                    ->get();
        $adminuploadedfile = $datalist->toarray();
        if(!empty($adminuploadedfile)){
            foreach($adminuploadedfile as $ad){
                if($ad->created_date != '0000-00-00 00:00:00'){
                    $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $ad->created_date);                
                    $ad->created_date = $created_date->format('m-d-Y H:i:s'); 
                }
            }
            $datadetails->adminuploadedfile = $adminuploadedfile;
        }else{
            $datadetails->adminuploadedfile = array();
        }  

        $datalist = DB::table('goaltask_note')
                    ->select('*')
                    ->where('goaltask_note.goaltask_id',$datadetails->id)
                    ->where('goaltask_note.victim_id',$user_id)
                    ->get();
        $notes = $datalist->toarray();
        if(!empty($notes)){
            foreach($notes as $n){
                if($n->created_date != '0000-00-00 00:00:00'){
                    $created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $n->created_date);                
                    $n->created_date = $created_date->format('m-d-Y H:i:s'); 
                }
            }
            $datadetails->notes = $notes;
        }else{
            $datadetails->notes = array();
        }  

        return response()->json(['status'=>true, 'message' => '', 'data' => array('datadetails' => $datadetails)]);
    }


    public function actiongoaltask(Request $request)
    {
        $user_id = $this->user_id;
        $user_data = DB::table('mentee')->where('id',$user_id)->first();
        $timezone = !empty($user_data->timezone)?$user_data->timezone:'';

        $type = $request->type;

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $status = $request->status;

        if(empty($status)){
            return response()->json(['status'=>false, 'message' => "Please give status", 'data' => array()]);
        }

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        if($type == "task"){
            $datadetails = DB::table('assign_task')
                        ->select('*')
                        ->where('assign_task.victim_id',$user_id)
                        ->where('assign_task.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            if($status == 1){               
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $begin_time = date('Y-m-d H:i:s');
                }else{
                    $begin_time = date('Y-m-d H:i:s');
                }

                DB::table('assign_task')
                    ->where('assign_task.victim_id',$user_id)
                    ->where('assign_task.goaltask_id',$id)
                    ->update(['status' => $status, 'begin_time' => $begin_time]);
            }else if($status == 2){                
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $complated_time = date('Y-m-d H:i:s');
                }else{
                    $complated_time = date('Y-m-d H:i:s');
                }

                DB::table('assign_task')
                    ->where('assign_task.victim_id',$user_id)
                    ->where('assign_task.goaltask_id',$id)
                    ->update(['status' => $status, 'complated_time' => $complated_time]);
            }else{
                DB::table('assign_task')
                    ->where('assign_task.victim_id',$user_id)
                    ->where('assign_task.goaltask_id',$id)
                    ->update(['status' => 1, 'complated_time' => '']);
            }
        }else if($type == "goal"){
            $datadetails = DB::table('assign_goal')
                        ->select('*')
                        ->where('assign_goal.victim_id',$user_id)
                        ->where('assign_goal.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            if($status == 1){
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $begin_time = date('Y-m-d H:i:s');
                }else{
                    $begin_time = date('Y-m-d H:i:s');
                }

                DB::table('assign_goal')
                    ->where('assign_goal.victim_id',$user_id)
                    ->where('assign_goal.goaltask_id',$id)
                    ->update(['status' => $status, 'begin_time' => $begin_time]);
            }else if($status == 2){
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $complated_time = date('Y-m-d H:i:s');
                }else{
                    $complated_time = date('Y-m-d H:i:s');
                }

                DB::table('assign_goal')
                    ->where('assign_goal.victim_id',$user_id)
                    ->where('assign_goal.goaltask_id',$id)
                    ->update(['status' => $status, 'complated_time' => $complated_time]);
            }else{
                DB::table('assign_goal')
                    ->where('assign_goal.victim_id',$user_id)
                    ->where('assign_goal.goaltask_id',$id)
                    ->update(['status' => 1, 'complated_time' => '']);
            }
        }else{
            $datadetails = DB::table('assign_challenge')
                        ->select('*')
                        ->where('assign_challenge.victim_id',$user_id)
                        ->where('assign_challenge.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            if($status == 1){
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $begin_time = date('Y-m-d H:i:s');
                }else{
                    $begin_time = date('Y-m-d H:i:s');
                }

                DB::table('assign_challenge')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->where('assign_challenge.goaltask_id',$id)
                    ->update(['status' => $status, 'begin_time' => $begin_time]);
            }else if($status == 2){
                
                if(!empty($timezone)){
                    date_default_timezone_set($timezone);
                    $complated_time = date('Y-m-d H:i:s');
                }else{
                    $complated_time = date('Y-m-d H:i:s');
                }
                
                DB::table('assign_challenge')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->where('assign_challenge.goaltask_id',$id)
                    ->update(['status' => $status, 'complated_time' => $complated_time]);
                
            }else{
                DB::table('assign_challenge')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->where('assign_challenge.goaltask_id',$id)
                    ->update(['status' => 1, 'complated_time' => '']);
            }
        }

        return response()->json(['status'=>true, 'message' => 'Status Changed Successfully.', 'data' => array()]);
    }

    public function notesavegoaltask(Request $request)
    {
        $user_id = $this->user_id;

        $type = $request->type;
        $title = $request->title;

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $note = $request->note;

        if(empty($note)){
            return response()->json(['status'=>false, 'message' => "Please give note", 'data' => array()]);
        }

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        if($type == "task"){
            $datadetails = DB::table('assign_task')
                        ->select('*')
                        ->where('assign_task.victim_id',$user_id)
                        ->where('assign_task.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            
        }else if($type == "goal"){
            $datadetails = DB::table('assign_goal')
                        ->select('*')
                        ->where('assign_goal.victim_id',$user_id)
                        ->where('assign_goal.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
           
        }else{
            $datadetails = DB::table('assign_challenge')
                        ->select('*')
                        ->where('assign_challenge.victim_id',$user_id)
                        ->where('assign_challenge.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            
        }
        
    
        DB::table('goaltask_note')->insert(['victim_id' => $user_id, 'goaltask_id' => $id, 'note' => $note, 'title' => $title , 'type' => $type, 'created_date' => date('Y-m-d H:i:s')]);

        $datalist = DB::table('goaltask_note')
                        ->select('*')
                        ->where('goaltask_note.goaltask_id',$id)
                        ->where('goaltask_note.victim_id',$user_id)
                        ->get();
        if(!empty($datalist->toarray())){
            $datalist = $datalist->toarray();
        }else{
            $datalist = array();
        } 

        if(!empty($datalist)){
            foreach($datalist as $data){
                // $date = "10/24/2014";
                if($data->created_date != '0000-00-00 00:00:00'){
                    $date = DateTime::createFromFormat("Y-m-d H:i:s" , $data->created_date);                
                    $data->created_date = $date->format('m-d-Y H:i:s');
                }
                
            }
        } 
       

        return response()->json(['status'=>true, 'message' => 'Note added Successfully.', 'data' => array('notes' => $datalist)]);
    }



    public function filesavegoaltask(Request $request)
    {
        $user_id = $this->user_id;

        $type = $request->type;

        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type", 'data' => array()]);
        }

        $files = $request->files;

        if(empty($files)){
            return response()->json(['status'=>false, 'message' => "Please give files", 'data' => array()]);
        }

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        if($type == "task"){
            $datadetails = DB::table('assign_task')
                        ->select('*')
                        ->where('assign_task.victim_id',$user_id)
                        ->where('assign_task.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            
        }else if($type == "goal"){
            $datadetails = DB::table('assign_goal')
                        ->select('*')
                        ->where('assign_goal.victim_id',$user_id)
                        ->where('assign_goal.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
           
        }else{
            $datadetails = DB::table('assign_challenge')
                        ->select('*')
                        ->where('assign_challenge.victim_id',$user_id)
                        ->where('assign_challenge.goaltask_id',$id)
                        ->first();
            if(empty($datadetails)){
                return response()->json(['status'=>false, 'message' => 'This '.$type.' is not assign to you.', 'data' => array()]);
            }
            
        }

        //dd($request->files);

        if(!empty($request->files)) {    
            $i = 0;  
            foreach($request->files as $key  => $all_files){   
                for($i = 0;$i<count($all_files);$i++){               
                    $img = $all_files[$i];
        
                    $image_name = time().uniqid(rand());
                    $image_name = $image_name.'.'.$img->getClientOriginalExtension();
        
                    // $storage_path = public_path() . '/uploads/goaltask/';
                    // $img->move($storage_path,$image_name);

                    $filePath = 'goaltask/' . $image_name;
                    Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');
        
                    DB::table('goaltaskuserfiles')->insertGetId(['goaltask_id' => $id, 'file_name' => $image_name, 'added_by' => $user_id , 'created_date' => date('Y-m-d H:i:s')]); 
                   
                }
            }                     
        }

        $datalist = DB::table('goaltaskuserfiles')
                                    ->select('*')
                                    ->where('goaltaskuserfiles.goaltask_id',$id)
                                    ->where('goaltaskuserfiles.added_by',$user_id)
                                    ->get();
        if(!empty($datalist->toarray())){
            $datalist = $datalist->toarray();
        }else{
            $datalist = array();
        }  

        if(!empty($datalist)){
            foreach($datalist as $data){
                // $date = "10/24/2014";
                if($data->created_date != '0000-00-00 00:00:00'){
                    $date = DateTime::createFromFormat("Y-m-d H:i:s" , $data->created_date);                
                    $data->created_date = $date->format('m-d-Y H:i:s');
                }
                
            }
        } 

    

        return response()->json(['status'=>true, 'message' => 'Files added Successfully.', 'data' => array('useruploadedfile' => $datalist)]);
    }

    public function filedeletegoaltask(Request $request,$goaltaskuserfiles_id)
    {
        $user_id = $this->user_id;

        if(empty($goaltaskuserfiles_id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        $datadetails = DB::table('goaltaskuserfiles')
                            ->select('*')
                            ->where('goaltaskuserfiles.added_by',$user_id)
                            ->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)
                            ->first();
        if(empty($datadetails)){
            return response()->json(['status'=>false, 'message' => 'This is not assign to you.', 'data' => array()]);
        }

        DB::table('goaltaskuserfiles')->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)->delete();
        return response()->json(['status'=>true, 'message' => 'Files deleted Successfully.', 'data' => array()]);
    }

}