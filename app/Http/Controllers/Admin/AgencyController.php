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


class AgencyController extends Controller
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
        //  ==== Active Agencies

        $type="agency";
        $query = $request->search;

        $sort_needed = 0;
        if(!empty($request->sort)){
            $sort = $request->sort;
            $sort_needed = 1;
        }
        if(empty($sort)){
            $user_arr = DB::table('admins')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                        ->orWhere('email', 'like', '%'.$query.'%')
                                        ->orWhere('address', 'like', '%'.$query.'%');
                                })
                                ->where('type','=', 2)
                                ->where('is_active','=', 1)
                                ->orderBy('id', 'desc')
                                ->paginate(15);
        }else{
            $user_arr = DB::table('admins')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                        ->orWhere('email', 'like', '%'.$query.'%')
                                        ->orWhere('address', 'like', '%'.$query.'%');
                                })
                                ->where('type','=', 2)
                                ->where('is_active','=', 1)
                                ->orderBy('name', $sort)
                                ->paginate(15);
        }
        if(empty($sort)){
            $sort = 'asc';
        }

        $user_arr->appends(array('search' => $query))->links();
        $user_arr->appends(array('sort' => $sort))->links();
        $user_arr->appends(array('type' => $type))->links();

        return view('admin.agency.list',['user_arr' => $user_arr, 'type'=>$type,'sort' => $sort,'search' => $query,'sort_needed' => $sort_needed]);
    }

    public function inactive_agencies(Request $request)
    {
        //  ==== Inactive Agencies

        $type="agency";
        $query = $request->search;

        $sort_needed = 0;
        if(!empty($request->sort)){
            $sort = $request->sort;
            $sort_needed = 1;
        }
        if(empty($sort)){
            $user_arr = DB::table('admins')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                        ->orWhere('email', 'like', '%'.$query.'%')
                                        ->orWhere('address', 'like', '%'.$query.'%');
                                })
                                ->where('type','=', 2)
                                ->where('is_active','=', 0)
                                ->orderBy('id', 'desc')
                                ->paginate(15);
        }else{
            $user_arr = DB::table('admins')
                                ->where(function ($q) use ($query) {
                                    $q->where('name', 'like', '%'.$query.'%')
                                        ->orWhere('email', 'like', '%'.$query.'%')
                                        ->orWhere('address', 'like', '%'.$query.'%');
                                })
                                ->where('type','=', 2)
                                ->where('is_active','=', 0)
                                ->orderBy('name', $sort)
                                ->paginate(15);
        }
        if(empty($sort)){
            $sort = 'asc';
        }

        $user_arr->appends(array('search' => $query))->links();
        $user_arr->appends(array('sort' => $sort))->links();
        $user_arr->appends(array('type' => $type))->links();

        return view('admin.agency.list',['user_arr' => $user_arr, 'type'=>$type,'sort' => $sort,'search' => $query,'sort_needed' => $sort_needed]);

    }

    public function user(Request $request)
    {
        /* Active Staff */
        $type="user";

        $query = $request->search;

        $sort_needed = 0;
        $user_arr = '';
        $sort1 = '';
        $sort2 = '';
        $column_name1 = '';
        $column_name2 = '';
        if(!empty($request->sort1) || !empty($request->sort2)){
            $sort1 = $request->sort1;
            $sort2 = $request->sort2;
            $sort_needed = 1;
        }

        //if(!empty($column_name1) || !empty($column_name2)){
            $column_name1 = $request->column_name1;
            $column_name2 = $request->column_name2;
        //}


        if(Auth::user()->type == 2){
            if(!empty($sort1) || !empty($sort2)){
                if(!empty($sort1) && !empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.name', $sort1)
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);
                }else if(!empty($sort1)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.name', $sort1)
                                ->select('main_admin.*')
                                ->paginate(15);

                }else if(!empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','1')
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);

                }
            }else{
                $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.id', 'desc')
                                ->select('main_admin.*')
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 1){

            if(!empty($sort1) || !empty($sort2)){
                if(!empty($sort1) && !empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.name', $sort1)
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);
                }else if(!empty($sort1)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.name', $sort1)
                                ->select('main_admin.*')
                                ->paginate(15);

                }else if(!empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','1')
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);

                }
            }else{
                $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','1')
                                ->orderBy('main_admin.id', 'desc')
                                ->select('main_admin.*')
                                ->paginate(15);
            }

            // if(empty($sort1)){
            //     $sort1 = 'asc';
            // }
            // if(empty($sort2)){
            //     $sort2 = 'asc';
            // }

            $user_arr->appends(array('search' => $query))->links();
            $user_arr->appends(array('sort1' => $sort1))->links();
            $user_arr->appends(array('sort2' => $sort2))->links();
            $user_arr->appends(array('column_name1' => $column_name1))->links();
            $user_arr->appends(array('column_name2' => $column_name2))->links();
            $user_arr->appends(array('type' => $type))->links();
            // echo '<pre>'; print_r($user_arr); die;
        }

        return view('admin.agency.list',['user_arr' => $user_arr, 'type'=>$type,'sort1' => $sort1,'sort2' => $sort2,'column_name1' => $column_name1,'column_name2' => $column_name2,'search' => $query,'sort_needed' => $sort_needed]);
    }

    public function inactive_user(Request $request)
    {
        $type="user";

        $query = $request->search;

        $sort_needed = 0;
        $sort1 = '';
        $sort2 = '';
        $column_name1 = '';
        $column_name2 = '';
        if(!empty($request->sort1) || !empty($request->sort2)){
            $sort1 = $request->sort1;
            $sort2 = $request->sort2;
            $sort_needed = 1;
        }

        //if(!empty($column_name1) || !empty($column_name2)){
            $column_name1 = $request->column_name1;
            $column_name2 = $request->column_name2;
        //}


        if(Auth::user()->type == 2){
            if(!empty($sort1) || !empty($sort2)){
                if(!empty($sort1) && !empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.name', $sort1)
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);
                }else if(!empty($sort1)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.name', $sort1)
                                ->select('main_admin.*')
                                ->paginate(15);

                }else if(!empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','0')
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);

                }
            }else{
                $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.parent_id','=',Auth::user()->id)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.id', 'desc')
                                ->select('main_admin.*')
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 1){

            if(!empty($sort1) || !empty($sort2)){
                if(!empty($sort1) && !empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.name', $sort1)
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);
                }else if(!empty($sort1)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.name', $sort1)
                                ->select('main_admin.*')
                                ->paginate(15);

                }else if(!empty($sort2)){
                    $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','0')
                                ->orderBy('sub_admin.name', $sort2)
                                ->select('main_admin.*')
                                ->paginate(15);

                }
            }else{
                $user_arr = DB::table('admins as main_admin')
                                ->join('admins as sub_admin', 'main_admin.parent_id', '=', 'sub_admin.id')
                                ->where(function ($q) use ($query) {
                                    $q->where('main_admin.name', 'like', '%'.$query.'%')
                                        ->orWhere('main_admin.email', 'like', '%'.$query.'%')
                                        ->orWhere('sub_admin.name', 'like', '%'.$query.'%');
                                })
                                ->where('main_admin.type','=', 3)
                                ->where('main_admin.is_active','0')
                                ->orderBy('main_admin.id', 'desc')
                                ->select('main_admin.*')
                                ->paginate(15);
            }

            // if(empty($sort1)){
            //     $sort1 = 'asc';
            // }
            // if(empty($sort2)){
            //     $sort2 = 'asc';
            // }

            $user_arr->appends(array('search' => $query))->links();
            $user_arr->appends(array('sort1' => $sort1))->links();
            $user_arr->appends(array('sort2' => $sort2))->links();
            $user_arr->appends(array('column_name1' => $column_name1))->links();
            $user_arr->appends(array('column_name2' => $column_name2))->links();
            $user_arr->appends(array('type' => $type))->links();
            // echo '<pre>'; print_r($user_arr); die;
        }

        return view('admin.agency.list',['user_arr' => $user_arr, 'type'=>$type,'sort1' => $sort1,'sort2' => $sort2,'column_name1' => $column_name1,'column_name2' => $column_name2,'search' => $query,'sort_needed' => $sort_needed]);
    }

    public function add($id,Request $request)
    {
        try {
            $id = Crypt::decrypt($id);

            $menu_type = Input::get('menu_type');

            if($menu_type == 'agency'){
                $user_details = DB::table('admins')->select('*')->where('id',$id)->first();

            }else{
                $user_details = DB::table('admins')->select('admins.*','user_access.access_mentor','user_access.access_victim','user_access.acces_resource','user_access.access_job','user_access.access_goal_task_challenge','user_access.access_e_learning','user_access.access_agency','user_access.access_meeting')->join('user_access', 'admins.id', '=', 'user_access.user_id')->where('admins.id',$id)->first();
            }

            // echo '<pre>'; print_r($user_details); die;

            $contact_type_details = DB::table('contact_type')->where('is_active',1)->get();
            $other_service_details = DB::table('other_service')->where('is_active',1)->get();
            $document_type_details = DB::table('document_type')->where('is_active',1)->get();
            $states = DB::table('states')->get()->toarray();
            $timezones = DB::table('timezones')->where('status',1)->get()->toarray();

            $admin_files_details = DB::table('admin_files')
                        ->join('document_type', 'document_type.id', '=', 'admin_files.document_type')
                        ->select('admin_files.*', 'document_type.name as document_type_name')
                        ->where('admin_id',$id)
                        ->get();


            // echo '<pre>'; print_r($timezones); die;
            $agency_arr = DB::table('admins')->where('type', '!=', '3')->where('is_active', 1)->get()->toarray();

            return view('admin.agency.add',['user_details' => $user_details, 'admin_files_details' => $admin_files_details, 'contact_type_details' => $contact_type_details, 'other_service_details' => $other_service_details, 'document_type_details' => $document_type_details, 'states' => $states, 'menu_type'=>$menu_type, 'timezones'=>$timezones, 'agency_arr' => $agency_arr]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.user');
        }
    }

    public function save(Request $request)
    {
        $id = Input::get('id');
        $type = Input::get('type');

        /* Validation */

        if(!empty($id)){
            if($type == 'agency'){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:admins,email,'.Input::get('id'),
                    // 'state' => 'required',
                    // 'address' => 'required',
                    // 'latitude' => 'required',
                    // 'longitude' => 'required'
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:admins,email,'.Input::get('id')
                ]);
            }

            if ($validator->fails()) {
                if($type == 'agency'){
                    return redirect('admin/agency/add/'.Crypt::encrypt($id).'?menu_type=agency')
                            ->withErrors($validator)
                            ->withInput();
                }else{
                    return redirect('admin/agency/add/'.Crypt::encrypt($id).'?menu_type=user')
                            ->withErrors($validator)
                            ->withInput();
                }
            }
        }else{
            if($type == 'agency'){
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:admins,email,'.Input::get('id'),
                    'password' => 'required',
                    // 'state' => 'required',
                    // 'address' => 'required',
                    // 'latitude' => 'required',
                    // 'longitude' => 'required'
                ]);
            }else{
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:admins,email,'.Input::get('id'),
                    'password' => 'required'
                ]);
            }

            if ($validator->fails()) {
                if($type == 'agency'){
                    return redirect('admin/agency/add/'.Crypt::encrypt(0).'?menu_type=agency')
                            ->withErrors($validator)
                            ->withInput();
                }else{
                    return redirect('admin/agency/add/'.Crypt::encrypt(0).'?menu_type=user')
                    ->withErrors($validator)
                    ->withInput();
                }

            }
        }

        /* Validation */

        $name = Input::get('name');
        $email = Input::get('email');
        $password = Input::get('password');
        $state = Input::get('state');
        if(empty($state)){
            $state = '';
        }

        $is_active = Input::get('is_active');
        if(empty($is_active)){
            $is_active = 0;
        }

        $cell_phone = Input::get('cell_phone');
        if(empty($cell_phone)){
            $cell_phone = '';
        }
        $work_phone = Input::get('work_phone');
        if(empty($work_phone)){
            $work_phone = '';
        }
        $website = Input::get('website');
        if(empty($website)){
            $website = '';
        }
        $fax = Input::get('fax');
        if(empty($fax)){
            $fax = '';
        }
        $description = Input::get('description');
        if(empty($description)){
            $description = '';
        }
        $city = Input::get('city');
        if(empty($city)){
            $city = '';
        }
        $country = Input::get('country');
        if(empty($country)){
            $country = '';
        }
        $zipcode = Input::get('zipcode');
        if(empty($zipcode)){
            $zipcode = '';
        }

        /*user access*/

        $access_mentor = Input::get('access_mentor');
        if(empty($access_mentor)){
            $access_mentor = 0;
        }
        $access_victim = Input::get('access_victim');
        if(empty($access_victim)){
            $access_victim = '';
        }
        $acces_resource = Input::get('acces_resource');
        if(empty($acces_resource)){
            $acces_resource = '';
        }
        $access_job = Input::get('access_job');
        if(empty($access_job)){
            $access_job = '';
        }
        $access_goal_task_challenge = Input::get('access_goal_task_challenge');
        if(empty($access_goal_task_challenge)){
            $access_goal_task_challenge = '';
        }
        $access_e_learning = Input::get('access_e_learning');
        if(empty($access_e_learning)){
            $access_e_learning = '';
        }
        $access_agency = Input::get('access_agency');
        if(empty($access_agency)){
            $access_agency = '';
        }
        $access_meeting = Input::get('access_meeting');
        if(empty($access_meeting)){
            $access_meeting = '';
        }
        $services = Input::get('services');

        /*=====*/


        $profile_pic = Input::file('profile_pic');
        if(empty($profile_pic)){
            $profile_pic = '';
        }

        if(Auth::user()->type == 1){
            if($type == 'agency'){
                $parent_id = Auth::user()->id;
            }else if($type == 'user'){
                $parent_id = Input::get('parent_id');
            }

        }else if(Auth::user()->type == 2){
            $parent_id = Auth::user()->id;
        }else if(Auth::user()->type == 3){
            if(Auth::user()->parent_id == 1){
                $parent_id = Auth::user()->parent_id;
            }
        }


        if($type == 'agency'){
            $type = 2;
            // $parent_id = 0;
        }else{
            $type = 3;
        }

        //=======//
        $is_allow_keyword_notification = 0;

        if($type == 2){
            $timezone = Input::get('timezone');
            $is_allow_keyword_notification = 1;
        }

        if($type == 3){
            $description = '';
            $state = '';
            $city = '';
            $country = 'USA';
            $zipcode = '';

            $contact_type = '';
            $other_service = '';
            $timezone_data = DB::table('admins')->where('id',$parent_id)->first();
            $timezone = $timezone_data->timezone;
        }



        $characters = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789_');
        shuffle($characters);
        $rand_char = '';
        foreach (array_rand($characters, 30) as $k) $rand_char .= $characters[$k];
        $system_email =  $rand_char.rand(10000,99999)."@seeandsend.info";


        if(!empty($password)){
            $password_original = Input::get('password');
            $password = bcrypt(Input::get('password'));
            if(empty($id)){
                if(!empty($profile_pic)){
                    $image_name = time().uniqid(rand());
                    $image_name = $image_name.'.'.$profile_pic->getClientOriginalExtension();

                    // $storage_path = public_path() . '/uploads/agency_pic/';
                    // $profile_pic->move($storage_path,$image_name);

                    $filePath = 'agency_pic/' . $image_name;
                    Storage::disk('s3')->put($filePath, file_get_contents($profile_pic), 'public');

                }else{
                    $image_name = '';
                }


                $id = DB::table('admins')->insertGetId([ 'timezone'=>$timezone, 'type' => $type,'parent_id' => $parent_id, 'name' => $name, 'email' => $email,'password' => $password, 'profile_pic'=>$image_name, 'map_type'=> '' , 'address' => '' ,'state' => $state , 'latitude' => '' , 'longitude' => '' ,'jurisdiction_radius' => '' ,'is_active'=> $is_active,'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country , 'created_by'=> Auth::user()->id ,'is_allow_keyword_notification'=>$is_allow_keyword_notification ]);


                if($type == 3){
                    DB::table('user_access')->insert(['user_id'=>$id,'access_mentor'=>$access_mentor,'access_victim'=>$access_victim,'acces_resource'=>$acces_resource,'access_job'=>$access_job,'access_goal_task_challenge'=>$access_goal_task_challenge,'access_e_learning'=>$access_e_learning,'access_agency'=>$access_agency,'access_meeting'=>$access_meeting]);

                }


                session(['success_message' => 'Data added successfully']);

                $content = "<html><body><div>Hi, ".$name." thank you for registering to Take Stock In Children. <br/><table>
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Password</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>".$email."</td>
                    <td>".$password_original."</td>
                  </tr>
                </tbody>
              </table><br/>Regards</div></body></html>";
                $to = $email;

                $subject = 'User Registration Email';

                $headers  = 'MIME-Version: 1.0' . "\r\n";

                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: no_reply@seeandsend.info";

                //mail($to,$subject,$content,$headers);
                // email_send($to,$subject,$content);

            }else{

                if(!empty($profile_pic)){
                    $image_name = time().uniqid(rand());
                    $image_name = $image_name.'.'.$profile_pic->getClientOriginalExtension();

                    // $storage_path = public_path() . '/uploads/agency_pic/';
                    // $profile_pic->move($storage_path,$image_name);
                    $filePath = 'agency_pic/' . $image_name;
                    Storage::disk('s3')->put($filePath, file_get_contents($profile_pic), 'public');


                }else{
                    $resource_data = DB::table('admins')->where('id',$id)->first();
                    $image_name = $resource_data->profile_pic;
                }

                if(!empty($services)){
                    $admin_services = DB::table('admin_services')->where('user_id',$id)->get()->toarray();
                    $s_ids = [];
                    if(!empty($admin_services)){
                        foreach($admin_services as $ser){
                            $s_ids[] = $ser->id;
                        }
                        DB::table('admin_services')->whereIn('id', $s_ids)->delete();

                        foreach($services as $ser){
                            DB::table('admin_services')->insert(['user_id'=>$id,'service_id'=>$ser]);
                        }
                    }else{
                        foreach($services as $ser){
                            DB::table('admin_services')->insert(['user_id'=>$id,'service_id'=>$ser]);
                        }
                    }
                }

                if($type == 2){
                    $vic_ids = array();

                    DB::table('mentee')->where('assigned_by',$id)->update(['timezone'=>$timezone]);
                    // echo $id; die;
                    $staff_ids = array();
                    $staffs = DB::table('admins')->where('parent_id',$id)->get()->toarray();
                    if(!empty($staffs)){
                        foreach($staffs as $sf){
                            $staff_ids[] = $sf->id;
                        }
                    }
                    DB::table('admins')->whereIn('id',$staff_ids)->update(['timezone'=>$timezone]);

                }


                DB::table('admins')
                ->where('id', $id)
                ->update(['parent_id'=>$parent_id, 'timezone'=>$timezone, 'type' => $type, 'name' => $name, 'email' => $email,'password' => $password, 'profile_pic'=>$image_name, 'map_type'=> '' , 'address' => '' ,'state' => $state , 'latitude' => '' , 'longitude' => '' ,'jurisdiction_radius' => '' ,'is_active'=> $is_active, 'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country ]);

                if($type == 3){
                    DB::table('user_access')->where('user_id',$id)->update(['access_mentor'=>$access_mentor,'access_victim'=>$access_victim,'acces_resource'=>$acces_resource,'access_job'=>$access_job,'access_goal_task_challenge'=>$access_goal_task_challenge,'access_e_learning'=>$access_e_learning,'access_agency'=>$access_agency,'access_meeting'=>$access_meeting]);

                }


                session(['success_message' => 'Data updated successfully']);
            }
        }else{

            // echo $access_mentor; die;

            if(!empty($profile_pic)){
                $image_name = time().uniqid(rand());
                $image_name = $image_name.'.'.$profile_pic->getClientOriginalExtension();

                // $storage_path = public_path() . '/uploads/agency_pic/';
                // $profile_pic->move($storage_path,$image_name);
                $filePath = 'agency_pic/' . $image_name;
                Storage::disk('s3')->put($filePath, file_get_contents($profile_pic), 'public');

            }else{
                $resource_data = DB::table('admins')->where('id',$id)->first();
                $image_name = $resource_data->profile_pic;
            }

            if($type == 2){
                $staff_ids = array();
                $staffs = DB::table('admins')->where('parent_id',$id)->get()->toarray();
                if(!empty($staffs)){
                    foreach($staffs as $sf){
                        $staff_ids[] = $sf->id;
                    }
                }
                DB::table('admins')->whereIn('id',$staff_ids)->update(['timezone'=>$timezone]);

            }



            DB::table('admins')
            ->where('id', $id)
            ->update([ 'timezone'=>$timezone, 'type' => $type, 'name' => $name, 'email' => $email, 'profile_pic' => $image_name, 'map_type'=>'' , 'address' => '' ,'state' => $state , 'latitude' => '' , 'longitude' => '' ,'jurisdiction_radius' => '' ,'is_active'=> $is_active, 'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country ]);
            if($type == 3){
                DB::table('user_access')->where('user_id',$id)->update(['access_mentor'=>$access_mentor,'access_victim'=>$access_victim,'acces_resource'=>$acces_resource,'access_job'=>$access_job,'access_goal_task_challenge'=>$access_goal_task_challenge,'access_e_learning'=>$access_e_learning,'access_agency'=>$access_agency,'access_meeting'=>$access_meeting]);

            }
            session(['success_message' => 'Data updated successfully']);
        }

        $document_type_arr = $request->document_type;

        if(!empty($request->images)) {
            $i = 0;
            foreach($request->images as $all_images){
                $img = $all_images;

                $image_name = time().uniqid(rand());
                $image_name = $image_name.'.'.$img->getClientOriginalExtension();

                // $storage_path = public_path() . '/uploads/documents/';
                // $img->move($storage_path,$image_name);

                $filePath = 'documents/' . $image_name;
                Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');


                $document_type = $document_type_arr[$i];

                DB::table('admin_files')->insertGetId(['admin_id' => $id, 'document_type'=> $document_type, 'file_name' => $image_name, 'added_by' => Auth::user()->id]);
                $i++;
            }
        }

        if($type == 2){
            return redirect('/admin/agency');
        }else{
            return redirect('/admin/user');
        }
        // return redirect()->action('Admin\AgencyController@index');

    }

    public function delete_agency_files($id,$admin_id,$file_name)
    {
        if(!empty($id) && !empty($admin_id) && !empty($file_name)){
            DB::table('admin_files')->where('id',$id)->where('admin_id',$admin_id)->delete();
            // $file_path = public_path().'/uploads/documents/'.$file_name;
            // File::delete($file_path);
            $filePath = 'documents/'.$file_name;
            Storage::delete($filePath);



            session(['success_message' => 'File deleted successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);

            return redirect('/admin/agency/add/'.$admin_id.'?menu_type=agency');
        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            return redirect('/admin/agency/add/'.$admin_id.'?menu_type=agency');
        }

    }

    public function getcities(Request $request)
    {
        $input = $request->all();
        $state = !empty($input['state'])?$input['state']:'';

        $cities = DB::table('cities')->where('state_code',$state)->get()->toarray();
        if(!empty($cities)){
            ?>
            <option value="">Choose A City</option>
            <?php
            foreach($cities as $city){
                ?>
                <option value="<?php echo $city->city; ?>"><?php echo $city->city; ?></option>
                <?php
            }
        }else{
            return array();
        }


    }

    public function remove_agency_pic($id,$profile_pic,$type)
    {
        if(!empty($id) && !empty($profile_pic)){
            DB::table('admins')->where('id',$id)->update(['profile_pic'=>'']);
            $file_path = public_path().'/uploads/agency_pic/'.$profile_pic;
            File::delete($file_path);
            session(['success_message' => 'Profile picture has been removed successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);
            if($type == 'agency'){
                return redirect('/admin/agency/add/'.$id.'?menu_type=agency');
            }else{
                return redirect('/admin/agency/add/'.$id.'?menu_type=user');
            }

        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            if($type == 'agency'){
                return redirect('/admin/agency/add/'.$id.'?menu_type=agency');
            }else{
                return redirect('/admin/agency/add/'.$id.'?menu_type=user');
            }
        }
    }

    public function status_change_ajax(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            $agency = DB::table('admins')->where('id',$id)->first();
            if($agency->is_active == 1){
                DB::table('admins')->where('id',$id)->update(['is_active' => 0 ]);
                $message = 'Inactive';
            }else{
                DB::table('admins')->where('id',$id)->update(['is_active' => 1 ]);
                $message = 'Active';
            }
            return Response::json(array(
                'success' => true,
                'message'   => $message
            ));

        }else{
            return response()->json([ 'status' => false, 'message' => 'Error' ]);
        }
    }

    public function change_status($id,$uri,Request $request)
    {
        $id = !empty($request->id)?$request->id:'';
        $menu_type = $request->menu_type;

        if(!empty($id)){
            $agency = DB::table('admins')->where('id',$id)->first();
            if($agency->is_active == 1){
                DB::table('admins')->where('id',$id)->update(['is_active' => 0 ]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('admins')->where('id',$id)->update(['is_active' => 1 ]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }

            if($menu_type == 'agency'){
                if($uri == 'agency'){
                    return redirect('/admin/agency');
                }else if($uri == 'inactiveagencies'){
                    return redirect('/admin/inactiveagencies');
                }else{
                    return redirect('/admin/agency');
                }

            }else{
                if($uri == 'user'){
                    return redirect('/admin/user');
                }else if($uri == 'inactiveuser'){
                    return redirect('/admin/inactiveuser');
                }else{
                    return redirect('/admin/user');
                }

            }

        }
    }




    public function meeting_list(Request $request)
    {
    	$is_affiliate_view = 0;
        $sort_needed = 0;
        if(Auth::user()->type == 1){
            $is_affiliate_view = 1;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $is_affiliate_view = 1;
        }

        date_default_timezone_set('America/New_York');
        $today = date('Y-m-d H:i:s');
        $last_month = date('Y-m-d H:i:s', strtotime("-1 month"));

        $data = array();
        $search_added = !empty($request->search_added)?$request->search_added:'';
        $search_text = !empty($request->search_text)?$request->search_text:'';
        $search_year = !empty($request->search_year)?$request->search_year:'';
        $search_month = !empty($request->search_month)?$request->search_month:'';
        $search_status = isset($request->search_status)?$request->search_status:'';

        $orderByRaw = "";

        $sort = !empty($request->sort)?$request->sort:'';
        $column = !empty($request->column)?$request->column:'';

        if(!empty($sort) && !empty($column)){
            $sort_needed = 1;

            if($sort == 'asc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "a.name ASC ";
                }else if($column == 'mentor'){
                    $orderByRaw .= "mentor.firstname ASC ";
                }else if($column == 'mentee'){
                    $orderByRaw .= "mentee.firstname ASC ";
                }else if($column == 'schedule'){
                    $orderByRaw .= "m.schedule_time ASC ";
                }else if($column == 'status'){
                    $orderByRaw .= "meeting_status.status ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "a.name DESC ";
                }else if($column == 'mentor'){
                    $orderByRaw .= "mentor.firstname DESC ";
                }else if($column == 'mentee'){
                    $orderByRaw .= "mentee.firstname DESC ";
                }else if($column == 'schedule'){
                    $orderByRaw .= "m.schedule_time DESC ";
                }else if($column == 'status'){
                    $orderByRaw .= "meeting_status.status DESC ";
                }
            }

        }else if(!empty($sort) && empty($column)){
            if($sort == 'asc'){
                $orderByRaw .= "m.id ASC ";
            }else if($sort == 'desc'){
                $orderByRaw .= "m.id DESC ";
            }
        }elseif (empty($sort) && empty($column)) {
        	$orderByRaw .= "m.id DESC ";
        }

        $data = DB::table('meeting AS m')
                                ->select('m.id','m.title','m.agency_id','m.created_by','m.created_by_type','m.schedule_time','m.address','m.school_type','m.created_date','a.name','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname','meeting_status.status')
                                ->leftJoin('admins AS a', 'a.id', 'm.agency_id')
                                ->leftJoin('mentor','mentor.id','m.mentor_id')
                                ->leftJoin('mentee','mentee.id','m.mentee_id')
                                ->leftJoin('meeting_status','meeting_status.meeting_id','m.id');

        if(!empty($search_added)){


            if($search_status != ''){

                $whereStatusRaw = "";
                $whereStatusRaw .= " meeting_status.status = ".$search_status." ";
                $data = $data->whereRaw($whereStatusRaw);
                if(!empty($search_year) && !empty($search_month)){
                    $schedule_time_like = $search_year.'-'.$search_month;
                    $whereStatusRaw .= "AND m.schedule_time LIKE '%".$schedule_time_like."%' ";
                    $data = $data->whereRaw($whereStatusRaw);
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }else if(!empty($search_year) && empty($search_month)){
                    $schedule_time_like = $search_year;
                    $whereStatusRaw .= "AND m.schedule_time LIKE '%".$schedule_time_like."%' ";
                    $data = $data->whereRaw($whereStatusRaw);
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }else{
                    $data = $data->whereRaw("m.created_date BETWEEN '".$last_month."' AND '".$today."'");
                    $data = $data->whereRaw($whereStatusRaw);
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }
            }else{
                // die('Hi');
                $whereWithoutStatusRaw = "";
                if(!empty($search_year) && !empty($search_month)){
                    $schedule_time_like = $search_year.'-'.$search_month;
                    $whereWithoutStatusRaw .= "m.schedule_time LIKE '%".$schedule_time_like."%' ";
                    $data = $data->whereRaw($whereWithoutStatusRaw);
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }else if(!empty($search_year) && empty($search_month)){
                    $schedule_time_like = $search_year;
                    $whereWithoutStatusRaw .= "m.schedule_time LIKE '%".$schedule_time_like."%' ";
                    $data = $data->whereRaw($whereWithoutStatusRaw);
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }else{
                    if(!empty($search_text)){
                        $data = $data->where(function ($q) use ($search_text) {
                                        $q->where('m.title', 'like', '%'.$search_text.'%')
                                        ->orWhere('m.school_type', 'like', '%'.$search_text.'%')
                                        ->orWhere('a.name', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentor.lastname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.firstname', 'like', '%'.$search_text.'%')
                                        ->orWhere('mentee.lastname', 'like', '%'.$search_text.'%');
                                    });
                    }
                }
            }

        }else{
        	$data = $data->whereRaw("m.created_date BETWEEN '".$last_month."' AND '".$today."'");
        }

        if(Auth::user()->type == 2){
        	$agency_id = Auth::user()->id;
        	$data = $data->where('m.agency_id', $agency_id);
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id != 1){
        	$user_access = DB::table('user_access')->select('access_meeting')->where('user_id', Auth::user()->id)->first();

        	$access_meeting = $user_access->access_meeting;
        	$agency_id = Auth::user()->parent_id;
            // echo $agency_id; die;
            // echo $access_meeting; die;
        	if(!empty($access_meeting)){
        		$data = $data->where('m.agency_id', $agency_id);
        	}
        }


        $data = $data->orderByRaw($orderByRaw);
        // $data = $data->orderBy('m.id', 'desc');
        $data = $data->paginate(15);

        if(empty($sort)){
            $sort = 'desc';
        }

        // echo '<pre>'; print_r($data); die;
        // echo $sort;
        $data->appends(array('search_added' => $search_added))->links();
        $data->appends(array('search_year' => $search_year))->links();
        $data->appends(array('search_month' => $search_month))->links();
        $data->appends(array('search_status' => $search_status))->links();
        $data->appends(array('search_text' => $search_text))->links();
        $data->appends(array('sort' => $sort))->links();
        $data->appends(array('column' => $column))->links();

        return view('admin.meeting.list',['data' => $data, 'search_text' => $search_text , 'sort' => $sort, 'column' => $column ,'sort_needed' => $sort_needed, 'is_affiliate_view' => $is_affiliate_view, 'search_year'=>$search_year, 'search_month'=>$search_month, 'search_status'=>$search_status,'search_added'=>$search_added ]);
    }

    public function add_meetings($id,Request $request)
    {
        $id = Crypt::decrypt($id);
        $is_datetime_valid = true;

        if(Auth::user()->type == 2){
        	$agency_id = Auth::user()->id;
        }else if(Auth::user()->type == 3){
        	$agency_id = Auth::user()->parent_id;
        }

        $mentors = $meeting_data = $mentees = $school = $session_method_location = array();
        $mentors = DB::table('mentor')->where('assigned_by', $agency_id)->where('is_active', 1)->orderBy('firstname','asc')->get()->toarray();
        $mentees = DB::table('mentee')->where('assigned_by', $agency_id)->where('status', 1)->get()->toarray();
        $school = DB::table('school')->where('agency_id', $agency_id)->where('status',1)->get()->toarray();
        $session_method_location = DB::table('session_method_location')->select('*')->where('status',1)->get()->toarray();
        if(!empty($id)){
            $meeting_data = DB::table('meeting')->where('id',$id)->first();

            if($meeting_data->schedule_time < date('Y-m-d H:i:s')){
                $is_datetime_valid = false;
            }
        }

        // echo $is_datetime_valid; die;

        return view('admin.meeting.add' , ['mentors' => $mentors , 'mentees' =>$mentees ,  'meeting_data' => $meeting_data , 'school' => $school , 'is_datetime_valid' => $is_datetime_valid , 'session_method_location' => $session_method_location ]);
    }

    public function save_meetings(Request $request)
    {
//        dd($request);
//        $fcmApiKey = config('app.fcmApiKey');
        // echo $id; die;
        $id = Input::get('id');
        if(!empty($id)){

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                // 'description' => 'required',
                'mentor_ids' => 'required',
                'mentee_ids' => 'required'
                // 'school_id' => 'required'
            ]);


            if ($validator->fails()) {
                return redirect('admin/agency/meeting/add/'.Crypt::encrypt($id))
                            ->withErrors($validator)
                            ->withInput();
            }
        }else{
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                // 'description' => 'required',
                'mentor_ids' => 'required',
                'mentee_ids' => 'required'
                // 'school_id' => 'required'
            ]);


            if ($validator->fails()) {
                    return redirect('admin/agency/meeting/add/'.Crypt::encrypt(0))
                    ->withErrors($validator)
                    ->withInput();

            }
        }

        /* Validation */

        $title = Input::get('title');
        $description = Input::get('description');
        $mentor_ids = Input::get('mentor_ids');
        $mentee_ids = Input::get('mentee_ids');
        $schedule_time = Input::get('schedule_time');
        $school_id = Input::get('school_id');
        $school_location = Input::get('school_location');
        $school_type = Input::get('school_type');
        $session_method_location_id = Input::get('session_method_location_id');

        if(empty($title)){
            $title = "Mentor Session";
        }

        if(empty($description)){
            $description = "";
        }
        if(empty($school_location)){
            $school_location = "";
        }

        if(!empty($school_id)){
            $school_data = DB::table('school')->where('id', $school_id)->first();
            $address = !empty($school_data->address)?$school_data->address:'';
            $latitude = !empty($school_data->latitude)?$school_data->latitude:'';
            $longitude = !empty($school_data->longitude)?$school_data->longitude:'';
        }else{
            $address = "";
            $latitude = "";
            $longitude = "";
        }

        if(empty($school_type)){
            $school_type = "";
        }

        $schedule_time = str_replace("-", "/", $schedule_time);
        $schedule_time = date("Y-m-d H:i:s", strtotime($schedule_time));

        // echo $schedule_time; die;

        if(!empty(Auth::user()->timezone)){
        	date_default_timezone_set(Auth::user()->timezone);
			$created_date = date('Y-m-d H:i:s');
        }else{
        	$created_date = date('Y-m-d H:i:s');
        }

        if(Auth::user()->type == 2){
        	$agency_id = Auth::user()->id;
        }else if(Auth::user()->type == 3){
        	$agency_id = Auth::user()->parent_id;
        }

        if(empty($id)){
            $id = DB::table('meeting')->insertGetId(['title'=>$title,'description'=>$description,'agency_id'=>$agency_id,'schedule_time'=>$schedule_time,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude,'school_id'=>$school_id,'school_location'=>$school_location , 'session_method_location_id' => $session_method_location_id , 'school_type'=>$school_type , 'created_date' => $created_date , 'mentor_id' => $mentor_ids , 'mentee_id' => $mentee_ids , 'created_from' => 'affiliate_portal' ]);

            if(!empty($mentor_ids)){
                DB::table('meeting_users')->insert(['meeting_id'=>$id,'type'=>'mentor','user_id'=>$mentor_ids]);
            }
            if(!empty($mentee_ids)){
                DB::table('meeting_users')->insert(['meeting_id'=>$id,'type'=>'mentee','user_id'=>$mentee_ids]);
            }

            DB::table('meeting_status')->insert(['meeting_id'=>$id,'status'=>0]);
            DB::table('meeting_web_status')->insert(['meeting_id'=>$id,'web_status_date'=>$created_date]);

            /* ++++++++Mentee push notification ++++++++++ */



            $mentee_data = DB::table('mentee')->select('id','firebase_id','device_type')->where('id',$mentee_ids)->first();

            $mentee_notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'assign_meeting','user_type'=>'mentee','user_id'=>$mentee_ids,'notification_response'=>'','created_at'=>$created_date]);

            if(!empty($mentee_data->device_type) && !empty($mentee_data->firebase_id)){
                $message = "A new Mentor Session has been scheduled for you by your local affiliate. Please review and confirm the mentor session invite in the 'Schedule A Session' navigation on the Take Stock App profile screen.";

                $schedule_session_count = schedule_session_count('mentee',$mentee_ids);
                $user_total_chat_count = user_total_chat_count('mentee',$mentee_ids);
                $user_total_unread_goaltask_count = user_total_unread_goaltask_count('mentee',$mentee_ids);

                $unread_task_count = $schedule_session_count+$user_total_unread_goaltask_count;
                $send_data = array('title' => $message,'type' => 'meeting_notification' , 'meeting_id' => "$id",'message'=>$message,'firebase_token' => $mentee_data->firebase_id , 'unread_chat'=> "$user_total_chat_count", 'unread_task' => "$unread_task_count" );
                $data_arr = array('meeting_data' => json_encode($send_data));

                if($mentee_data->device_type == "iOS"){

                    $msg = array('message' => $message,'title' => $message, 'sound'=>"default", 'badge' => ($schedule_session_count+$user_total_chat_count+$user_total_unread_goaltask_count)  );
                    $fields = array('to' => $mentee_data->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

                }else if($mentee_data->device_type == "android"){

                    $fields = array('to' => $mentee_data->firebase_id,'data' => $send_data ); // For Android
                }

                $result = sendPushNotificationWithV1($fields);

                if ($result) {
                    if(!empty($result['name'])){
                        DB::table(MEETING_NOTIFICATION)->where('id',$mentee_notification_id)->update(['notification_response' => json_encode($result)]);

                    }
                }
            }

            /* ++++++++Mentor push notification ++++++++++ */
            $mentor_data = DB::table('mentor')->select('id','firebase_id','device_type')->where('id',$mentor_ids)->first();
            $mentor_notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$id,'notification_for'=>'assign_web_meeting','user_type'=>'mentor','user_id'=>$mentor_ids,'notification_response'=>'','created_at'=>$created_date]);

            if(!empty($mentor_data->device_type) && !empty($mentor_data->firebase_id)){
                $message = "A new mentor session has been scheduled by your local affiliate. Please review and confirm the session in the 'Schedule A Session' navigiation on the Take Stock App profile screen.";

                $schedule_session_count = schedule_session_count('mentor',$mentor_ids);
                $user_total_chat_count = user_total_chat_count('mentor',$mentor_ids);
                $user_total_unread_goaltask_count = user_total_unread_goaltask_count('mentor',$mentor_ids);

                $unread_task_count = $schedule_session_count+$user_total_unread_goaltask_count;
                $send_data = array('title' => $message,'type' => 'meeting_notification' , 'meeting_id' => "$id",'message'=>$message,'firebase_token' => $mentor_data->firebase_id , 'unread_chat'=> "$user_total_chat_count", 'unread_task' => "$unread_task_count" );
                $data_arr = array('meeting_data' => json_encode($send_data));

                if($mentor_data->device_type == "iOS"){

                    $msg = array('message' => $message,'title' => $message, 'sound'=>"default", 'badge' => ($schedule_session_count+$user_total_chat_count+$user_total_unread_goaltask_count)  );
                    $fields = array('to' => $mentor_data->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

                }else if($mentor_data->device_type == "android"){

                    $fields = array('to' => $mentor_data->firebase_id,'data' => $send_data ); // For Android
                }

                $result = sendPushNotificationWithV1($fields);

                if ($result) {
                    if(!empty($result['name'])){
                        DB::table(MEETING_NOTIFICATION)->where('id',$mentor_notification_id)->update(['notification_response' => json_encode($result)]);

                    }
                }
            }
            /* ++++++++++++++++++ */


            session(['success_message'=>"Session created successfully"]);
        }else{

            DB::table('meeting')->where('id', $id)->update(['title'=>$title,'description'=>$description,'schedule_time'=>$schedule_time,'school_id'=>$school_id,'address'=>$address,'latitude'=>$latitude,'longitude'=>$longitude,'school_location'=>$school_location , 'session_method_location_id' => $session_method_location_id , 'school_type'=>$school_type , 'mentor_id' => $mentor_ids , 'mentee_id' => $mentee_ids  ]);

            $mentor = DB::table('meeting_users')->where('meeting_id',$id)->where('type','mentor')->first();
            $mentee = DB::table('meeting_users')->where('meeting_id',$id)->where('type','mentee')->first();

            $mentor_id = $mentor->user_id;
            $mentee_id = $mentee->user_id;

            DB::table('meeting_users')->where('id',$mentor->id)->update(['user_id'=>$mentor_ids]);
            DB::table('meeting_users')->where('id',$mentee->id)->update(['user_id'=>$mentee_ids]);

            /*+++++++++++++++++++++++++++++++++*/

            session(['success_message'=>"Session updated successfully"]);
        }
        return redirect('admin/agency/meeting');


    }

    public function view_meeting(Request $request)
    {

        $id = $request->id;

        try{
            $dec_id = Crypt::decrypt($id);
            $data = DB::table('meeting')->select('meeting.*','meeting_status.status','session_method_location.method_value','mentee.firstname AS mentee_firstname','mentee.middlename AS mentee_middlename','mentee.lastname AS mentee_lastname','mentor.firstname AS mentor_firstname','mentor.middlename AS mentor_middlename','mentor.lastname AS mentor_lastname')->selectRaw("IFNULL(school.name,'') AS school_name,IFNULL(meeting_requests.note,'') AS note,DATE_FORMAT(meeting.schedule_time,'%m-%d-%Y') AS date,DATE_FORMAT(meeting.schedule_time,'%h:%i %p') AS time")->leftJoin('meeting_status', 'meeting_status.meeting_id','meeting.id')->leftJoin('session_method_location','session_method_location.id','meeting.session_method_location_id')->leftJoin('school','school.id','meeting.school_id')->leftJoin('meeting_requests','meeting_requests.meeting_id','meeting.id')->leftJoin('mentee', 'mentee.id','meeting.mentee_id')->leftJoin('mentor', 'mentor.id','meeting.mentor_id')->where('meeting.id',$dec_id)->first();

            // if($data->created_by_type == 'mentor'){
            //     $mentee = DB::table('meeting_users')->where('type','mentee')->where('meeting_id',$data->id)->first();
            //     $mentee_data = DB::table('mentee')->where('id',$mentee->user_id)->first();
            //     $data->mentee_name = $mentee_data->firstname.' '.$mentee_data->middlename.' '.$mentee_data->lastname;
            // }else{
            //     $data->mentee_name = "";
            // }
            // echo '<pre>'; print_r($data); die;
            return view('admin.meeting.view',['data'=>$data]);
        }catch(\Exception $e){
            // die('Hi');
            return redirect('/admin/agency/meeting');
        }

    }

    public function get_mentees_by_mentor(Request $request)
    {
        $data = array();
        $mentor_id = $request->mentor_id;
        $data = DB::table('assign_mentee')->select('assign_mentee.*', 'mentee.firstname','mentee.middlename','mentee.lastname','mentee.school_id','school.name AS school_name')->join('mentee', 'mentee.id', 'assign_mentee.mentee_id')->join('school','school.id','mentee.school_id')->where('assign_mentee.assigned_by', $mentor_id)->where('assign_mentee.is_primary',1)->orderBy('assign_mentee.created_date','desc')->get()->toarray();
        return json_encode(array('mentee'=>$data));
    }

    public function get_mentee_from_meeting_data(Request $request)
    {

        $id = $request->id;

        $mentor_ids = $mentee_ids = array();

        $mentor_ids = DB::table('meeting_users')->select('meeting_users.*','mentor.firstname','mentor.middlename','mentor.lastname')->join('mentor', 'mentor.id', 'meeting_users.user_id')->where('meeting_users.meeting_id', $id)->where('type', 'mentor')->get()->toarray();

        $mentee_ids = DB::table('meeting_users')->select('meeting_users.*','mentee.firstname','mentee.middlename','mentee.lastname','mentee.school_id','school.name AS school_name','meeting.school_type')->join('mentee', 'mentee.id', 'meeting_users.user_id')->join('school','school.id','mentee.school_id')->join('meeting','meeting.id','meeting_users.meeting_id')->where('meeting_users.meeting_id', $id)->where('type', 'mentee')->get()->toarray();



        $school_name = $mentee_ids[0]->school_name;
        $school_id = $mentee_ids[0]->school_id;

        $meeting_data = DB::table('meeting')->select('school_id')->where('id',$id)->first();
        if(!empty($meeting_data->school_id)){
            $is_school = 1;
        }else{
            $is_school = 0;
        }
        // if(!empty($meeting_data->school_id)){
        //     $school = DB::table('school')->select('name')->where('id',$meeting_data->school_id)->first();

        //     $school_name = !empty($school)?$school->name:'';
        // }else{
        //     $school_name = '';
        // }

        return  json_encode(array('mentor_ids'=>$mentor_ids , 'mentee_ids'=>$mentee_ids , 'school_name'=>$school_name, 'school_id'=>$school_id , 'is_school'=>$is_school)) ;
    }

    public function change_status_meetings($id,$uri)
    {
        if(!empty($id)){
            $meeting = DB::table('meeting')->where('id',$id)->first();
            if(!empty($meeting->status)){
                DB::table('meeting')->where('id',$id)->update(['status'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('meeting')->where('id',$id)->update(['status'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'meeting'){
                return redirect('/admin/agency/meeting');
            }else{
                return redirect('/admin/agency/inactivemeeting');
            }

        }
    }

    public function change_staff_keyword_alert_access($id,$parent_id)
    {
        // die('Hi');
        $staff = DB::table('admins')->select('id','is_allow_keyword_notification')->where('id',$id)->first();

        // echo '<pre>'; print_r($staff); die;
        if($staff->is_allow_keyword_notification == 1){
            DB::table('admins')->where('id',$id)->update(['is_allow_keyword_notification' => 0 ]);

            DB::table('admins')->where('id',$parent_id)->update(['is_allow_keyword_notification' => 1 ]);
            $message = 'Inactive';
        }else{

            DB::table('admins')->where('parent_id',$parent_id)->update(['is_allow_keyword_notification' => 0 ]);
            DB::table('admins')->where('id',$parent_id)->update(['is_allow_keyword_notification' => 0 ]);
            DB::table('admins')->where('id',$id)->update(['is_allow_keyword_notification' => 1 ]);
            $message = 'Active';
        }

        return redirect('/admin/user');

    }



}
