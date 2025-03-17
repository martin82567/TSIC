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
class SchoolController extends Controller
{
	
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
    	$query = $request->search;
        if(Auth::user()->type == 1){
            $school_arr = DB::table('school')
                            ->select('school.*','admins.name AS affiliate_name')
                            ->leftJoin('admins', 'admins.id','school.agency_id')
                            ->where(function ($q) use ($query) {
                                $q->where('school.name', 'like', '%'.$query.'%')
                                ->orWhere('admins.name', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->orderBy('name', 'asc')
                            ->paginate(10);
        }else if(Auth::user()->type == 2){
            $school_arr = DB::table('school')
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->where('agency_id', Auth::user()->id)
                            ->orderBy('name', 'asc')
                            ->paginate(10);
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $school_arr = DB::table('school')
                            ->select('school.*','admins.name AS affiliate_name')
                            ->leftJoin('admins', 'admins.id','school.agency_id')
                            ->where(function ($q) use ($query) {
                                $q->where('school.name', 'like', '%'.$query.'%')
                                ->orWhere('admins.name', 'like', '%'.$query.'%');
                            })
                            ->where('status',1)
                            ->orderBy('name', 'asc')
                            ->paginate(10);
        }

    	

        return view('admin.school.list',['school_arr' => $school_arr,'type'=>'active','search'=>$query]);

    }

    public function inactive(Request $request)
    {
    	$query = $request->search;
    	if(Auth::user()->type == 1){
            $school_arr = DB::table('school')
                            ->select('school.*','admins.name AS affiliate_name')
                            ->leftJoin('admins', 'admins.id','school.agency_id')
                            ->where(function ($q) use ($query) {
                                $q->where('school.name', 'like', '%'.$query.'%')
                                ->orWhere('admins.name', 'like', '%'.$query.'%');
                            })
                            ->where('school.status',0)
                            ->orderBy('school.name', 'asc')
                            ->paginate(10);
        }else if(Auth::user()->type == 2){
            $school_arr = DB::table('school')->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%'.$query.'%');
                            })
                            ->where('status',0)
                            ->where('agency_id', Auth::user()->id)
                            ->orderBy('name', 'asc')
                            ->paginate(10);
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $school_arr = DB::table('school')
                            ->select('school.*','admins.name AS affiliate_name')
                            ->leftJoin('admins', 'admins.id','school.agency_id')
                            ->where(function ($q) use ($query) {
                                $q->where('school.name', 'like', '%'.$query.'%')
                                ->orWhere('admins.name', 'like', '%'.$query.'%');
                            })
                            ->where('school.status',0)
                            ->orderBy('school.name', 'asc')
                            ->paginate(10);
        }

        return view('admin.school.list',['school_arr' => $school_arr,'type'=>'inactive','search'=>$query]);

    }


    public function add($id)
    {
    	try {
    		$school_arr = $agency_arr =  array();
	        $id = Crypt::decrypt($id);

            $agency_arr = DB::table('admins')->where('type', 2)->where('is_active', 1)->get()->toarray();
	        $school_arr = DB::table('school')->where('id',$id)->first();

	        return view('admin.school.add',['school_arr' => $school_arr , 'agency_arr' => $agency_arr]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.school.index');
        }
    }

    public function save(Request $request)
    {
    	$id = Input::get('id');
        if(!empty($id)){
            $validator = Validator::make($request->all(), [
                'name' => 'required'             
            ]);
            if ($validator->fails()) {
                return redirect('admin/school/add/'.$id)
                            ->withErrors($validator)
                            ->withInput();
            }
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required'             
            ]);
            if ($validator->fails()) {
                return redirect('admin/school/add/0')
                            ->withErrors($validator)
                            ->withInput();
            }
        }

        $name=Input::get('name');
        $address=Input::get('address');
        $latitude=Input::get('latitude');
        $longitude=Input::get('longitude');
        $city=Input::get('city');
        $state=Input::get('state');
        $zip=Input::get('zip');
        $status=Input::get('status');

        
        $agency_id=Input::get('agency_id');
        
        

        if(empty($address)){
        	$address = '';
        }
        if(empty($latitude)){
        	$latitude = '';
        } 
        if(empty($longitude)){
        	$longitude = '';
        } 
        if(empty($city)){
        	$city = '';
        } 
        if(empty($state)){
        	$state = '';
        } 
        if(empty($zip)){
        	$zip = '';
        } 


        if(empty($id)){
        	$id = DB::table('school')->insertGetId(['agency_id' => $agency_id , 'name' => $name,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude,'city' => $city,'state' => $state,'zip' => $zip,'status'=>$status]);
        	session(['success_message' => 'Data added successfully']);
        }else{
        	DB::table('school')->where('id',$id)->update(['name' => $name,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude,'city' => $city,'state' => $state,'zip' => $zip,'status'=>$status,'updated_at'=>date('Y-m-d H:i:s')]);
        	session(['success_message' => 'Data updated successfully']);
        }

        return redirect('/admin/active-school');


    }

    public function change_status($id,$uri)
    {
        if(!empty($id)){
            $school = DB::table('school')->where('id',$id)->first();
            if(!empty($school->status)){
                DB::table('school')->where('id',$id)->update(['status'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('school')->where('id',$id)->update(['status'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'active-school'){
                return redirect('/admin/active-school');
            }else{
                return redirect('/admin/inactive-school');
            }
        }
    }
    
}