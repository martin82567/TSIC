<?php


namespace App\Http\Controllers\Api\V1\mentor;

use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use File;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class GoalTaskChallengeController extends Controller
{

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

            $chk_user = DB::table('mentor')->where('id',$this->user_id)->first();

            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit(); 
            }

            mentor_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function add(Request $request)
    { 
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

        
        $created_by = $this->user_id;
        
        $mentor_data = DB::table('mentor')->where('id',$created_by)->first();

        $agency_data = DB::table('admins')->where('id',$mentor_data->assigned_by)->first();

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
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $agency_data->id,'staff_id' => $created_by, 'created_date'=> $created_date, 'updated_date'=>$updated_date, 'user_type' => 'mentor']); 
                $message =  'Data added successfully';
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description , 'updated_date'=>$updated_date]);
                $message =  'Data updated successfully';
            }
        }else if($type == "task"){
            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $agency_data->id,'staff_id' => $created_by, 'created_date'=> $created_date , 'updated_date'=>$updated_date , 'user_type' => 'mentor']); 
                $message =  'Data added successfully';
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description , 'updated_date'=>$updated_date]);
                $message =  'Data updated successfully';
            }

        }else{
            
            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $agency_data->id,'staff_id' => $created_by, 'created_date'=> $created_date , 'updated_date'=>$updated_date , 'user_type' => 'mentor']); 
                $message =  'Data added successfully';
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
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function list(Request $request)
    {
        $type = Input::get('type');
        if(empty($type)){
            return response()->json(['status'=>false, 'message' => "Please give type.", 'data' => array() ]);
        }

        $search_text = $request->search_text;
        if(empty($search_text)){
            $search_text = '';
        }


        $data_list = DB::table('goaltask')
                ->where('staff_id',$this->user_id)
                ->where('user_type','mentor')
                ->where('type',$type)
                ->where('end_date','>=',date('Y-m-d'))
                ->orderBy('updated_date', 'desc')
                ->get();

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
                $updated_date = DateTime::createFromFormat("Y-m-d H:i:s" , $d->updated_date); 
                $d->updated_date = $updated_date->format('m-d-Y H:i:s');

            }
        }


        return response()->json(['status'=>true, 'message' => '', 'data' => array('datalist' => $data_list)]);
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function assign(Request $request)
    {
        $goaltask_id = !empty($request->goaltask_id)?$request->goaltask_id:'';
        $type = !empty($request->type)?$request->type:'';
        $mentee_id = !empty($request->mentee_id)?$request->mentee_id:'';

        if(empty($goaltask_id))
            return response()->json([ 'status'=>false, 'message' => "Please add a goal,task or challenge", 'data' => array() ]);

        if(empty($type))
            return response()->json([ 'status'=>false, 'message' => "Please add type", 'data' => array() ]);

        if(empty($mentee_id))
            return response()->json([ 'status'=>false, 'message' => "Please add mentee", 'data' => array() ]);

        $chk_goaltask = DB::table('goaltask')->where('id', $goaltask_id)->where('type', $type)->first();

        if(empty($chk_goaltask))
            return response()->json([ 'status'=>false, 'message' => "No ".$type." found ", 'data' => array() ]);



        if($type == 'goal'){
            $exist_assign_goal = DB::table('assign_goal')->where('victim_id',$mentee_id)->where('goaltask_id',$goaltask_id)->first();

            if(empty($exist_assign_goal)){
                $id = DB::table('assign_goal')->insertGetId(['victim_id' => $mentee_id , 'goaltask_id' => $goaltask_id , 'point' => 0 , 'status' => 0 , 'note' => '' , 'begin_time' => '0000-00-00 00:00:00' , 'complated_time' => '0000-00-00 00:00:00']);

                return response()->json([ 'status'=>true, 'message' => "This mentee successfully assigned in this ".$type." ", 'data' => array('assign_id' => $id , 'type' => $type) ]);

            }else{
                return response()->json([ 'status'=>false, 'message' => "This mentee already assigned in this ".$type." ", 'data' => array() ]);
            }

        }else if($type == 'task'){
            $exist_assign_task = DB::table('assign_task')->where('victim_id',$mentee_id)->where('goaltask_id',$goaltask_id)->first();

            if(empty($exist_assign_task)){
                $id = DB::table('assign_task')->insertGetId(['victim_id' => $mentee_id , 'goaltask_id' => $goaltask_id , 'point' => 0 , 'status' => 0 , 'note' => '' , 'begin_time' => '0000-00-00 00:00:00' , 'complated_time' => '0000-00-00 00:00:00']);

            return response()->json([ 'status'=>true, 'message' => "This mentee successfully assigned in this ".$type." ", 'data' => array('assign_id' => $id , 'type' => $type) ]);

            }else{
                return response()->json([ 'status'=>false, 'message' => "This mentee already assigned in this ".$type." ", 'data' => array() ]);
            }

        }else if($type == 'challenge'){
            $exist_assign_challenge = DB::table('assign_challenge')->where('victim_id',$mentee_id)->where('goaltask_id',$goaltask_id)->first();

            if(empty($exist_assign_challenge)){
                $id = DB::table('assign_challenge')->insertGetId(['victim_id' => $mentee_id , 'goaltask_id' => $goaltask_id , 'point' => 0 , 'status' => 0 , 'note' => '' , 'begin_time' => '0000-00-00 00:00:00' , 'complated_time' => '0000-00-00 00:00:00']);

            return response()->json([ 'status'=>true, 'message' => "This mentee successfully assigned in this ".$type." ", 'data' => array('assign_id' => $id , 'type' => $type) ]);

            }else{
                return response()->json([ 'status'=>false, 'message' => "This mentee already assigned in this ".$type." ", 'data' => array() ]);
            }

        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function listmentee(Request $request)
    {
        $goaltask_id = !empty($request->goaltask_id)?$request->goaltask_id:'';
        $type = !empty($request->type)?$request->type:'';

        if(empty($goaltask_id))
            return response()->json([ 'status'=>false, 'message' => "Please add a goal,task or challenge", 'data' => array() ]);

        if(empty($type))
            return response()->json([ 'status'=>false, 'message' => "Please add type", 'data' => array() ]);

        $chk_goaltask = DB::table('goaltask')->where('id', $goaltask_id)->where('type', $type)->first();

        if(empty($chk_goaltask))
            return response()->json([ 'status'=>false, 'message' => "No ".$type." found ", 'data' => array() ]);


        if($type == 'goal'){

            $assign_goal = array();
            $assign_goal = DB::table('assign_goal')->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.image','assign_goal.id AS assign_id','assign_goal.status AS assign_status')->join('mentee', 'mentee.id', 'assign_goal.victim_id')->where('assign_goal.goaltask_id', $goaltask_id)->get()->toarray();

            return response()->json([ 'status'=>true, 'message' => "These are the mentees of this ".$type." ", 'data' => array('mentee_list' => $assign_goal ) ]);

        }else if($type == 'task'){

            $assign_task = array();
            $assign_task = DB::table('assign_task')->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.image','assign_task.id AS assign_id','assign_task.status AS assign_status')->join('mentee', 'mentee.id', 'assign_task.victim_id')->where('assign_task.goaltask_id', $goaltask_id)->get()->toarray();

            return response()->json([ 'status'=>true, 'message' => "These are the mentees of this ".$type." ", 'data' => array('mentee_list' => $assign_task ) ]);

        }else if($type == 'challenge'){

            $assign_challenge = array();
            $assign_challenge = DB::table('assign_challenge')->select('mentee.id','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email','mentee.image','assign_challenge.id AS assign_id','assign_challenge.status AS assign_status')->join('mentee', 'mentee.id', 'assign_challenge.victim_id')->where('assign_challenge.goaltask_id', $goaltask_id)->get()->toarray();

            return response()->json([ 'status'=>true, 'message' => "These are the mentees of this ".$type." ", 'data' => array('mentee_list' => $assign_challenge ) ]);

        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/
    public function delmentee(Request $request)
    {
        $goaltask_id = !empty($request->goaltask_id)?$request->goaltask_id:'';
        $type = !empty($request->type)?$request->type:'';
        $mentee_id = !empty($request->mentee_id)?$request->mentee_id:'';

        if(empty($goaltask_id))
            return response()->json([ 'status'=>false, 'message' => "Please add a goal,task or challenge", 'data' => array() ]);

        if(empty($type))
            return response()->json([ 'status'=>false, 'message' => "Please add type", 'data' => array() ]);

        if(empty($mentee_id))
            return response()->json([ 'status'=>false, 'message' => "Please add mentee", 'data' => array() ]);

        $chk_goaltask = DB::table('goaltask')->where('id', $goaltask_id)->where('type', $type)->first();

        if(empty($chk_goaltask))
            return response()->json([ 'status'=>false, 'message' => "No ".$type." found ", 'data' => array() ]);


        if($type == 'goal'){

            $is_exist_goal = DB::table('assign_goal')->where('goaltask_id', $goaltask_id)->where('victim_id', $mentee_id)->first();
            if(!empty($is_exist_goal)){
                DB::table('assign_goal')->where('id',$is_exist_goal->id)->delete();
                return response()->json([ 'status'=>true, 'message' => "Mentee removed from this ".$type." ", 'data' => array() ]);
            }else{
                return response()->json([ 'status'=>false, 'message' => "No mentee found in this ".$type." ", 'data' => array() ]);
            }

        }else if($type == 'task'){

            $is_exist_task = DB::table('assign_task')->where('goaltask_id', $goaltask_id)->where('victim_id', $mentee_id)->first();
            if(!empty($is_exist_task)){
                DB::table('assign_task')->where('id',$is_exist_task->id)->delete();
                return response()->json([ 'status'=>true, 'message' => "Mentee removed from this ".$type." ", 'data' => array() ]);
            }else{
                return response()->json([ 'status'=>false, 'message' => "No mentee found in this ".$type." ", 'data' => array() ]);
            }

        }else if($type == 'challenge'){

            $is_exist_challenge = DB::table('assign_challenge')->where('goaltask_id', $goaltask_id)->where('victim_id', $mentee_id)->first();
            if(!empty($is_exist_challenge)){
                DB::table('assign_challenge')->where('id',$is_exist_challenge->id)->delete();
                return response()->json([ 'status'=>true, 'message' => "Mentee removed from this ".$type." ", 'data' => array() ]);
            }else{
                return response()->json([ 'status'=>false, 'message' => "No mentee found in this ".$type." ", 'data' => array() ]);
            }

        }
    }
/**++++++++++++++++++++++++++++++++++++++++++++++**/       
}