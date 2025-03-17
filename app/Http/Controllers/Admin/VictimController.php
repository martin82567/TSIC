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
class VictimController extends Controller
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
                    $orderByRaw .= "mentee.firstname ASC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentee.lastname ASC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentee.email ASC ";
                }else if($column == 'school'){
                    $orderByRaw .= "school.name ASC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentee.last_activity_at ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name DESC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentee.firstname DESC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentee.lastname DESC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentee.email DESC ";
                }else if($column == 'school'){
                    $orderByRaw .= "school.name DESC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentee.last_activity_at DESC ";
                }
            }
        
        }else if(!empty($sort) && empty($column)){
            $orderByRaw = "mentee.id DESC ";
        }
        
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',1)
                                ->orderBy('mentee.id', 'desc')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->paginate(15);
            }else{
                $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',1)
                                ->orderByRaw($orderByRaw)
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                        )
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title')
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',1)
                            ->where('mentee.assigned_by',Auth::user()->id)
                            ->orderBy('mentee.id', 'desc')
                            ->paginate(15);
            }else{
                $victim_arr = DB::table('mentee')                        
                        ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                        ->join('school', 'school.id', '=', 'mentee.school_id')
                        ->join('student_status', 'student_status.id', '=', 'mentee.status')
                        ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                )
                        ->where(function ($q) use ($query) {
                            $q->where('mentee.firstname', 'like', '%'.$query.'%')
                            ->orWhere('mentee.lastname','like', '%'.$query.'%')
                            ->orWhere('mentee.email','like', '%'.$query.'%')
                            ->orWhere('admins.name','like', '%'.$query.'%')
                            ->orWhere('school.name','like', '%'.$query.'%');
                        })
                        ->where('student_status.view_in_application',1)
                        ->where('mentee.platform_status',1)
                        ->where('mentee.assigned_by',Auth::user()->id)
                        ->orderByRaw($orderByRaw)
                        ->paginate(15);
                
            }
        }else{
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
                    $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',1)
                                ->orderBy('mentee.id', 'desc')
                                ->paginate(15);
                }else{
                    $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',1)
                            ->orderByRaw($orderByRaw)
                            ->paginate(15);
                    
                }

            }else{
                if(empty($sort)){
                    $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',1)
                                ->where('mentee.assigned_by',$parent_id)
                                ->orderBy('mentee.id', 'desc')
                                ->paginate(15);
                }else{
                    $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',1)
                            ->where('mentee.assigned_by',$parent_id)
                            ->orderByRaw($orderByRaw)
                            ->paginate(15);
                    
                }

            }            
        }

        if(empty($sort)){
            $sort = 'asc';
        }

        $victim_arr->appends(array('search' => $query))->links();
        $victim_arr->appends(array('sort' => $sort))->links();
        $victim_arr->appends(array('column' => $column))->links();

        $type = 'active';
        
        return view('admin.victim.list',['victim_arr' => $victim_arr, 'sort' => $sort, 'column' => $column , 'search' => $query,'sort_needed' => $sort_needed, 'type' => $type , 'is_affiliate_view' => $is_affiliate_view ]);
    }

    public function inactive_victim(Request $request)
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
                    $orderByRaw .= "mentee.firstname ASC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentee.lastname ASC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentee.email ASC ";
                }else if($column == 'school'){
                    $orderByRaw .= "school.name ASC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentee.last_activity_at ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'affiliate'){
                    $orderByRaw .= "admins.name DESC ";
                }else if($column == 'firstname'){
                    $orderByRaw .= "mentee.firstname DESC ";
                }else if($column == 'lastname'){
                    $orderByRaw .= "mentee.lastname DESC ";
                }else if($column == 'email'){
                    $orderByRaw .= "mentee.email DESC ";
                }else if($column == 'school'){
                    $orderByRaw .= "school.name DESC ";
                }else if($column == 'last_login'){
                    $orderByRaw .= "mentee.last_activity_at DESC ";
                }
            }
        
        }else if(!empty($sort) && empty($column)){
            $orderByRaw = "mentee.id DESC ";
        }
        
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',0)
                                ->orderBy('mentee.id', 'desc')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->paginate(15);
            }else{
                $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',0)
                                ->orderByRaw($orderByRaw)
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                        )
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title')
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',0)
                            ->where('mentee.assigned_by',Auth::user()->id)
                            ->orderBy('mentee.id', 'desc')
                            ->paginate(15);
            }else{
                $victim_arr = DB::table('mentee')                        
                        ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                        ->join('school', 'school.id', '=', 'mentee.school_id')
                        ->join('student_status', 'student_status.id', '=', 'mentee.status')
                        ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                )
                        ->where(function ($q) use ($query) {
                            $q->where('mentee.firstname', 'like', '%'.$query.'%')
                            ->orWhere('mentee.lastname','like', '%'.$query.'%')
                            ->orWhere('mentee.email','like', '%'.$query.'%')
                            ->orWhere('admins.name','like', '%'.$query.'%')
                            ->orWhere('school.name','like', '%'.$query.'%');
                        })
                        ->where('student_status.view_in_application',1)
                        ->where('mentee.platform_status',0)
                        ->where('mentee.assigned_by',Auth::user()->id)
                        ->orderByRaw($orderByRaw)
                        ->paginate(15);
                
            }
        }else{
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
                    $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',0)
                                ->orderBy('mentee.id', 'desc')
                                ->paginate(15);
                }else{
                    $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',0)
                            ->orderByRaw($orderByRaw)
                            ->paginate(15);
                    
                }

            }else{
                if(empty($sort)){
                    $victim_arr = DB::table('mentee')                                
                                ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                                ->join('school', 'school.id', '=', 'mentee.school_id')
                                ->join('student_status', 'student_status.id', '=', 'mentee.status')
                                ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                                ->where(function ($q) use ($query) {
                                    $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                    ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                    ->orWhere('mentee.email','like', '%'.$query.'%')
                                    ->orWhere('admins.name','like', '%'.$query.'%')
                                    ->orWhere('school.name','like', '%'.$query.'%');
                                })
                                ->where('student_status.view_in_application',1)
                                ->where('mentee.platform_status',0)
                                ->where('mentee.assigned_by',$parent_id)
                                ->orderBy('mentee.id', 'desc')
                                ->paginate(15);
                }else{
                    $victim_arr = DB::table('mentee')                            
                            ->join('admins', 'admins.id', '=', 'mentee.assigned_by')
                            ->join('school', 'school.id', '=', 'mentee.school_id')
                            ->join('student_status', 'student_status.id', '=', 'mentee.status')
                            ->select('mentee.*','admins.name as admin_name','school.name AS school_name',
                                        DB::raw("(SELECT count(*) FROM mentee_notes WHERE mentee_notes.victims_id = mentee.id)  as note_count" ),'student_status.title AS status_title'
                                    )
                            ->where(function ($q) use ($query) {
                                $q->where('mentee.firstname', 'like', '%'.$query.'%')
                                ->orWhere('mentee.lastname','like', '%'.$query.'%')
                                ->orWhere('mentee.email','like', '%'.$query.'%')
                                ->orWhere('admins.name','like', '%'.$query.'%')
                                ->orWhere('school.name','like', '%'.$query.'%');
                            })
                            ->where('student_status.view_in_application',1)
                            ->where('mentee.platform_status',0)
                            ->where('mentee.assigned_by',$parent_id)
                            ->orderByRaw($orderByRaw)
                            ->paginate(15);
                    
                }

            }            
        }

        if(empty($sort)){
            $sort = 'asc';
        }

        $victim_arr->appends(array('search' => $query))->links();
        $victim_arr->appends(array('sort' => $sort))->links();
        $victim_arr->appends(array('column' => $column))->links();

        $type = 'active';
        
        return view('admin.victim.list',['victim_arr' => $victim_arr, 'sort' => $sort, 'column' => $column , 'search' => $query,'sort_needed' => $sort_needed, 'type' => $type , 'is_affiliate_view' => $is_affiliate_view ]);
    }

    public function notelist($id)
    {  
        try {
        $id = Crypt::decrypt($id);

        $victim_details = DB::table('mentee')->where('id',$id)->first();
        $note_list = DB::table('mentee_notes')
                                ->join('admins', 'admins.id', '=', 'mentee_notes.added_by')
                                ->where('mentee_notes.victims_id',$id)
                                ->where('mentee_notes.status',1)
                                ->orderBy('mentee_notes.created_date', 'desc')
                                ->select('mentee_notes.*', 'admins.name as admins_name')
                                ->get();

        if(Auth::user()->type !=1 && !empty($note_list)){
            // DB::table('note_tracker')->insert(['victim_id' => $id,'user_id' => Auth::user()->id,'action' => 'View note.','note_id' => 0,'action_details' => '']);
        }   

        return view('admin.victim.victimnotelist',['victim_details' => $victim_details, 'note_list' => $note_list]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.mentee');
        }
    }

    public function notesave(Request $request)
    {

        $id = Input::get('victim_id');
        $enid = Crypt::encrypt($id);

        $validator = Validator::make($request->all(), [
            'note' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect('admin/mentee/notelist/'.$enid)
                        ->withErrors($validator)
                        ->withInput();
        }

        $note = Input::get('note');
       
        $image = '';

        if(!empty($request->image)){
            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $ext = strtolower($ext);
            if($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'mp4' && $ext != '3gp' && $ext != 'docx' && $ext != 'doc' && $ext != 'pdf'){
                session(['success_message' => 'Please upload correct file type.']);
                return redirect('admin/mentee/notelist/'.$enid);
            }
            $storage_path = public_path() . '/uploads/note/';
            $image_name = time().uniqid(rand());
            $image = $image_name.'.'.$img->getClientOriginalExtension();
            $img->move($storage_path,$image);
        }

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }
        
    
        $note_id = DB::table('mentee_notes')->insertGetId(['victims_id' => $id,'added_by' => Auth::user()->id,'note' => $note,'file' => $image , 'created_date'=>$created_date ]);
        // if(Auth::user()->type !=1){
            $action_details = array('victims_id' => $id,'added_by' => Auth::user()->id,'note' => $note,'file' => $image);
            DB::table('note_tracker')->insert(['victim_id' => $id,'user_id' => Auth::user()->id,'action' => 'Note added.','note_id' => $note_id,'action_details' => json_encode($action_details) , 'created_date'=>$created_date]); 
        // }

        return redirect('admin/mentee/notelist/'.$enid);
    }
    
    public function notefiledownload($note_id){ 

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }


        $note_details = DB::table('mentee_notes')->where('id',$note_id)->first();
        // if(Auth::user()->type !=1){
            DB::table('note_tracker')->insert(['victim_id' => $note_details->victims_id,'user_id' => Auth::user()->id,'action' => 'Note file download.','note_id' => $note_id,'action_details' => json_encode($note_details) , 'created_date' => $created_date ]);
        // }  
        $file_path = public_path('uploads/note/'.$note_details->file);          
        return response()->download($file_path);
    }

    public function notefiledelete($note_id){ 

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }


        $note_details = DB::table('mentee_notes')->where('id',$note_id)->first();
        // if(Auth::user()->type !=1){
            DB::table('note_tracker')->insert(['victim_id' => $note_details->victims_id,'user_id' => Auth::user()->id,'action' => 'Note deleted.','note_id' => $note_id,'action_details' => json_encode($note_details) , 'created_date' => $created_date ]);
        // }          
        DB::table('mentee_notes')->where('id', $note_id)->update(['status' => 0]);
        session(['success_message' => 'Data deleted successfully']);
        $enid = Crypt::encrypt($note_details->victims_id);
        return redirect('admin/mentee/notelist/'.$enid);
    }

    public function view_tracker(Request $request)
    {
        $id = $request->user_id; 
        // echo $id; die;
        $trackers = DB::table('note_tracker')->select('note_tracker.*','admins.name AS user_name')->join('admins' , 'admins.id','note_tracker.user_id')->where('note_tracker.victim_id',$id)->get()->toarray();

        if(!empty($trackers)){ ?>

        <div class="box">
            <div class="box-header">
              <!-- <h3 class="box-title">Tracker Details</h3> -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
            <div class="listing-table">
                <div class="table-responsive text-center">
              <table class="table table-hover">
                <thead>
                    <tr>
                      <th>Agency / Staff Name</th>
                      <th>Date & Time</th>
                      <th>Action</th>
                      <th>Additional</th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach($trackers as $tracker){
            //echo $img->data; ?>
            
                <tr>
                  <td>
                      <?php echo $tracker->user_name; ?>
                  </td>
                  <td><?php echo date('m-d-Y H:i:s', strtotime($tracker->created_date)); ?>
                  </td>
                  <td>
                    <?php echo $tracker->action; ?>
                  </td>
                  <td>
                    <?php
                    if(!empty($tracker->action_details)){
                        if($tracker->action == "Note added."){
                            $action_details = json_decode($tracker->action_details);
                            echo 'Note is added:-     '.$action_details->note; 
                        }else if($tracker->action == "Note file download."){
                            $action_details = json_decode($tracker->action_details);
                            if(!empty($action_details->file)){
                                echo ucfirst($action_details->file)." is downloaded successfully. ";
                            }
                        }else if($tracker->action == "Note deleted."){
                          echo 'Note is deleted:-     '.$action_details->note;
                        }

                    }
                     

                     ?>
                  </td>
                </tr>
            
            <?php 
            } ?>
            </tbody>
            </table>
            </div>
            </div>
            </div>

        <?php 
        }else{
            echo 'No data found';
        }

    }

    public function add($id)
    {
        # code...
        // try{
            $id = Crypt::decrypt($id);            
            $victim_details = DB::table('mentee')->where('id',$id)->first();

            $created_by = $goal = $task = $offer_type = $app_countries = $assign_goals = $assign_tasks = $admins_arr = $mentor_arr = $school_arr =  array();

            if(Auth::user()->type == 2){
                $mentor_arr = DB::table('mentor')->where('assigned_by', Auth::user()->id)->where('is_active',1)->get()->toarray();
                $school_arr = DB::table('school')->where('agency_id', Auth::user()->id)->where('status',1)->get()->toarray();
            }else if(Auth::user()->type == 3){
                if(Auth::user()->parent_id != 1){
                    $mentor_arr = DB::table('mentor')->where('assigned_by', Auth::user()->parent_id)->get()->toarray();
                    $school_arr = DB::table('school')->where('agency_id', Auth::user()->parent_id)->where('status',1)->get()->toarray();
                }
            }

            $mentor_id = 0;
            $school_id = 0;
            $mentor_name = "";
            $school_name = "";            

            if(!empty($id)){
                $school_id = $victim_details->school_id;
                // echo $school_id; die;
                $school_name = "";
                if(!empty($school_id)){
                    $school_data = DB::table('school')->where('id',$school_id)->first();
                    $school_name = $school_data->name;
                }

                $assign_mentee = DB::table('assign_mentee')->where('mentee_id', $id)->orderBy('created_date','desc')->first();
                if(!empty($assign_mentee)){
                    $mentor_id = $assign_mentee->assigned_by;
                    $mentor_value = DB::table('mentor')->where('id',$mentor_id)->first();
                    if(!empty($mentor_value)){
						$mentor_name = $mentor_value->firstname.' '.$mentor_value->middlename.' '.$mentor_value->lastname;
                    }else{
                    	$mentor_name = "";
                    }
                    
                }else{
                    $mentor_id = 0;
                    $mentor_name = "";
                }                

                if(Auth::user()->type == 1){
                    $created_by = array($victim_details->assigned_by,Auth::user()->id);

                    $goal = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','goal')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                    $task = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','task')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();
                    
                }else if(Auth::user()->type == 2){

                    $user_id = Auth::user()->id;
                    $goal = DB::table('goaltask')->where('type','goal')->where('status',1)
                                ->where(function ($q) use ($user_id) {
                                    $q->where('created_by', $user_id);
                                })
                                ->get();
                    $task = DB::table('goaltask')->where('type','task')->where('status',1)
                                ->where(function ($q) use ($user_id) {
                                    $q->where('created_by', $user_id);
                                })
                                ->get();
                    
                }else{ 

                    $parent_id = Auth::user()->parent_id;
                    $parent_type = DB::table('admins')->where('id',$parent_id)->first();
                    if($parent_type->type == 1){
                        // print_r($created_by); die;
                        $created_by = array($victim_details->assigned_by,Auth::user()->parent_id);
                           
                        $goal = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','goal')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                        $task = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','task')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();
                        
                    }else{
                        $goal = DB::table('goaltask')->where('type','goal')->where('status',1)
                                    ->where(function ($q) use ($parent_id) {
                                        $q->where('created_by', $parent_id);
                                    })
                                    ->get();
                        $task = DB::table('goaltask')->where('type','task')->where('status',1)
                                    ->where(function ($q) use ($parent_id) {
                                        $q->where('created_by', $parent_id);
                                    })
                                    ->get();

                    }
                }

            }
     
            
            $assign_goals = DB::table('assign_goal')->select('goaltask_id')->where('victim_id',$id)->get()->toarray();
            $assign_tasks = DB::table('assign_task')->select('goaltask_id')->where('victim_id',$id)->get()->toarray();            

            $admins_arr = DB::table('admins')->select('firstname','middlename','lastname','name','id')->where('is_admin',0)->where('type',2)->where('is_active',1)->get()->toarray();

            $affiliate_data = DB::table('admins')->select('firstname','middlename','lastname','name','id')->where('id',$victim_details->assigned_by)->first();

            $affiliate_name = !empty($affiliate_data->name)?$affiliate_data->name:'';

            $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first();

            if(!empty($id) &&  ($victim_details->assigned_by != Auth::user()->id) && (Auth::user()->is_admin != 1) && empty($user_access->access_victim)){
                return redirect()->route('admin.mentee');
            }


            $mentors = DB::table('assign_mentee')->select('assign_mentee.mentee_id','assign_mentee.assigned_by','assign_mentee.is_primary','mentor.firstname','mentor.middlename','mentor.lastname')->leftJoin('mentor','mentor.id','assign_mentee.assigned_by')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor_status.view_in_application',1)->where('assign_mentee.mentee_id',$id)->get()->toarray();
            if(!empty($mentors)){
                foreach($mentors as $m){
                    $video_chat_user = DB::select(DB::raw(" SELECT * FROM ".VIDEO_CHAT_USER." WHERE user_type = 'mentor-mentee' AND (sender_id = ".$m->assigned_by." AND sender_type = 'mentor' AND receiver_id = ".$id." AND receiver_type = 'mentee') OR (sender_id = ".$id." AND sender_type = 'mentee' AND receiver_id = ".$m->assigned_by." AND receiver_type = 'mentor') "));

                    $m->remaining_time = '';
                    $m->video_chat_week = array();
                    if(!empty($video_chat_user)){
                        $m->remaining_time = $video_chat_user[0]->remaining_time;
                        $video_chat_week = DB::table('video_chat_week')->where('chat_code',$video_chat_user[0]->chat_code)->orderBy('id','desc')->get()->toarray();
                        $m->video_chat_week = $video_chat_week;
                        
                    }
                }
            }
            
            // echo '<pre>'; print_r($mentors); die;

            $settings = DB::table('settings')->first();

            return view('admin.victim.add',['victim' => $victim_details, 'admins_arr' => $admins_arr, 'goal' => $goal, 'task' => $task, 'assign_goals' => $assign_goals, 'assign_tasks' => $assign_tasks, 'offer_type'=>$offer_type,'app_countries'=>$app_countries , 'mentor_arr'=>$mentor_arr , 'school_arr'=>$school_arr , 'mentor_id'=>$mentor_id, 'mentor_name'=>$mentor_name , 'school_id'=>$school_id,'school_name'=>$school_name,'affiliate_name'=>$affiliate_name,'mentors'=>$mentors,'settings'=>$settings]);

        // }catch(\Exception $e){
        //     return redirect()->route('admin.mentee');
        // }
    }

    public function add_bkp($id)
    {  
        // try{
            $id = Crypt::decrypt($id);
            
            $victim_details = DB::table('mentee')->where('id',$id)->first();

            $assign_victim = DB::table('assign_victim')->select('*')->where('victim_id',$id)->first();

            $created_by = $goal = $task = $challenge = $offer_type = $app_countries = $assign_goals = $assign_tasks = $assign_challenges = $admins_arr = $mentor_arr = $school_arr =  array();

            if(Auth::user()->type == 2){
                $mentor_arr = DB::table('mentor')->where('assigned_by', Auth::user()->id)->where('is_active',1)->get()->toarray();
                $school_arr = DB::table('school')->where('agency_id', Auth::user()->id)->where('status',1)->get()->toarray();
            }else if(Auth::user()->type == 3){
                if(Auth::user()->parent_id != 1){
                    $mentor_arr = DB::table('mentor')->where('assigned_by', Auth::user()->parent_id)->get()->toarray();
                    $school_arr = DB::table('school')->where('agency_id', Auth::user()->parent_id)->where('status',1)->get()->toarray();
                }
            }

            $mentor_id = 0;
            $school_id = 0;
            $mentor_name = "";
            $school_name = "";
            

            if(!empty($id)){

                $school_id = $victim_details->school_id;

                // echo $school_id; die;

                $school_name = "";

                if(!empty($school_id)){
                    $school_data = DB::table('school')->where('id',$school_id)->first();
                    $school_name = $school_data->name;
                }

                
                


                
                if(!empty($assign_victim->admin_id)){
                    $offer_type = DB::table('offer_type')->where('status',1)
                                  ->whereIn('created_by',array(1,$assign_victim->admin_id))
                                  ->get()->toarray();
                }else{
                    $offer_type = DB::table('offer_type')->where('status',1)
                                  ->where('created_by',1)
                                  ->get()->toarray();
                }

                $assign_mentee = DB::table('assign_mentee')->where('mentee_id', $id)->first();
                if(!empty($assign_mentee)){
                    $mentor_id = $assign_mentee->assigned_by;
                    $mentor_value = DB::table('mentor')->where('id',$mentor_id)->first();
                    $mentor_name = $mentor_value->firstname.' '.$mentor_value->middlename.' '.$mentor_value->lastname;
                }else{
                    $mentor_id = 0;
                    $mentor_name = "";
                }

                

                if(Auth::user()->type == 1){

                    $created_by = array($assign_victim->admin_id,Auth::user()->id);


                    $goal = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','goal')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                    $task = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','task')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                    $challenge = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','challenge')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();

                    // echo '<pre>'; print_r($goal);
                    // echo '<pre>'; print_r($task);
                    // echo '<pre>'; print_r($challenge); 
                    // die;
                }else if(Auth::user()->type == 2){

                    $user_id = Auth::user()->id;
                    $goal = DB::table('goaltask')->where('type','goal')->where('status',1)
                    ->where(function ($q) use ($user_id) {
                        $q->where('created_by', $user_id);
                    })
                    ->get();
                    $task = DB::table('goaltask')->where('type','task')->where('status',1)
                    ->where(function ($q) use ($user_id) {
                        $q->where('created_by', $user_id);
                    })
                    ->get();
                    $challenge = DB::table('goaltask')->where('type','challenge')->where('status',1)
                    ->where(function ($q) use ($user_id) {
                        $q->where('created_by', $user_id);
                    })
                    ->get();
                }else{ 

                    $parent_id = Auth::user()->parent_id;
                    $parent_type = DB::table('admins')->where('id',$parent_id)->first();
                    if($parent_type->type == 1){
                        // print_r($created_by); die;
                        $created_by = array($assign_victim->admin_id,Auth::user()->parent_id);
                           
                        $goal = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','goal')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                        $task = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','task')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();


                        $challenge = DB::table('goaltask')->select('goaltask.*','admins.id as ag_id','admins.type as ag_type')->join('admins', 'admins.id','goaltask.created_by')->where('goaltask.type','challenge')->where('goaltask.status',1)->whereIn('goaltask.created_by', $created_by)->get();

                    }else{
                        $goal = DB::table('goaltask')->where('type','goal')->where('status',1)
                        ->where(function ($q) use ($parent_id) {
                            $q->where('created_by', $parent_id);
                        })
                        ->get();
                        $task = DB::table('goaltask')->where('type','task')->where('status',1)
                        ->where(function ($q) use ($parent_id) {
                            $q->where('created_by', $parent_id);
                        })
                        ->get();
                        $challenge = DB::table('goaltask')->where('type','challenge')->where('status',1)
                        ->where(function ($q) use ($parent_id) {
                            $q->where('created_by', $parent_id);
                        })
                        ->get();

                    }
                }

            }

            /*---------new static databases(no backend module)--------------------- */  
            
            //$ethnicity = DB::table('ethnicity')->where('status',1)->get();
            
            /*----------------------------------------------------------------------*/
            //$app_countries = DB::table('apps_countries')->get()->toarray();          
            
            $assign_goals = DB::table('assign_goal')->select('goaltask_id')->where('victim_id',$id)->get()->toarray();
            $assign_tasks = DB::table('assign_task')->select('goaltask_id')->where('victim_id',$id)->get()->toarray();
            $assign_challenges = DB::table('assign_challenge')->select('goaltask_id')->where('victim_id',$id)->get()->toarray();

            $admins_arr = DB::table('admins')->select('firstname','middlename','lastname','name','id')->where('is_admin',0)->where('type',2)->where('is_active',1)->get()->toarray();

            
            

            $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first();



            if(!empty($id) && !empty($assign_victim) && ($assign_victim->admin_id != Auth::user()->id) && (Auth::user()->is_admin != 1) && empty($user_access->access_victim)){
                return redirect()->route('admin.mentee');
            }

            // echo '<pre>'; print_r($victim_details); 
            // echo $mentor_id;  die;

            return view('admin.victim.add',['victim' => $victim_details, 'assign_victim' => $assign_victim, 'admins_arr' => $admins_arr, 'goal' => $goal, 'task' => $task, 'challenge' => $challenge, 'assign_goals' => $assign_goals, 'assign_tasks' => $assign_tasks, 'assign_challenges' => $assign_challenges,'offer_type'=>$offer_type,'app_countries'=>$app_countries , 'mentor_arr'=>$mentor_arr , 'school_arr'=>$school_arr , 'mentor_id'=>$mentor_id, 'mentor_name'=>$mentor_name , 'school_id'=>$school_id,'school_name'=>$school_name]);

        // }catch(\Exception $e){
        //     return redirect()->route('admin.mentee');
        // }
    }

    public function save(Request $request)
    {        
        $id = Input::get('id');
        $enid = Crypt::encrypt($id);
        
        $email = Input::get('email');
        $password = Input::get('password');
        $is_chat_video = Input::get('is_chat_video');
        $platform_status = Input::get('platform_status');

        if(empty($is_chat_video)){
            $is_chat_video = 0;
        }
        if(empty($platform_status)){
            $platform_status = 0;
        }
          

        $admin_id = Input::get('admin_id_hid');
        
        if(!empty($admin_id)){
            $admin_data = DB::table('admins')->where('id',$admin_id)->first();
            $timezone = $admin_data->timezone;
            
        }else{
            $timezone = 'America/New_York';
            
        }


        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        } 

        


        if(!empty($id)){   
            $victim_data = DB::table('mentee')->where('id',$id)->first();

            if(!empty($password)){
                $password = bcrypt($password);
                DB::table('mentee')->where('id', $id)->update([ 'password' => $password , 'is_chat_video' => $is_chat_video , 'platform_status' => $platform_status , 'updated_at'=>$created_date]);                
            }else{                
                DB::table('mentee')->where('id', $id)->update([ 'is_chat_video' => $is_chat_video , 'platform_status' => $platform_status , 'updated_at'=>$created_date ]);
            }            

            session(['success_message' => 'Data updated successfully']);          
            
        }
                
        return redirect()->action('Admin\VictimController@index');
    }

    public function save_bkp(Request $request)
    {        
        $id = Input::get('id');
        $enid = Crypt::encrypt($id);
        if(empty($id)){
            $validator = Validator::make($request->all(), [
                'firstname' => 'required',
                'lastname' => 'required',
                'admin_id' => 'required',
                'email' => 'required|email|unique:victims,email,'.Input::get('id'),
                'password' => 'required'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                
                'email' => 'required|email|unique:victims,email,'.Input::get('id')
            ]);
        }
        
        if ($validator->fails()) {
            return redirect('admin/mentee/add/'.$enid)
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
        $password = Input::get('password');
        
        $current_living_details = Input::get('current_living_details');
        if(empty($current_living_details)){
            $current_living_details = '';
        }
        
        $cell_phone_number = Input::get('cell_phone_number');
        if(empty($cell_phone_number)){
            $cell_phone_number = '';
        }
        $home_phone_number = Input::get('home_phone_number');
        if(empty($home_phone_number)){
            $home_phone_number = '';
        }
        
                
        $status = Input::get('status');
        if(empty($status)){
            $status = 0;
        }

        $admin_id = Input::get('admin_id');
        if(empty($admin_id)){
            $admin_id = 0;
        }

        $mentor_id = Input::get('mentor_id');

        if(!empty($admin_id)){
            $admin_data = DB::table('admins')->where('id',$admin_id)->first();
            $timezone = $admin_data->timezone;
            $latitude = !empty($admin_data->latitude)?$admin_data->latitude:'';
            $longitude = !empty($admin_data->longitude)?$admin_data->longitude:'';
        }else{
            $timezone = 'America/New_York';
            $latitude = '';
            $longitude = '';
        }

        // echo $timezone; die;

        if(Auth::user()->type == 3){
           $created_by = Auth::user()->parent_id;
        }else{
            $created_by = Auth::user()->id;
        } 

        

        if(!empty($timezone)){
            date_default_timezone_set($timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        } 

        $image = Input::file('image');
        if(empty($image)){
            $image = '';
        } 

        $school_id = Input::get('school_id');

        if(empty($school_id)){
            $school_id = 0;
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
                $storage_path = public_path() . '/uploads/userimage/';
                $image_name = time().uniqid(rand());
                $image_val = $image_name.'.'.$img->getClientOriginalExtension();
                $img->move($storage_path,$image_val);
            }else{
                $image_val = "";
            }

            $id = DB::table('mentee')->insertGetId(['timezone'=>$timezone, 'firstname' => $firstname,'middlename' => $middlename,'lastname' => $lastname,'email' => $email,'latitude'=>$latitude,'longitude'=>$longitude,'password' => $password,'image'=>$image_val,'cell_phone_number' => $cell_phone_number,'home_phone_number' => $home_phone_number, 'created_by' => $created_by , 'assigned_by' =>$admin_id , 'status' => $status , 'created_at' => $created_date, 'current_living_details' => $current_living_details ]); 

            DB::table('assign_victim')->insert(['admin_id' => $admin_id,'victim_id' => $id]); 
            DB::table('assign_mentee')->insert(['mentee_id' => $id,'assigned_by' => $mentor_id]); 


            /*Mail*/            
            
            $content = "<html>
                        <body>
                        <div>Hi, ".$firstname.' '.$middlename.' '.$lastname." thank you for registering to Take Stock In Children. <br/>
                        <table>
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
                        </table>
                        <br/>Regards</div>
                        </body>
                        </html>";

            $to = $email;                
            $subject = 'Client Registration Email';
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: no_reply@seeandsend.info";

            //mail($to,$subject,$content,$headers);

            session(['success_message' => 'Data added successfully']);

            
            
        }else{

            $victim_data = DB::table('mentee')->where('id',$id)->first();

            if(!empty($image)){
                $img = $image;
                $ext = $img->getClientOriginalExtension();
                $ext = strtolower($ext);
                if($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'mp4' && $ext != '3gp' && $ext != 'docx' && $ext != 'doc' && $ext != 'pdf'){
                    session(['error_message' => 'Please upload correct file type.']);
                    // return redirect('admin/mentee/notelist/'.$enid);
                }
                $storage_path = public_path() . '/uploads/userimage/';
                $image_name = time().uniqid(rand());
                $image_val = $image_name.'.'.$img->getClientOriginalExtension();
                $img->move($storage_path,$image_val);
            }else{
                

                $image_val = $victim_data->image;
            }

            if(!empty($password)){

                $password = bcrypt($password);
                DB::table('mentee')
                ->where('id', $id)
                ->update([ 'password' => $password]);
                /*->update([ 'timezone'=>$timezone, 'password' => $password,'image' => $image_val ,'cell_phone_number' => $cell_phone_number,'home_phone_number' => $home_phone_number,'status' => $status,'updated_at'=>$created_date, 'current_living_details' => $current_living_details ]);*/
            }else{
                
                /*DB::table('mentee')
                ->where('id', $id)
                ->update([ 'timezone'=>$timezone, 'image' => $image_val,'cell_phone_number' => $cell_phone_number,'home_phone_number' => $home_phone_number,'status' => $status,'updated_at'=>$created_date, 'current_living_details' => $current_living_details ]);*/
            }

            /*$assign_victim = DB::table('assign_victim')->select('*')->where('victim_id', $id)->first();
            if(!empty($assign_victim)){
                DB::table('assign_victim')->where('id', $assign_victim->id)->update(['admin_id' => $admin_id]);
            }else{
                DB::table('assign_victim')->insert(['admin_id' => $admin_id,'victim_id' => $id, 'created_date'=>$created_date]); 
            }

            $assign_mentee = DB::table('assign_mentee')->select('*')->where('mentee_id', $id)->orderBy('created_date','desc')->first();
            if(!empty($assign_mentee)){
                DB::table('assign_mentee')->where('id', $assign_mentee->id)->update(['assigned_by' => $mentor_id]);
            }else{
                DB::table('assign_mentee')->insert(['mentee_id' => $id,'assigned_by' => $mentor_id]); 
            }*/

            

            session(['success_message' => 'Data updated successfully']);
        }
                
        return redirect()->action('Admin\VictimController@index');
    }

    

    public function viewnote($goaltask_id,$victim_id){
        try {
        $goaltask_id = Crypt::decrypt($goaltask_id);
        $victim_id = Crypt::decrypt($victim_id);

        $goaltask_details = DB::table('goaltask')->where('id',$goaltask_id)->first();
        $victim_note_arr = DB::table('goaltask_note')->where('goaltask_id',$goaltask_id)->where('victim_id',$victim_id)->get();
        return view('admin.victim.notelist',['victim_note_arr' => $victim_note_arr, 'goaltask_details' => $goaltask_details]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.mentee');
        }
    }

    public function change_status($id,$uri)
    {
        if(!empty($id)){
            $victim = DB::table('mentee')->where('id',$id)->first();
            if(!empty($victim->status)){
                DB::table('mentee')->where('id',$id)->update(['status'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('mentee')->where('id',$id)->update(['status'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'victim'){
                return redirect('/admin/mentee');
            }else{
                return redirect('/admin/inactivementee');
            }
            
        }
    }

    

    

    public function add_offerings(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $offer_type_id = $request->offer_type_id;
        $is_accepted = $request->is_accepted;
        $note = $request->note;
        $agency_id = Auth::user()->id;

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }

        
        DB::table('mentee_offers')->insert(['mentee_id' => $mentee_id , 'offer_type_id' => $offer_type_id , 'note' => $note , 'is_accepted' => $is_accepted , 'agency_id' => $agency_id , 'created_at' =>$created_date ]);
        $mentee_id = Crypt::encrypt($mentee_id);
        return redirect('admin/mentee/add/'.$mentee_id);
    }

    public function view_offerings(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $offerings = DB::table('mentee_offers')->select('mentee_offers.*','offer_type.name as offer_type')->join('offer_type' , 'offer_type.id','mentee_offers.offer_type_id')->where('mentee_offers.mentee_id',$mentee_id)->get()->toarray();

        ?>
        <div class="listing-table">
        <div class="table-responsive text-center">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Offer Type</th>
                    <th>Accepted</th>
                    <th>Note</th>                                    
                    <th>Date&Time</th>                                    
                </tr>
            </thead>
            <tbody>
                

        <?php 

        if(!empty($offerings)){ 
            foreach($offerings as $of){


        ?>
                <tr>
                    <td><?php echo $of->offer_type;?></td>
                    <td><?php if(!empty($of->is_accepted)){ echo 'Yes'; }else{ echo 'No'; }?></td>
                    <td><?php echo $of->note;?></td>
                    <td><?php echo date('m-d-Y H:i:s' , strtotime($of->created_at))?></td>
                </tr>
            

        <?php } 
        }else{
            echo '<tr><td colspan="4" style="text-align:center;">No data found</td></tr>';
        }?>
            </tbody>
        </table>
        </div>
        </div>
        <?php 
    }

    

    

    public function add_note(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $note = $request->note1;
        $agency_id = Auth::user()->id;

        $image = '';

        // $file_name = Request::file('file');
        
        if(!empty($request->image)) {

            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $ext = strtolower($ext);
            if($ext != 'png' && $ext != 'jpg' && $ext != 'jpeg' && $ext != 'mp4' && $ext != '3gp' && $ext != 'docx' && $ext != 'doc' && $ext != 'pdf'){
                session(['error_message' => 'Please upload correct file type.']);
                // return redirect('admin/mentee/notelist/'.$enid);
            }
            $storage_path = public_path() . '/uploads/note/';
            $image_name = time().uniqid(rand());
            $image = $image_name.'.'.$img->getClientOriginalExtension();
            $img->move($storage_path,$image);

        }

        // echo $image; die;

        

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }

        
        // DB::table('mentee_notes')->insert(['mentee_id' => $mentee_id , 'note' => $note , 'agency_id' => $agency_id , 'created_at' => $created_date ]);

        $note_id = DB::table('mentee_notes')->insertGetId(['victims_id' => $mentee_id,'added_by' => $agency_id,'note' => $note,'file' => $image , 'created_date'=>$created_date ]);

        $action_details = array('victims_id' => $mentee_id,'added_by' => $agency_id,'note' => $note,'file' => $image);
        DB::table('note_tracker')->insert(['victim_id' => $mentee_id,'user_id' => $agency_id,'action' => 'Note added.','note_id' => $note_id,'action_details' => json_encode($action_details) , 'created_date'=>$created_date]); 


        $mentee_id = Crypt::encrypt($mentee_id);



        return redirect('admin/mentee/add/'.$mentee_id);
    }

    public function view_note(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $notes = DB::table('mentee_notes')->select('mentee_notes.*','admins.name')->join('admins', 'admins.id','mentee_notes.added_by')->where('mentee_notes.victims_id',$mentee_id)->orderBy('mentee_notes.id','desc')->get()->toarray();
        // $notes = DB::table('mentee_notes')->where('mentee_id',$mentee_id)->get()->toarray();

        ?>
        <div class="listing-table">
        <div class="table-responsive text-center">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Sl No.</th>                                    
                    <th>Agency</th>                                    
                    <th>Note</th>                                    
                    <th>File</th>                                    
                    <th>Date&Time</th>                                    
                </tr>
            </thead>
            <tbody>
        <?php 

        if(!empty($notes)){ $id=1;
            foreach($notes as $note){
        ?>
                <tr>
                    <td><?php echo $id;?></td>
                    <td><?php echo $note->name;?></td>
                    <td class="col-lg-3"><?php echo $note->note;?></td>
                    <td><?php echo !empty($note->file)?'<a href="'.url('/public').'/uploads/note/'.$note->file.'" target="_blank">'.$note->file.'</a>':'';?></td>
                    <td><?php echo date('m-d-Y H:i:s' , strtotime($note->created_date));?></td>
                </tr>
            

        <?php 
            $id++;
            } 
        }else{
            echo '<tr><td colspan="5" style="text-align:center;">No data found</td></tr>';
        }?>
            </tbody>
        </table>
        </div>
        </div>


        <?php 
    }

    public function assign_task(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $note = $request->note;
        $agency_id = Auth::user()->id;

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }

        $dts = $intersect = $diff = $assigned_tasks =  array();

        $data1 = DB::table('assign_task')->select('goaltask_id')->where('victim_id',$mentee_id)->get()->toarray();

        if(!empty($data1)){
            foreach($data1 as $dt1){
                $dts[] = $dt1->goaltask_id;
            }
        }


        if(!empty($request->tasks)) { 
            foreach($request->tasks as $key => $value){ 
                
                $assigned_tasks[] = $value;
            }                     
        }

        $intersect = array_intersect($assigned_tasks,$dts);

        $diff = array_diff($assigned_tasks,$dts) ;
        $diff1 = array_diff($dts,$assigned_tasks) ;

        if(empty($intersect)){
            DB::table('assign_task')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$dts)->delete();
        }else if(!empty($diff1)){
            DB::table('assign_task')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$diff1)->delete();
        }
        
        if(!empty($diff)){
            foreach($diff as $k => $v){
                DB::table('assign_task')->insert(['victim_id' => $mentee_id, 'goaltask_id' => $v , 'created_date'=>$created_date]);
            }
            // DB::table('assign_task')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$diff)->delete();
        }
        $mentee_id = Crypt::encrypt($mentee_id);
        return redirect('admin/mentee/add/'.$mentee_id);
    }

    public function assign_goal(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $note = $request->note;
        $agency_id = Auth::user()->id;

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }

        $dts = $intersect = $diff = $assigned_goals =  array();                
        
        $data1 = DB::table('assign_goal')->select('goaltask_id')->where('victim_id',$mentee_id)->get()->toarray();

        if(!empty($data1)){
            foreach($data1 as $dt1){
                $dts[] = $dt1->goaltask_id;
            }
        }

        echo '<pre>'; print_r($dts); 

        if(!empty($request->goals)) { 
            foreach($request->goals as $key => $value){ 
                
                $assigned_goals[] = $value;
            }                     
        }

        echo '<pre>'; print_r($assigned_goals); 

        $intersect = array_intersect($assigned_goals,$dts);

        echo '<pre>'; print_r($intersect); 

        $diff = array_diff($assigned_goals,$dts) ;

        $diff1 = array_diff($dts,$assigned_goals) ;

        echo '<pre>'; print_r($diff); 
        echo '<pre>'; print_r($diff1); 

        // die;

        if(empty($intersect)){
            DB::table('assign_goal')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$dts)->delete();
        }else if(!empty($diff1)){
            DB::table('assign_goal')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$diff1)->delete();
        }
        
        if(!empty($diff)){
            foreach($diff as $k => $v){
                DB::table('assign_goal')->insert(['victim_id' => $mentee_id, 'goaltask_id' => $v , 'created_date'=>$created_date]);
            }
            
        }
        $mentee_id = Crypt::encrypt($mentee_id);
        return redirect('admin/mentee/add/'.$mentee_id);
    }

    public function assign_challenge(Request $request)
    {
        $mentee_id = $request->mentee_id;
        $note = $request->note;
        $agency_id = Auth::user()->id;

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s') ;
        }else{
            $created_date = date('Y-m-d H:i:s') ;
        }

        $dts = $intersect = $diff = $assigned_goals =  array();
        
        $data1 = DB::table('assign_challenge')->select('goaltask_id')->where('victim_id',$mentee_id)->get()->toarray();

        if(!empty($data1)){
            foreach($data1 as $dt1){
                $dts[] = $dt1->goaltask_id;
            }
        }

        echo '<pre>'; print_r($dts); 

        if(!empty($request->challenges)) { 
            foreach($request->challenges as $key => $value){ 
                
                $assigned_challenges[] = $value;
            }                     
        }

        echo '<pre>'; print_r($assigned_challenges); 

        $intersect = array_intersect($assigned_challenges,$dts);

        echo '<pre>'; print_r($intersect); 

        $diff = array_diff($assigned_challenges,$dts) ;

        $diff1 = array_diff($dts,$assigned_challenges) ;

        echo '<pre>'; print_r($diff); 
        echo '<pre>'; print_r($diff1); 

        // die;

        if(empty($intersect)){
            DB::table('assign_challenge')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$dts)->delete();
        }else if(!empty($diff1)){
            DB::table('assign_challenge')->where('victim_id',$mentee_id)->whereIn('goaltask_id',$diff1)->delete();
        }
        
        if(!empty($diff)){
            foreach($diff as $k => $v){
                DB::table('assign_challenge')->insert(['victim_id' => $mentee_id, 'goaltask_id' => $v, 'created_date'=>$created_date]);
            }
            
        }
        $mentee_id = Crypt::encrypt($mentee_id);
        return redirect('admin/mentee/add/'.$mentee_id);
    }

    public function view_assign_gtc(Request $request)
    {
        $id = $request->id;
        $type = $request->type;

        if($type == 'goal'){
            $data = DB::table('assign_goal')->select('assign_goal.*','goaltask.name')->join('goaltask', 'goaltask.id', 'assign_goal.goaltask_id')->where('victim_id',$id)->get()->toarray();
        }else if($type == 'task'){
            $data = DB::table('assign_task')->select('assign_task.*','goaltask.name')->join('goaltask', 'goaltask.id', 'assign_task.goaltask_id')->where('victim_id',$id)->get()->toarray();
        }else if($type == 'challenge'){
            $data = DB::table('assign_challenge')->select('assign_challenge.*','goaltask.name')->join('goaltask', 'goaltask.id', 'assign_challenge.goaltask_id')->where('victim_id',$id)->get()->toarray();
        }

        // echo '<pre>'; print_r($data); die;
        ?>
        <div class="listing-table">
        <div class="table-responsive text-center">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Sl No.</th> 
                    <?php if($type == 'goal'){?>                                   
                    <th>Goal</th>  
                    <?php }else if($type == 'task'){?>
                    <th>Task</th>    
                    <?php }else if($type == 'challenge'){?>
                    <th>Challenge</th>    
                    <?php }?> 
                    <th>Status</th>
                    <th>Date&Time</th>                                 
                </tr>
            </thead>
            <tbody>
        <?php 
        if(!empty($data)){ 
            $i=1;
            foreach($data as $d){
        ?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td><?php echo $d->name;?></td>
                    <td><?php if($d->status == 0) { $status = "Pending"; }else if($d->status == 1){ $status = "Begin"; }else if($d->status == 2){ $status = "Completed"; } echo $status; ?></td>
                    <td><?php echo date('m-d-Y H:i:s' , strtotime($d->created_date));?></td>
                </tr>
        <?php
            $i++;
            } 

        }else{
            echo '<tr><td colspan="4" style="text-align:center;">No data found</td></tr>';

        } ?>
            </tbody>
        </table>
        </div>
        </div>
        <?php 

    }
    
    public function get_mentor_from_agency(Request $request)
    {
        $agency_id = $request->agency_id;
        $data = array();
        $data = DB::table('mentor')->where('assigned_by', $agency_id)->where('is_active', 1)->get()->toarray();
        $school = DB::table('school')->where('agency_id', $agency_id)->where('status', 1)->get()->toarray();
        return json_encode(array('mentor'=>$data,'school'=>$school));

    }

    public function view_report($id)
    {
        try{
            $id = Crypt::decrypt($id);
            $data = array();
            $data = DB::table('report')->where('mentee_id',$id)->orderBy('id','desc')->paginate(15);
            return view('admin.victim.reportlist')->with('data',$data); 
        }catch(\Exception $e){
            return redirect('/admin/mentee');
        }
        
    }

   

}