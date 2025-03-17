<?php
namespace App\Http\Controllers\Admin;
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

class GoaltaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    /**
     * show dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->search;
        if(Auth::user()->type == 1){
            $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 1)
                            ->orderBy('id', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 2){            
            $id = Auth::user()->id;            
            $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 1)
                            ->where(function ($q) use ($id) {
                                $q->where('created_by', $id);
                            })
                            ->orderBy('id', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 3){
            $admin_parent = DB::table('admins')->where('id',Auth::user()->parent_id)->first();
            if($admin_parent->type == 1){
                $goaltask_arr = DB::table('goaltask')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                    ->orWhere('description','like', '%'.$query.'%');
                                })
                                ->where('status', 1)
                                ->orderBy('id', 'desc')
                                ->paginate(15);
            }else{
                $parent_id = Auth::user()->parent_id;
                $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 1)
                            ->where(function ($q) use ($parent_id) {
                                $q->where('created_by', $parent_id);
                            })
                            ->orderBy('id', 'desc')
                            ->paginate(15);
            }

        }
        
        
        return view('admin.goaltask.list',['goaltask_arr' => $goaltask_arr]);
    }

    public function inactive_goaltask(Request $request)
    {
        $query = $request->search;
        if(Auth::user()->type == 1){
            $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 0)
                            ->orderBy('id', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 2){            
            $id = Auth::user()->id;            
            $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 0)
                            ->where(function ($q) use ($id) {
                                $q->where('created_by', $id);
                            })
                            ->orderBy('id', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 3){
            $admin_parent = DB::table('admins')->where('id',Auth::user()->parent_id)->first();
            if($admin_parent->type == 1){
                $goaltask_arr = DB::table('goaltask')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                    ->orWhere('description','like', '%'.$query.'%');
                                })
                                ->where('status', 0)
                                ->orderBy('id', 'desc')
                                ->paginate(15);
            }else{
                $parent_id = Auth::user()->parent_id;
                $goaltask_arr = DB::table('goaltask')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%')
                                ->orWhere('description','like', '%'.$query.'%');
                            })
                            ->where('status', 0)
                            ->where(function ($q) use ($parent_id) {
                                $q->where('created_by', $parent_id);
                            })
                            ->orderBy('id', 'desc')
                            ->paginate(15);
            }

        }
        
        $victims_list = DB::table('mentee')->where('status',1)->get();
        return view('admin.goaltask.list',['goaltask_arr' => $goaltask_arr,'victims_list' => $victims_list]);
    }

    public function add($id)
    {   
        try {
            $affiliates = array();
            $id = Crypt::decrypt($id);
            $goaltask_details = DB::table('goaltask')->where('id',$id)->first();
            // print_r($goaltask_details); die;
            $admin_files_details = DB::table('admin_files')->where('admin_id',$id)->get();
            $frequency_arr = DB::table('frequency')->where('status',1)->get();

            $task_files = DB::table('task_files')->where('goaltask_id',$id)->get();

            $affiliates = DB::table('admins')->where('type',2)->where('is_active', 1)->get()->toarray();


            return view('admin.goaltask.add',['goaltask_details' => $goaltask_details, 'admin_files_details' => $admin_files_details, 'frequency_arr' => $frequency_arr, 'task_files' => $task_files , 'affiliates' => $affiliates ]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.goaltask');
        }


        
    }

    #################################
    public function assign_mentee(Request $request){
        $victims = $request->victims;
        $id = $request->goaltask_id;
        $goaltask_details = DB::table('goaltask')->where('id',$id)->first();


        $assign_goals = DB::table('assign_goal')->select('victim_id')->where('goaltask_id',$id)->get();
        $assign_tasks = DB::table('assign_task')->select('victim_id')->where('goaltask_id',$id)->get();
        $assign_challenge = DB::table('assign_challenge')->select('victim_id')->where('goaltask_id',$id)->get();

        $assign_data = array();            

        if(!empty($goaltask_details) && ($goaltask_details->type == "task")){
            if(!empty($victims)) { 
                foreach($victims as $key => $value){ 
                    $data = DB::table('assign_task')->where('victim_id',$value)->where('goaltask_id',$id)->first();
                    if(empty($data)){
                        DB::table('assign_task')->insertGetId(['victim_id' => $value, 'goaltask_id' => $id]);

                        /*
                        $notification_id = DB::table(GOALTASK_NOTIFICATION)->insertGetId(['goaltask_id'=>$id,'type'=>'task','notification_for'=>'assign','user_type'=>'mentee','user_id'=>$value,'notification_response'=>'','created_at'=>date('Y-m-d H:i:s') ]);


                        $notification_response = goaltask_event_notification('mentee',$value,$id,'task_notification');

                        DB::table(GOALTASK_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
                        */
                        
                    }
                    $assigned_tasks[] = $value;
                }                     
            }
            if(!empty($assign_tasks)){
                foreach($assign_tasks as $vic_ac){
                    if (!in_array($vic_ac->victim_id, $assigned_tasks))
                    {
                        DB::table('assign_task')->where('victim_id',$vic_ac->victim_id)->where('goaltask_id',$id)->delete();
                    }
                }
            }
            $assign_data = DB::table('assign_task')->select('assign_task.*','mentee.firstname','mentee.middlename','mentee.lastname')->join('mentee', 'mentee.id', 'assign_task.victim_id')->where('assign_task.goaltask_id', $id)->get()->toarray();

        }else if(!empty($goaltask_details) && ($goaltask_details->type == "goal")){
            if(!empty($victims)) { 
                foreach($victims as $key => $value){ 
                    $data = DB::table('assign_goal')->where('victim_id',$value)->where('goaltask_id',$id)->first();
                    if(empty($data)){
                        DB::table('assign_goal')->insertGetId(['victim_id' =>$value, 'goaltask_id' => $id]); 

                        /*
                        $notification_id = DB::table(GOALTASK_NOTIFICATION)->insertGetId(['goaltask_id'=>$id,'type'=>'goal','notification_for'=>'assign','user_type'=>'mentee','user_id'=>$value,'notification_response'=>'','created_at'=>date('Y-m-d H:i:s') ]);

                        $notification_response = goaltask_event_notification('mentee',$value,$id,'goal_notification');

                        DB::table(GOALTASK_NOTIFICATION)->where('id',$notification_id)->update(['notification_response'=>$notification_response]);
                        */
                        
                    }
                    $assigned_goals[] = $value;
                }                     
            }
            if(!empty($assign_goals)){
                foreach($assign_goals as $vic_ac){
                    if (!in_array($vic_ac->victim_id, $assigned_goals))
                    {
                        DB::table('assign_goal')->where('victim_id',$vic_ac->victim_id)->where('goaltask_id',$id)->delete();
                    }
                }
            } 

            $assign_data = DB::table('assign_goal')->select('assign_goal.*','mentee.firstname','mentee.middlename','mentee.lastname')->join('mentee', 'mentee.id', 'assign_goal.victim_id')->where('assign_goal.goaltask_id', $id)->get()->toarray();   
        }else if(!empty($goaltask_details)){
            if(!empty($victims)) { 
                foreach($victims as $key => $value){ 
                    $data = DB::table('assign_challenge')->where('victim_id',$value)->where('goaltask_id',$id)->first();
                    if(empty($data)){
                        DB::table('assign_challenge')->insertGetId(['victim_id' => $value, 'goaltask_id' => $id]); 
                    }
                    $assigned_challenges[] = $value;
                }                     
            }
            if(!empty($assign_challenge)){
                foreach($assign_challenge as $vic_ac){
                    if (!in_array($vic_ac->victim_id, $assigned_challenges))
                    {
                        DB::table('assign_challenge')->where('victim_id',$vic_ac->victim_id)->where('goaltask_id',$id)->delete();
                    }
                }
            }   

            $assign_data = DB::table('assign_challenge')->select('assign_challenge.*','mentee.firstname','mentee.middlename','mentee.lastname')->join('mentee', 'mentee.id', 'assign_challenge.victim_id')->where('assign_challenge.goaltask_id', $id)->get()->toarray(); 
        }


        ?>
        <tbody>
        <?php 
        if(!empty($assign_data)){
            $count = 1;
            foreach($assign_data as $ad){ ?>
            <tr>
                <th scope="row">
                    <?php echo $count; ?>
                </th>
                <td>
                    <?php echo $ad->firstname.' '.$ad->middlename.' '.$ad->lastname; ?>
                </td>
            </tr>
            <?php $count++;
            }
        }

        ?>
        </tbody>
        <?php 



        // return json_encode(array('assign_data' => $assign_data));
    }
    #################################
    ########################## For VICTIM VIEW #############################

    public function viewtask($user_id)
    {
        try {
        $user_id = Crypt::decrypt($user_id);
        $type = "Task";
        $data_list = DB::table('goaltask')
                    ->leftjoin('assign_task', 'assign_task.goaltask_id', '=', 'goaltask.id')
                    ->leftjoin('mentee', 'mentee.id', '=', 'assign_task.victim_id')
                    ->select('goaltask.*','mentee.firstname','mentee.middlename','mentee.lastname','mentee.id as victims_id','assign_task.id as assign_id','assign_task.status as datastatus','assign_task.begin_time','assign_task.complated_time','assign_task.note','assign_task.point as points')
                    ->where('assign_task.victim_id',$user_id)
                    ->paginate(15);

        // echo '<pre>'; print_r($data_list); die;
        return view('admin.goaltask.viewtask',['data_list' => $data_list, 'type' => $type]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.mentee');
        }
    }

    public function viewgoal($user_id)
    {  
        try {
        $user_id = Crypt::decrypt($user_id);
        $type = "Goal";
        $data_list = DB::table('goaltask')
                    ->leftjoin('assign_goal', 'assign_goal.goaltask_id', '=', 'goaltask.id')
                    ->leftjoin('mentee', 'mentee.id', '=', 'assign_goal.victim_id')
                    ->select('goaltask.*','mentee.firstname','mentee.middlename','mentee.lastname','mentee.id as victims_id','assign_goal.id as assign_id','assign_goal.status as datastatus','assign_goal.begin_time','assign_goal.complated_time','assign_goal.note','assign_goal.point as points')
                    ->where('assign_goal.victim_id',$user_id)
                    ->paginate(15);
       // dd($data_list);
        return view('admin.goaltask.viewtask',['data_list' => $data_list, 'type' => $type]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.mentee');
        }
    }

    public function viewchallenge($user_id)
    {  
        try {
        $user_id = Crypt::decrypt($user_id);
        $type = "Challenge";
        $data_list = DB::table('goaltask')
                    ->leftjoin('assign_challenge', 'assign_challenge.goaltask_id', '=', 'goaltask.id')
                    ->leftjoin('mentee', 'mentee.id', '=', 'assign_challenge.victim_id')
                    ->select('goaltask.*','mentee.firstname','mentee.middlename','mentee.lastname','mentee.id as victims_id','assign_challenge.id as assign_id','assign_challenge.status as datastatus','assign_challenge.begin_time','assign_challenge.complated_time','assign_challenge.note','assign_challenge.point as points')
                    ->where('assign_challenge.victim_id',$user_id)
                    ->paginate(15);

        return view('admin.goaltask.viewtask',['data_list' => $data_list, 'type' => $type]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.mentee');
        }
    }

    ########################## For VICTIM VIEW #############################

    public function save(Request $request)
    {

        $id = Input::get('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect('admin/goaltask/add/'.$id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $type = Input::get('type');

        $name = Input::get('name');
        $description = Input::get('description');

        $dead_line = Input::get('dead_line');
        if(empty($dead_line)){
            $dead_line = '';
        }
        $reminder = Input::get('reminder');
        if(empty($reminder)){
            $reminder = 0;
        }
        $frequency = Input::get('frequency');
        if(empty($frequency)){
            $frequency = 0;
        }
        $start_date = Input::get('start_date');
        if(empty($start_date)){
            $start_date = '';
        }else{
            $start_date = DateTime::createFromFormat("m-d-Y" , $start_date);
            $start_date->format('Y-m-d');
        }
        $end_date = Input::get('end_date');
        if(empty($end_date)){
            $end_date = '';
        }else{
            $end_date = DateTime::createFromFormat("m-d-Y" , $end_date);
            $end_date->format('Y-m-d');
        }


        $point = Input::get('point');
        if(empty($point)){
            $point = 0;
        }


        $status = Input::get('status');
        if(empty($status)){
            $status = 0;
        }
        
        $created_by = Input::get('created_by');
        $timezone = Auth::user()->timezone;

        if(Auth::user()->type == 3){
            $created_by = Auth::user()->parent_id;
            $creater_data = DB::table('admins')->where('id',$created_by)->first();
            $timezone = $creater_data->timezone;
        }

        // echo $timezone; die;

        if($start_date>$end_date){
            session(['error_message' => 'Oops!Start date is greater than end date.']);
            return redirect()->back();
        }

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
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $created_by,'staff_id' => Auth::user()->id,'point' => $point , 'created_date'=> $created_date , 'updated_date' => $updated_date]); 
                session(['success_message' => 'Data added successfully']);
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'point' => $point , 'updated_date' => $updated_date]);
                session(['success_message' => 'Data updated successfully']);
            }
        }else if($type == "task"){
            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'dead_line' => $dead_line,'start_date' => $start_date,'end_date' => $end_date,'reminder' => $reminder,'frequency' => $frequency,'description' => $description,'created_by' => $created_by,'staff_id' => Auth::user()->id,'point' => $point , 'created_date'=> $created_date , 'updated_date' => $updated_date]); 
                session(['success_message' => 'Data added successfully']);
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'dead_line' => $dead_line,'start_date' => $start_date,'end_date' => $end_date,'reminder' => $reminder,'frequency' => $frequency,'description' => $description,'point' => $point , 'updated_date' => $updated_date]);
                session(['success_message' => 'Data updated successfully']);
            }

        }else{
            
            if(empty($id)){
                $id = DB::table('goaltask')->insertGetId(['type' => $type,'name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'created_by' => $created_by,'staff_id' => Auth::user()->id,'point' => $point , 'created_date'=> $created_date , 'updated_date' => $updated_date]); 
                session(['success_message' => 'Data added successfully']);
            }else{
                DB::table('goaltask')
                ->where('id', $id)
                ->update(['name' => $name,'status' => $status,'start_date' => $start_date,'end_date' => $end_date,'description' => $description,'point' => $point , 'updated_date' => $updated_date]);
                session(['success_message' => 'Data updated successfully']);
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

        
        return redirect()->action('Admin\GoaltaskController@index');
    }

    public function delete_task_files($id,$goaltask_id,$file_name)
    {   
        if(!empty($id) && !empty($goaltask_id) && !empty($file_name)){            
            DB::table('task_files')->where('id',$id)->where('goaltask_id',$goaltask_id)->delete();
            $file_path = public_path().'/uploads/goaltask/'.$file_name;
            File::delete($file_path);
            
            return redirect('/admin/goaltask/add/'.$goaltask_id)->with('msg' , 'File deleted successfully')->with('msg_color','success');
        }else{
            return redirect('/admin/goaltask/add/'.$goaltask_id)->with('msg' ,'Oops!Something went wrong')->with('msg_color','danger');
        }

    }

    public function change_status_ajax(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            $goaltask = DB::table('goaltask')->where('id',$id)->first();
            if($goaltask->status == 1){
                DB::table('goaltask')->where('id',$id)->update(['status'=>0]);
                $message = 'Inactive';
            }else{
                DB::table('goaltask')->where('id',$id)->update(['status'=>1]);
                $message = 'Active';
            }
            return Response::json(array(
                'success' => true,
                'message'   => $message
            )); 
        }
    }

    public function change_status($id,$uri)
    {
        if(!empty($id)){
            $goaltask = DB::table('goaltask')->where('id',$id)->first();
            if(!empty($goaltask->status)){
                DB::table('goaltask')->where('id',$id)->update(['status'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('goaltask')->where('id',$id)->update(['status'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'goaltask'){
                return redirect('/admin/goaltask');
            }else{
                return redirect('/admin/inactivegoaltask');
            }
        }
    }

    /*==================VIEW NOTE==================*/

    public function view_notes(Request $request)
    {
        $goaltask_id = $request->goaltask_id;
        $victim_id = $request->victim_id;
        $goaltask_details = DB::table('goaltask')->where('id',$goaltask_id)->first();
        $victim_note_arr = DB::table('goaltask_note')->where('goaltask_id',$goaltask_id)->where('victim_id',$victim_id)->get()->toarray();

        ?>
        <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>                        
                <th scope="col">Note</th>                        
                <th scope="col">Date</th>
                </tr>
            </thead>

            <?php   $i=1;
                    if(!empty($victim_note_arr)){
                foreach($victim_note_arr as $vc){
                ?>
            
                <tbody>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $vc->note?></td>
                        <td>
                            <?php if($vc->created_date == '' || $vc->created_date != '0000-00-00 00:00:00'){
                                $note_created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $vc->created_date);
                                echo $note_created_date->format('m-d-Y H:i:s');
                            }
                            ?>
                            
                        </td>
                    </tr>
                </tbody>

            <?php $i++;
            }}?>
                
        </table>
        <?php 
    }


    /*==================VIEW UPLOADED FILES==================*/

    public function view_uploaded_files(Request $request)
    {
        $goaltask_id = $request->goaltask_id;
        $victim_id = $request->victim_id;
        $goaltask_details = DB::table('goaltask')->where('id',$goaltask_id)->first();
        $victim_files_arr = DB::table('goaltaskuserfiles')->where('goaltask_id',$goaltask_id)->where('added_by',$victim_id)->get()->toarray();

        ?>
        <table class="table">
            <thead>
                <tr>
                <th scope="col">#</th>                        
                <th scope="col">Files</th>                        
                <th scope="col">Date</th>
                </tr>
            </thead>

            <?php   $i=1;
                    if(!empty($victim_files_arr)){
                foreach($victim_files_arr as $vf){
                ?>
            
                <tbody>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $vf->file_name?></td>
                        <td>
                            <?php if($vf->created_date == '' || $vf->created_date != '0000-00-00 00:00:00'){
                                $note_created_date = DateTime::createFromFormat("Y-m-d H:i:s" , $vf->created_date);
                                echo $note_created_date->format('m-d-Y H:i:s');
                            }
                            ?>
                            
                        </td>
                    </tr>
                </tbody>

            <?php $i++;
            }}?>
                
        </table>
        <?php 
    }

    public function taskpoint(Request $request)
    {
        $staff_id = $request->staff_id;
        $tgc_id = $request->tgc_id;
        $t_type = $request->t_type;
        $point = $request->point;
        $staff_point = DB::table('goaltask')->select('point')->where('id',$tgc_id)->first();
        
        if($point<=$staff_point->point){

            if($t_type=='Goal'){
             $staff_point = DB::table('assign_goal')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }elseif($t_type=='Task'){
                $staff_point = DB::table('assign_task')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }elseif($t_type=='Challenge'){
                $staff_point = DB::table('assign_challenge')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }
        }
        //$staff_point = DB::table('assign_task')->where('victim_id',$staff_id)->update(['point' => $point]);

        return redirect()->back();
    }

    public function getmaxpoint(Request $request)
    {
        $tgc_id = $request->tgc_id;
        $staff_id = $request->staff_id;
        $t_type = $request->t_type;
        $points = $request->points;

        $staff_point = DB::table('goaltask')->select('point')->where('id',$tgc_id)->first();
        
        if($points>$staff_point->point){
            $data['points']=0;
        }else{
            
            /*if($t_type=='Goal'){
             $s_point = DB::table('assign_goal')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }elseif($t_type=='Task'){
                $s_point = DB::table('assign_task')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }elseif($t_type=='Challenge'){
                $s_point = DB::table('assign_challenge')->where('goaltask_id',$tgc_id)->where('victim_id',$staff_id)->update(['point' => $point]);
            }*/
            $data['points']=$points;
        }

        return $data;
    }

}