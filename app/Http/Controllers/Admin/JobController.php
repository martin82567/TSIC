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
use File;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
class JobController extends Controller
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
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 2){
            $p_ids = array();
            $parent_ids = DB::table('admins')->where('parent_id',Auth::user()->id)->get();
            if(!empty($parent_ids)){
                foreach($parent_ids as $p){
                    $p_ids[] = $p->id;
                }
            }
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->where('created_by',Auth::user()->id)
                            ->orWhereIn('created_by',$p_ids)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 3){
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->where('created_by',Auth::user()->id)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }
        
        return view('admin.job.list',['job_arr' => $job_arr]);
    }

    public function inactive_jobs(Request $request)
    {
        $query = $request->search;
        if(Auth::user()->type == 1){
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',0)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 2){
            $p_ids = array();
            $parent_ids = DB::table('admins')->where('parent_id',Auth::user()->id)->get();
            if(!empty($parent_ids)){
                foreach($parent_ids as $p){
                    $p_ids[] = $p->id;
                }
            }
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',0)
                            ->where('created_by',Auth::user()->id)
                            ->orWhereIn('created_by',$p_ids)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }else if(Auth::user()->type == 3){
            $job_arr = DB::table('job')
                            ->where(function ($q) use ($query) {
                                $q->where('job_title', 'like', '%'.$query.'%')
                                      ->orWhere('summary', 'like', '%'.$query.'%');
                            })
                            ->where('status',0)
                            ->where('created_by',Auth::user()->id)
                            ->orderBy('status', 'desc')
                            ->paginate(15);
        }
        
        return view('admin.job.list',['job_arr' => $job_arr]);
    }

    public function add($id)
    {  
        // try {
        $id = Crypt::decrypt($id);
        $user_details = DB::table('job')->where('id',$id)->first();
        
        // $job_type = DB::table('job_type')->where('status',1)->get();

        
        return view('admin.job.add',['user_details' => $user_details ]);
        // }
        // catch (\Exception $e) {
        //     return redirect()->route('admin.job');
        // }
    }

    public function save(Request $request)
    {
        //dd($request->all());
        $id = Input::get('id');
        if(!empty($id)){
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',                             
                'application_url' => 'required',
                
            ]);
            if ($validator->fails()) {
                return redirect('admin/job/add/'.Crypt::encrypt($id))
                            ->withErrors($validator)
                            ->withInput();
            }
        }else{
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',            
                'application_url' => 'required'
                
            ]);
            if ($validator->fails()) {
                return redirect('admin/job/add/'.Crypt::encrypt('0'))
                            ->withErrors($validator)
                            ->withInput();
            }
        }
    

        $job_title=Input::get('job_title'); 
        $application_url=Input::get('application_url');
        $created_by = Auth::user()->id;

        $summary = Input::get('summary');
        if(empty($summary)){
            $summary = '';
        }
        
        $status = Input::get('status');
        if(empty($status)){
            $status = '';
        }       

   
        if(empty($id)){            
            
            $id = DB::table('job')->insertGetId(['job_title' => $job_title,  'summary' => $summary , 'created_by' => $created_by, 'status' => $status, 'application_url' => $application_url ]); 
            session(['success_message' => 'Data added successfully']);
        }else{
            DB::table('job')
            ->where('id', $id)
            ->update(['job_title' => $job_title, 'summary' => $summary , 'status' => $status, 'application_url' => $application_url ]);
            session(['success_message' => 'Data updated successfully']); 
        }        

        return redirect()->route('admin.job');
        //return redirect()->action('Admin\jobController@index');

    }

    public function remove_job_upload_desc($id,$file)
    {
        if(!empty($id) && !empty($file)){
            DB::table('job')->where('id',$id)->update(['upload_desc'=>'']);
            $file_path = public_path().'/uploads/jobdescription/'.$file;
            File::delete($file_path);
            
            $id = Crypt::encrypt($id); 
            
            return redirect('/admin/job/add/'.$id);
        }else{
            
            return redirect('/admin/job/add/'.$id);
        }
    }

    public function change_status_ajax(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            $job = DB::table('job')->where('id',$id)->first();
            if($job->status == 1){
                DB::table('job')->where('id',$id)->update(['status'=>0]);
                $message = 'Inactive';
            }else{
                DB::table('job')->where('id',$id)->update(['status'=>1]);
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
            $job = DB::table('job')->where('id',$id)->first();
            if(!empty($job->status)){
                DB::table('job')->where('id',$id)->update(['status'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('job')->where('id',$id)->update(['status'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'job'){
                return redirect('/admin/job');
            }else{
                return redirect('/admin/inactivejobs');
            }
        }
    }


}