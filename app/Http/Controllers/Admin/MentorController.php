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
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
class MentorController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    public function index(Request $request)
    {
        $query = $request->search;
        $sort_needed = 0;
        $is_affiliate_view = 0;
        if(Auth::user()->type == 1){
            $is_affiliate_view = 1;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $is_affiliate_view = 1;
        }
        
        
        $sort = $request->sort;
        $column = $request->column;
            
        $orderByRaw = "";

        if(!empty($sort) && !empty($column)){
            $sort_needed = 1;

            if($sort == 'asc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name ASC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentor.firstname ASC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentor.lastname ASC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentor.email ASC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentor.last_activity_at ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name DESC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentor.firstname DESC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentor.lastname DESC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentor.email DESC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentor.last_activity_at DESC ";
                }
            }
            
        }else if(!empty($sort) && empty($column)){
            $orderByRaw = "mentor.id DESC ";
        }

        
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $mentor_arr = DB::table('mentor') 
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title')   
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',1)
                                ->orderBy('mentor.id', 'desc')                                
                                ->paginate(15);
            }else{

                // echo '<pre>';echo $sort; 
                // echo '<pre>';echo $column; 

                // die;

                $mentor_arr = DB::table('mentor') 
                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',1)
                                ->orderByRaw($orderByRaw)                                
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $mentor_arr = DB::table('mentor')
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',1)
                                ->where('mentor.assigned_by',Auth::user()->id)
                                ->orderBy('mentor.id', 'desc')     
                                ->paginate(15);
            }else{
                $mentor_arr = DB::table('mentor')
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active') 
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',1)
                                ->where('mentor.assigned_by',Auth::user()->id)
                                ->orderByRaw($orderByRaw)
                                ->paginate(15);
                
            }
        }else if(Auth::user()->type == 3){
            $parent_id = Auth::user()->parent_id;

            $age = 0;

            $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
            if($admin_parent->type == 1){
                $age = 1;
            }else{
                $age = 0;
            }

            if(!empty($age)){
                if(empty($sort)){
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',1)
                                    ->orderBy('mentor.id', 'desc')
                                    ->paginate(15);
                }else{
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',1)
                                    ->orderByRaw($orderByRaw)
                                    ->paginate(15);
                    
                }

            }else{
                if(empty($sort)){
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',1)
                                    ->where('mentor.assigned_by',$parent_id)
                                    ->orderBy('mentor.id', 'desc')
                                    ->paginate(15);
                }else{
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',1)
                                    ->where('mentor.assigned_by',$parent_id)
                                    ->orderByRaw($orderByRaw)
                                    ->paginate(15);
                    
                }
            }

            
        }

        if(empty($sort)){
            $sort = 'asc';
        }

        $mentor_arr->appends(array('search' => $query))->links();
        $mentor_arr->appends(array('sort' => $sort))->links();
        $mentor_arr->appends(array('column' => $column))->links();

        $type = 'active';

        // echo '<pre>'; print_r($mentor_arr); die;
        
        return view('admin.mentor.list',['mentor_arr' => $mentor_arr, 'sort' => $sort, 'column' => $column ,'search' => $query,'sort_needed' => $sort_needed, 'type' => $type , 'is_affiliate_view' => $is_affiliate_view]);
    }

    public function inactive_mentor(Request $request)
    {
        $query = $request->search;
        $sort_needed = 0;
        $is_affiliate_view = 0;
        if(Auth::user()->type == 1){
            $is_affiliate_view = 1;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $is_affiliate_view = 1;
        }
        
        
        $sort = $request->sort;
        $column = $request->column;
            
        $orderByRaw = "";

        if(!empty($sort) && !empty($column)){
            $sort_needed = 1;

            if($sort == 'asc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name ASC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentor.firstname ASC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentor.lastname ASC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentor.email ASC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentor.last_activity_at ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name DESC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentor.firstname DESC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentor.lastname DESC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentor.email DESC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentor.last_activity_at DESC ";
                }
            }
            
        }else if(!empty($sort) && empty($column)){
            $orderByRaw = "mentor.id DESC ";
        }

        
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $mentor_arr = DB::table('mentor') 
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title')   
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',0)
                                ->orderBy('mentor.id', 'desc')                                
                                ->paginate(15);
            }else{

                // echo '<pre>';echo $sort; 
                // echo '<pre>';echo $column; 

                // die;

                $mentor_arr = DB::table('mentor') 
                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',0)
                                ->orderByRaw($orderByRaw)                                
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $mentor_arr = DB::table('mentor')
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',0)
                                ->where('mentor.assigned_by',Auth::user()->id)
                                ->orderBy('mentor.id', 'desc')     
                                ->paginate(15);
            }else{
                $mentor_arr = DB::table('mentor')
                                ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                ->join('admins', 'admins.id', 'mentor.assigned_by')
                                ->join('mentor_status', 'mentor_status.id', 'mentor.is_active') 
                                ->where(function ($q) use ($query) {
                                    $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentor.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%');
                                })
                                ->where('mentor_status.view_in_application',1)
                                ->where('mentor.platform_status',0)
                                ->where('mentor.assigned_by',Auth::user()->id)
                                ->orderByRaw($orderByRaw)
                                ->paginate(15);
                
            }
        }else if(Auth::user()->type == 3){
            $parent_id = Auth::user()->parent_id;

            $age = 0;

            $admin_parent = DB::table('admins')->where('id',$parent_id)->first();
            if($admin_parent->type == 1){
                $age = 1;
            }else{
                $age = 0;
            }

            if(!empty($age)){
                if(empty($sort)){
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',0)
                                    ->orderBy('mentor.id', 'desc')
                                    ->paginate(15);
                }else{
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',0)
                                    ->orderByRaw($orderByRaw)
                                    ->paginate(15);
                    
                }

            }else{
                if(empty($sort)){
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',0)
                                    ->where('mentor.assigned_by',$parent_id)
                                    ->orderBy('mentor.id', 'desc')
                                    ->paginate(15);
                }else{
                    $mentor_arr = DB::table('mentor')
                                    ->select('mentor.*','admins.name AS admin_name','mentor_status.title AS status_title') 
                                    ->join('admins', 'admins.id', 'mentor.assigned_by') 
                                    ->join('mentor_status', 'mentor_status.id', 'mentor.is_active')
                                    ->where(function ($q) use ($query) {
                                        $q->where('mentor.firstname', 'like', '%'.$query.'%')
                                        ->orWhere('mentor.lastname','like', '%'.$query.'%')
                                        ->orWhere('mentor.email','like', '%'.$query.'%')
                                        ->orWhere('admins.name','like', '%'.$query.'%');
                                    })
                                    ->where('mentor_status.view_in_application',1)
                                    ->where('mentor.platform_status',0)
                                    ->where('mentor.assigned_by',$parent_id)
                                    ->orderByRaw($orderByRaw)
                                    ->paginate(15);
                    
                }
            }

            
        }

        if(empty($sort)){
            $sort = 'asc';
        }

        $mentor_arr->appends(array('search' => $query))->links();
        $mentor_arr->appends(array('sort' => $sort))->links();
        $mentor_arr->appends(array('column' => $column))->links();

        $type = 'active';

        // echo '<pre>'; print_r($mentor_arr); die;
        
        return view('admin.mentor.list',['mentor_arr' => $mentor_arr, 'sort' => $sort, 'column' => $column ,'search' => $query,'sort_needed' => $sort_needed, 'type' => $type , 'is_affiliate_view' => $is_affiliate_view]);
    }

    public function add($id)
    {  
        try{
            $id = Crypt::decrypt($id);
            
            $mentor_details = DB::table('mentor')->where('id',$id)->first();

            $goal = $task = $challenge = $agencies =  array();

            $agencies = DB::table('admins')->where('type', 2)->where('is_active', 1)->get()->toarray();

            if(!empty($id)){

            }


            return view('admin.mentor.add',['mentor' => $mentor_details, 'agencies'=>$agencies,  'goal' => $goal, 'task' => $task, 'challenge' => $challenge]);

        }catch(\Exception $e){
            return redirect()->route('admin.mentor');
        }
    }

    public function save(Request $request)
    {        
        $id = Input::get('id');
        $enid = Crypt::encrypt($id);
        

        $password = Input::get('password');
        $is_chat_mentee = Input::get('is_chat_mentee');
        $is_chat_staff = Input::get('is_chat_staff');
        $is_chat_video = Input::get('is_chat_video');
        $platform_status = Input::get('platform_status');

        if(empty($is_chat_mentee)){
            $is_chat_mentee = 0;
        }

        if(empty($is_chat_staff)){
            $is_chat_staff = 0;
        }

        if(empty($is_chat_video)){
            $is_chat_video = 0;
        }

        if(empty($platform_status)){
            $platform_status = 0;
        }
                
        
        $assigned_by = Input::get('assigned_by'); 

        if(!empty($assigned_by)){
            $admin_data = DB::table('admins')->where('id',$assigned_by)->first();
            $timezone = $admin_data->timezone;
        }else{
            $timezone = '';
        }

        

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }     

        if(!empty($id)){ 
            if(!empty($password)){
                $password = bcrypt($password);
                DB::table('mentor')
                ->where('id', $id)
                ->update([ 'password' => $password, 'is_chat_mentee'=>$is_chat_mentee , 'is_chat_staff' =>$is_chat_staff , 'is_chat_video' => $is_chat_video , 'platform_status' => $platform_status , 'updated_at' => $created_date ]);
            }else{
                
                DB::table('mentor')
                ->where('id', $id)
                ->update([  'is_chat_mentee'=>$is_chat_mentee , 'is_chat_staff' =>$is_chat_staff , 'is_chat_video' => $is_chat_video , 'platform_status' => $platform_status , 'updated_at' => $created_date ]);
            }

            
            

            session(['success_message' => 'Data updated successfully']);            
            
        }
                
        return redirect()->action('Admin\MentorController@index');
    }

    public function save_bkp(Request $request)
    {        
        $id = Input::get('id');
        $enid = Crypt::encrypt($id);
        if(empty($id)){
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'email' => 'required|email|unique:mentor,email,'.Input::get('id'),
                'password' => 'required'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                // 'firstname' => 'required',
                // 'lastname' => 'required',
                'email' => 'required|email|unique:mentor,email,'.Input::get('id')
            ]);
        }
        
        if ($validator->fails()) {
            return redirect('admin/mentor/add/'.$enid)
                        ->withErrors($validator)
                        ->withInput();
        }

        $firstname = Input::get('firstname');
        $middlename = Input::get('middlename');
        if(empty($middlename)){
            $middlename = '';
        }
        $lastname = Input::get('lastname');
        $email = Input::get('email');
        $phone = Input::get('phone');
        $password = Input::get('password');
        $is_chat_mentee = Input::get('is_chat_mentee');
        $is_chat_staff = Input::get('is_chat_staff');

        if(empty($is_chat_mentee)){
            $is_chat_mentee = 0;
        }

        if(empty($is_chat_staff)){
            $is_chat_staff = 0;
        }

        if(empty($phone)){
            $phone = '';
        }
        
        

        $image = Input::file('image');
        if(empty($image)){
            $image = '';
        }
        
                
        $is_active = Input::get('is_active');
        if(empty($is_active)){
            $is_active = 0;
        }

        // echo $is_active; die;        
        
        if(Auth::user()->type == 3){
           $created_by = Auth::user()->parent_id;
        }else{
            $created_by = Auth::user()->id;
        }

        $assigned_by = Input::get('assigned_by'); 

        

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }     

        if(empty($id)){            	

            $password_original = $password;
            $password = bcrypt($password);

            if(!empty($image)){
                $img = $image;
                $ext = $img->getClientOriginalExtension();
                $ext = strtolower($ext);
                if($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'mp4' && $ext != '3gp' && $ext != 'docx' && $ext != 'doc' && $ext != 'pdf'){
                    session(['error_message' => 'Please upload correct file type.']);
                    // return redirect('admin/mentee/notelist/'.$enid);
                }
                $storage_path = public_path() . '/uploads/mentor_pic/';
                $image_name = time().uniqid(rand());
                $image_val = $image_name.'.'.$img->getClientOriginalExtension();
                $img->move($storage_path,$image_val);
            }else{
                $image_val = "";
            }
            



            $id = DB::table('mentor')->insertGetId([ 'firstname' => $firstname,'middlename' => $middlename,'lastname' => $lastname,'email' => $email,'phone'=>$phone,'password' => $password, 'is_chat_mentee'=>$is_chat_mentee , 'is_chat_staff' =>$is_chat_staff , 'image'=>$image_val, 'created_by' => Auth::user()->id , 'assigned_by' => $assigned_by , 'is_active' => $is_active , 'created_at' => $created_date ]); 

            
                    
            

            session(['success_message' => 'Data added successfully']);

            
            
        }else{

            if(!empty($image)){
                $img = $image;
                $ext = $img->getClientOriginalExtension();
                $ext = strtolower($ext);
                if($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'mp4' && $ext != '3gp' && $ext != 'docx' && $ext != 'doc' && $ext != 'pdf'){
                    session(['error_message' => 'Please upload correct file type.']);
                    // return redirect('admin/mentee/notelist/'.$enid);
                }
                $storage_path = public_path() . '/uploads/mentor_pic/';
                $image_name = time().uniqid(rand());
                $image_val = $image_name.'.'.$img->getClientOriginalExtension();
                $img->move($storage_path,$image_val);
            }else{
                $mentor_data = DB::table('mentor')->where('id',$id)->first();

                $image_val = $mentor_data->image;
            }

            if(!empty($password)){
                $password = bcrypt($password);
                DB::table('mentor')
                ->where('id', $id)
                ->update([ 'email' => $email,'password' => $password, 'is_chat_mentee'=>$is_chat_mentee , 'is_chat_staff' =>$is_chat_staff , 'is_active' => $is_active , 'assigned_by' => $assigned_by , 'updated_at' => $created_date ]);
            }else{
                
                DB::table('mentor')
                ->where('id', $id)
                ->update([ 'email' => $email, 'is_chat_mentee'=>$is_chat_mentee , 'is_chat_staff' =>$is_chat_staff , 'is_active' => $is_active , 'assigned_by' => $assigned_by , 'updated_at' => $created_date ]);
            }

            
            

            session(['success_message' => 'Data updated successfully']);
        }
                
        return redirect()->action('Admin\MentorController@index');
    }

    public function view_session($id,Request $request)
    {
        try{
            $id = Crypt::decrypt($id);
            $session = DB::table('session')->select('session.*','mentee.firstname','mentee.middlename','mentee.lastname')->join('mentee', 'mentee.id','session.mentee_id')->where('session.mentor_id',$id)->orderBy('session.schedule_date', 'desc')->paginate(15);
            return view('admin.mentor.view-session', ['session'=>$session]);
        }catch(\Exception $e){
            return redirect('/mentor');
        }
        
    }

    public function change_status($id,$uri)
    {
        if(!empty($id)){
            $victim = DB::table('mentor')->where('id',$id)->first();
            if(!empty($victim->is_active)){
                DB::table('mentor')->where('id',$id)->update(['is_active'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('mentor')->where('id',$id)->update(['is_active'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'mentor'){
                return redirect('/admin/mentor');
            }else{
                return redirect('/admin/inactivementor');
            }
            
        }
    }

       

   

}