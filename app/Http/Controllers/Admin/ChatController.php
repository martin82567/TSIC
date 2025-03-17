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
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
class ChatController extends Controller
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

/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function index(Request $request)
    {
        // $view_chat = 0;

        // if(Auth::user()->type == 2){
        //     $view_chat = 1;
        // }else{
        //     if(!empty(Auth::user()->is_allow_keyword_notification)){
        //         $view_chat = 1;
        //     }
        // }

        $mentor = $staff = $mentee =  array();
        $view_chat = !empty($request->view_chat)?$request->view_chat:'';
        $type = !empty($request->type)?$request->type:'';
        $read_type = !empty($request->read_type)?$request->read_type:'';
        $search = !empty($request->search)?$request->search:'';
        $sort1 = !empty($request->sort1)?$request->sort1:'';
        $column_name1 = !empty($request->column_name1)?$request->column_name1:'';
        $is_super = 0;

        if(empty($view_chat)){
            $parent_id = Auth::user()->parent_id;

            if($parent_id != 1){  /*Not Super Admin*/

                if(empty($sort1)){

                    if(!empty($read_type)){

                        $mentor =  DB::table('mentor')
                                    ->select('mentor.*')
                                    ->join('mentor_staff_chat_threads', 'mentor_staff_chat_threads.sender_id', '=', 'mentor.id')
                                    ->leftJoin('mentor_status','mentor_status.id','mentor.is_active')
                                    ->where('mentor_staff_chat_threads.receiver_id',Auth::user()->id)
                                    ->where('mentor_staff_chat_threads.from_where','mentor')
                                    ->where('mentor_staff_chat_threads.receiver_is_read',0)
                                    ->where('mentor_status.view_in_application',1)
                                    ->groupBy('mentor_staff_chat_threads.sender_id')
                                    ->paginate(10);


                    }else{
                        $mentor = DB::table('mentor')
                                        ->select('mentor.*')
                                        ->leftJoin('mentor_status','mentor_status.id','mentor.is_active')
                                        ->where('mentor.assigned_by',$parent_id)
                                        ->where('mentor_status.view_in_application',1)
                                        ->where(function ($q) use ($search) {
                                            $q->where('mentor.firstname', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('mentor.id','desc')
                                        ->paginate(10);
                    }

                }else{
                    if(!empty($read_type)){

                        $mentor =  DB::table('mentor')
                                    ->select('mentor.*')
                                    ->leftJoin('mentor_status','mentor_status.id','mentor.is_active')
                                    ->join('mentor_staff_chat_threads', 'mentor_staff_chat_threads.sender_id', '=', 'mentor.id')
                                    ->where('mentor_staff_chat_threads.receiver_id',Auth::user()->id)
                                    ->where('mentor_staff_chat_threads.from_where','mentor')
                                    ->where('mentor_staff_chat_threads.receiver_is_read',0)
                                    ->where('mentor_status.view_in_application',1)
                                    ->groupBy('mentor_staff_chat_threads.sender_id')
                                    ->paginate(10);

                    }else{
                        $mentor = DB::table('mentor')
                                        ->select('mentor.*')
                                        ->leftJoin('mentor_status','mentor_status.id','mentor.is_active')
                                        ->where('mentor.assigned_by',$parent_id)
                                        ->where('mentor_status.view_in_application',1)
                                        ->where(function ($q) use ($search) {
                                            $q->where('mentor.firstname', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('mentor.firstname', $sort1)
                                        ->paginate(10);
                    }

                }


                $mentor->appends(['type'=>$type,'search'=>$search,'sort1' => $sort1,'column_name1' => $column_name1 ]);


                if(empty($sort1)){
                    if(!empty($read_type)){
                        $mentee =  DB::table('mentee')
                                    ->select('mentee.*')
                                    ->leftJoin('student_status','student_status.id','mentee.status')
                                    ->join('mentee_staff_chat_threads', 'mentee_staff_chat_threads.sender_id', '=', 'mentee.id')
                                    ->where('mentee_staff_chat_threads.receiver_id',Auth::user()->id)
                                    ->where('mentee_staff_chat_threads.from_where','mentee')
                                    ->where('mentee_staff_chat_threads.receiver_is_read',0)
                                    ->where('student_status.view_in_application',1)
                                    ->groupBy('mentee_staff_chat_threads.sender_id')
                                    ->paginate(10);
                    }else{
                        $mentee = DB::table('mentee')
                                        ->select('mentee.*')
                                        ->leftJoin('student_status','student_status.id','mentee.status')
                                        ->where('mentee.assigned_by',$parent_id)
                                        ->where('student_status.view_in_application',1)
                                        ->where(function ($q) use ($search) {
                                            $q->where('mentee.firstname', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('mentee.id','desc')
                                        ->paginate(10);

                    }

                }else{
                    if(!empty($read_type)){
                        $mentee =  DB::table('mentee')
                                    ->select('mentee.*')
                                    ->leftJoin('student_status','student_status.id','mentee.status')
                                    ->join('mentee_staff_chat_threads', 'mentee_staff_chat_threads.sender_id', '=', 'mentee.id')
                                    ->where('mentee_staff_chat_threads.receiver_id',Auth::user()->id)
                                    ->where('mentee_staff_chat_threads.from_where','mentee')
                                    ->where('mentee_staff_chat_threads.receiver_is_read',0)
                                    ->where('student_status.view_in_application',1)
                                    ->groupBy('mentee_staff_chat_threads.sender_id')
                                    ->paginate(10);
                    }else{
                        $mentee = DB::table('mentee')
                                        ->select('mentee.*')
                                        ->leftJoin('student_status','student_status.id','mentee.status')
                                        ->where('mentee.assigned_by',$parent_id)
                                        ->where('student_status.view_in_application',1)
                                        ->where(function ($q) use ($search) {
                                            $q->where('mentee.firstname', 'like', '%'.$search.'%');
                                        })
                                        ->orderBy('mentee.firstname', $sort1)
                                        ->paginate(10);

                    }

                }

                $mentee->appends(['type'=>$type,'search'=>$search,'sort1' => $sort1,'column_name1' => $column_name1]);

            }
        }else {
            if(Auth::user()->type == 1){
                $is_super = 1;
                $affiliate_id = 0;
            }else if(Auth::user()->type == 2){
                $affiliate_id = Auth::user()->id;
            }else if(Auth::user()->type == 3){
                $affiliate_id = Auth::user()->parent_id;
                if(Auth::user()->parent_id == 1){
                    $affiliate_id = 0;
                    $is_super = 1;
                }
            }

            if(!empty($affiliate_id)){
                $mentor = DB::table('mentor')->select('mentor.*')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.assigned_by',$affiliate_id)->orderBy('mentor.firstname','asc')->get()->toarray();
                $staff = DB::table('admins')->where('parent_id',$affiliate_id)->where('is_active', 1)->orderBy('name','asc')->get()->toarray();
                $mentee = DB::table('mentee')->select('mentee.*')->leftJoin('student_status', 'student_status.id','mentee.status')->where('mentee.assigned_by',$affiliate_id)->orderBy('mentee.firstname','asc')->get()->toarray();

            }

        }

        $affiliates = DB::table('admins')->select('id','name','email')->where('type', 2)->where('is_active',1)->orderBy('name','asc')->get()->toarray();

        // echo '<pre>'; print_r($affiliates); die;
        return view('admin.chat.chat_list',['affiliates' => $affiliates, 'mentor' => $mentor,'staff'=>$staff,'mentee'=>$mentee,'type'=>$type, 'view_chat' => $view_chat , 'read_type' => $read_type ,'search'=>$search , 'sort1' => $sort1, 'column_name1' => $column_name1, 'is_super' => $is_super ]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function get_mentor_staff_chatcode(Request $request)
    {
        try{
            $mentor_id = Crypt::decrypt($request->mentor_id);
            $staff_id = Crypt::decrypt($request->staff_id);

            $is_exist = DB::table('mentor_staff_chat_codes')->where('mentor_id',$mentor_id)->where('staff_id',$staff_id)->first();

            if(!empty($is_exist)){
                $code = $is_exist->code;
            }else{
                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char.rand(10000000,99999999);
                DB::table('mentor_staff_chat_codes')->insert(['code'=>$code,'mentor_id'=>$mentor_id,'staff_id'=>$staff_id]);
            }

            DB::table('mentor_staff_chat_threads')->where('from_where','mentor')->where('sender_id',$mentor_id)->where('receiver_id',$staff_id)->update(['receiver_is_read'=>1]);

            return redirect('/admin/chat/details_mentor/'.$code);



        }catch(\Exception $e){
            return redirect('/admin/chat?type=mentor');
        }
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function get_mentee_staff_chatcode(Request $request)
    {
        try{
            $mentee_id = Crypt::decrypt($request->mentee_id);
            $staff_id = Crypt::decrypt($request->staff_id);

            $is_exist = DB::table('mentee_staff_chat_codes')->where('mentee_id',$mentee_id)->where('staff_id',$staff_id)->first();

            if(!empty($is_exist)){
                $code = $is_exist->code;
            }else{
                $characters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
                shuffle($characters);
                $rand_char = '';
                foreach (array_rand($characters, 11) as $k) $rand_char .= $characters[$k];
                $code = $rand_char.rand(10000000,99999999);
                DB::table('mentee_staff_chat_codes')->insert(['code'=>$code,'mentee_id'=>$mentee_id,'staff_id'=>$staff_id]);
            }

            DB::table('mentee_staff_chat_threads')->where('from_where','mentee')->where('sender_id',$mentee_id)->where('receiver_id',$staff_id)->update(['receiver_is_read'=>1]);

            return redirect('/admin/chat/details_mentee/'.$code);



        }catch(\Exception $e){
            return redirect('/admin/chat?type=mentee');
        }
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function chat_details_mentor($chat_code)
    {
        $chat_details_arr = array();

        $mentor_staff_chat_codes = DB::table('mentor_staff_chat_codes AS mscc')->where('code',$chat_code)->first();


        if(empty($mentor_staff_chat_codes)){
            return redirect('/admin/chat');
        }

        $channel_sid = $mentor_staff_chat_codes->channel_sid;
        $chat_type = "mentor_staff";

        $chat_details_arr = DB::table('mentor_staff_chat_threads AS msct')->select('msct.*')->where('msct.chat_code',$chat_code)->orderBy('msct.id','asc')->get()->toarray();


        $mentor_id = $mentor_staff_chat_codes->mentor_id;
        $mentor_details = DB::table('mentor')->where('id',$mentor_id)->first();
        $mentor_firstname = !empty($mentor_details->firstname)?$mentor_details->firstname:'';
        $mentor_middlename = !empty($mentor_details->middlename)?$mentor_details->middlename:'';
        $mentor_lastname = !empty($mentor_details->lastname)?$mentor_details->lastname:'';
        $mentor_firebase_id = !empty($mentor_details->firebase_id)?$mentor_details->firebase_id:'';
        $mentor_device_type = !empty($mentor_details->device_type)?$mentor_details->device_type:'';
        $timezone = !empty(Auth::user()->timezone)?Auth::user()->timezone:'America/New_York';
        $receiver_type = "mentor";


        return view('admin.chat.chat_message_mentor')->with('chat_details_arr',$chat_details_arr)->with('staff_id',Auth::user()->id)->with('staff_name',Auth::user()->name)->with('mentor_id',$mentor_id)->with('mentor_firstname',$mentor_firstname)->with('mentor_middlename',$mentor_middlename)->with('mentor_lastname',$mentor_lastname)->with('mentor_firebase_id',$mentor_firebase_id)->with('chat_code',$chat_code)->with('mentor_device_type',$mentor_device_type)->with('timezone', $timezone)->with('channel_sid',$channel_sid)->with('chat_type',$chat_type)->with('receiver_type',$receiver_type);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function chat_details_mentee($chat_code)
    {
        $chat_details_arr = array();

        $mentee_staff_chat_codes = DB::table('mentee_staff_chat_codes AS mscc')->where('code',$chat_code)->first();


        if(empty($mentee_staff_chat_codes)){
            return redirect('/admin/chat');
        }

        $channel_sid = $mentee_staff_chat_codes->channel_sid;
        $chat_type = "mentee_staff";

        $chat_details_arr = DB::table('mentee_staff_chat_threads AS msct')->select('msct.*')->where('msct.chat_code',$chat_code)->orderBy('msct.id','asc')->get()->toarray();


        $mentee_id = $mentee_staff_chat_codes->mentee_id;
        $mentee_details = DB::table('mentee')->where('id',$mentee_id)->first();
        $mentee_firstname = !empty($mentee_details->firstname)?$mentee_details->firstname:'';
        $mentee_middlename = !empty($mentee_details->middlename)?$mentee_details->middlename:'';
        $mentee_lastname = !empty($mentee_details->lastname)?$mentee_details->lastname:'';
        $mentee_firebase_id = !empty($mentee_details->firebase_id)?$mentee_details->firebase_id:'';
        $mentee_device_type = !empty($mentee_details->device_type)?$mentee_details->device_type:'';
        $timezone = !empty(Auth::user()->timezone)?Auth::user()->timezone:'America/New_York';
        $receiver_type = "mentee";


        return view('admin.chat.chat_message_mentee')->with('chat_details_arr',$chat_details_arr)->with('staff_id',Auth::user()->id)->with('staff_name',Auth::user()->name)->with('mentee_id',$mentee_id)->with('mentee_firstname',$mentee_firstname)->with('mentee_middlename',$mentee_middlename)->with('mentee_lastname',$mentee_lastname)->with('mentee_firebase_id',$mentee_firebase_id)->with('chat_code',$chat_code)->with('mentee_device_type',$mentee_device_type)->with('timezone', $timezone)->with('channel_sid',$channel_sid)->with('chat_type',$chat_type)->with('receiver_type',$receiver_type);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function view_threads(Request $request)
    {
        $type = !empty($request->type)?$request->type:'';
        $view_chat = !empty($request->view_chat)?$request->view_chat:'';
        $mentor_id = !empty($request->mentor_id)?$request->mentor_id:'';
        $mentee_id = !empty($request->mentee_id)?$request->mentee_id:'';
        $staff_id = !empty($request->staff_id)?$request->staff_id:'';
        $data = array();

        if($type == 'mm'){
            $codes = DB::table('mentor_mentee_chat_codes')->where('mentor_id',$mentor_id)->where('mentee_id',$mentee_id)->first();

            $chat_code = !empty($codes)?$codes->code:'';

            // echo $chat_code; die;

            if(!empty($chat_code)){
                $data = DB::table('mentor_mentee_chat_threads')->where('chat_code',$chat_code)->orderBy('id','desc')->take(100)->get()->toarray();
            }

            // echo '<pre>'; print_r($data); die;



        }else if($type == 'ms'){
            $codes = DB::table('mentor_staff_chat_codes')->where('mentor_id',$mentor_id)->where('staff_id',$staff_id)->first();

            $chat_code = !empty($codes)?$codes->code:'';

            $data = DB::table('mentor_staff_chat_threads')->where('chat_code',$chat_code)->orderBy('id','desc')->take(100)->get()->toarray();

        }else if($type == 'menteestaff'){
            $codes = DB::table('mentee_staff_chat_codes')->where('mentee_id',$mentee_id)->where('staff_id',$staff_id)->first();

            $chat_code = !empty($codes)?$codes->code:'';

            $data = DB::table('mentee_staff_chat_threads')->where('chat_code',$chat_code)->orderBy('id','desc')->take(100)->get()->toarray();

        }

        // echo '<pre>'; print_r($data); die;

        return view('admin.chat.view-message',['data'=>$data,'type'=>$type,'mentor_id'=>$mentor_id,'mentee_id'=>$mentee_id,'staff_id'=>$staff_id,'view_chat'=>$view_chat]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function get_data_from_mentor(Request $request)
    {
        $data = array();
        $select_type = $request->select_type;
        $mentor_id = $request->mentor_id;

        $mentor_data = DB::table('mentor')->where('id',$mentor_id)->first();

        $mentor_assigned_by = $mentor_data->assigned_by;

        if($select_type == 'mentee'){
            $data = DB::table('mentee')
                            ->join('assign_mentee', 'assign_mentee.mentee_id', 'mentee.id')
                            ->select('mentee.id','mentee.timezone','mentee.firstname','mentee.middlename','mentee.lastname','mentee.email', 'mentee.current_living_details','mentee.image','mentee.cell_phone_number')
                            ->leftJoin('student_status', 'student_status.id','mentee.status')
                            ->where('assign_mentee.assigned_by',$mentor_id)
                            // ->where('assign_mentee.is_primary',1)
                            ->where('student_status.view_in_application',1)
                            ->orderBy('assign_mentee.created_date','desc')
                            ->get()->toarray();
        }else if($select_type == 'staff'){
            $data = DB::table('admins')->select('id','name','timezone','email','address','profile_pic')->where('parent_id', $mentor_assigned_by)->where('is_active',1)->get()->toarray();
        }

        return response()->json(['status'=>true, 'val'=>$data]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function get_data_from_affiliate(Request $request)
    {
        $data = array();

        $affiliate_id = $request->affiliate_id;

        $mentor = DB::table('mentor')->select('mentor.id','mentor.firstname','mentor.lastname','mentor.email')->leftJoin('mentor_status','mentor_status.id','mentor.is_active')->where('mentor_status.view_in_application', 1)->where('mentor.assigned_by',$affiliate_id)->orderBy('mentor.firstname','asc')->get()->toarray();
        $staff = DB::table('admins')->select('id','name','email')->where('parent_id',$affiliate_id)->where('is_active', 1)->orderBy('name','asc')->get()->toarray();
        $mentee = DB::table('mentee')->select('mentee.id','mentee.firstname','mentee.lastname','mentee.email')->leftJoin('student_status', 'student_status.id','mentee.status')->where('mentee.assigned_by',$affiliate_id)->orderBy('mentee.firstname','asc')->get()->toarray();



        return response()->json(['status'=>true, 'mentor'=>$mentor,'mentee'=>$mentee,'staff'=>$staff]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function keyword_notification_reviewed(Request $request)
    {

        /* Reviewed Lists*/


        $data = array();
        $sort = $request->sort;
        $column = $request->column;

        $orderByRaw = "";
        $sort_needed = 0;
        if(!empty($sort) && !empty($column)){
            $sort_needed = 1;

            if($sort == 'asc'){
                if($column == 'date'){
                    $orderByRaw .= "kcn.id ASC ";
                }
            }else if($sort == 'desc'){
                if($column == 'date'){
                    $orderByRaw .= "kcn.id DESC ";
                }
            }

        }else{
            $orderByRaw .= "kcn.id DESC ";
        }



        if(Auth::user()->type == 1){

            /* Super Admin*/

            DB::table('keyword_chat_notification')->update(['is_admin_read'=>1]);
            $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.is_super_admin_reviewed', 1)->orderByRaw($orderByRaw)->paginate(10);
        }else if(Auth::user()->type == 2){

            /* Affiliate */

            DB::table('keyword_chat_notification')->update(['is_affiliate_read'=>1]);
            $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.affiliate_id', Auth::user()->id)->where('kcn.is_affiliate_reviewed', 1)->orderByRaw($orderByRaw)->paginate(10);
        }else{
            if(Auth::user()->parent_id != 1){

                /* Lead Staff */

                DB::table('keyword_chat_notification')->update(['is_staff_read'=>1]);
                $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.staff_id', Auth::user()->id)->where('kcn.is_lead_staff_reviewed', 1)->orderByRaw($orderByRaw)->paginate(10);
            }else{

                /* Super Staff */

                DB::table('keyword_chat_notification')->update(['is_super_staff_read'=>1]);
                $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.super_staff_id', Auth::user()->id)->where('kcn.is_super_staff_reviewed', 1)->orderByRaw($orderByRaw)->paginate(10);
            }

        }

        if(empty($sort)){
            $sort = 'asc';
        }

        // echo '<pre>'; print_r($data); die;
        $data->appends(array('sort' => $sort))->links();
        $data->appends(array('column' => $column))->links();

        return view('admin.chat_notification.list',['data' => $data,  'sort' => $sort, 'column' => $column ,'sort_needed' => $sort_needed, 'type' => 'reviewed' ]);

    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function keyword_notification_unreviewed(Request $request)
    {

        $data = array();
        $sort = $request->sort;
        $column = $request->column;

        $orderByRaw = "";
        $sort_needed = 0;


        if(Auth::user()->type == 1){

            /* Super Admin*/

            if(!empty($sort) && !empty($column)){
                $sort_needed = 1;

                if($sort == 'asc'){
                    if($column == 'date'){
                        $orderByRaw .= "kcn.id ASC ";
                    }
                }else if($sort == 'desc'){
                    if($column == 'date'){
                        $orderByRaw .= "kcn.id DESC ";
                    }
                }

            }else{
                $orderByRaw .= "kcn.id DESC ";
                // $orderByRaw .= "kcn.is_super_admin_flagged DESC ";
            }

            DB::table('keyword_chat_notification')->update(['is_admin_read'=>1]);
            $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.is_super_admin_reviewed', 0)->orderByRaw($orderByRaw)->paginate(10);
        }else if(Auth::user()->type == 2){

            /* Affiliate */

            if(!empty($sort) && !empty($column)){
                $sort_needed = 1;

                if($sort == 'asc'){
                    if($column == 'date'){
                        $orderByRaw .= "kcn.id ASC ";
                    }
                }else if($sort == 'desc'){
                    if($column == 'date'){
                        $orderByRaw .= "kcn.id DESC ";
                    }
                }

            }else{
                $orderByRaw .= "kcn.id DESC ";
                // $orderByRaw .= "kcn.is_affiliate_flagged DESC ";
            }

            DB::table('keyword_chat_notification')->update(['is_affiliate_read'=>1]);
            $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.affiliate_id', Auth::user()->id)->where('kcn.is_affiliate_reviewed', 0)->orderByRaw($orderByRaw)->paginate(10);
        }else{
            if(Auth::user()->parent_id != 1){

                /* Lead Staff */

                if(!empty($sort) && !empty($column)){
                    $sort_needed = 1;

                    if($sort == 'asc'){
                        if($column == 'date'){
                            $orderByRaw .= "kcn.id ASC ";
                        }
                    }else if($sort == 'desc'){
                        if($column == 'date'){
                            $orderByRaw .= "kcn.id DESC ";
                        }
                    }

                }else{
                    $orderByRaw .= "kcn.is_lead_staff_flagged DESC ";
                }

                DB::table('keyword_chat_notification')->update(['is_staff_read'=>1]);
                $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.staff_id', Auth::user()->id)->where('kcn.is_lead_staff_reviewed', 0)->orderByRaw($orderByRaw)->paginate(10);
            }else{

                /* Super Staff */

                if(!empty($sort) && !empty($column)){
                    $sort_needed = 1;

                    if($sort == 'asc'){
                        if($column == 'date'){
                            $orderByRaw .= "kcn.id ASC ";
                        }
                    }else if($sort == 'desc'){
                        if($column == 'date'){
                            $orderByRaw .= "kcn.id DESC ";
                        }
                    }

                }else{
                    $orderByRaw .= "kcn.is_super_staff_flagged DESC ";
                }

                DB::table('keyword_chat_notification')->update(['is_super_staff_read'=>1]);
                $data = DB::table('keyword_chat_notification AS kcn')->select('kcn.*')->where('kcn.super_staff_id', Auth::user()->id)->where('kcn.is_super_staff_reviewed', 0)->orderByRaw($orderByRaw)->paginate(10);
            }

        }

        if(empty($sort)){
            $sort = 'asc';
        }

        $data->appends(array('sort' => $sort))->links();
        $data->appends(array('column' => $column))->links();

        return view('admin.chat_notification.list',['data' => $data,  'sort' => $sort, 'column' => $column ,'sort_needed' => $sort_needed,  'type' => 'unreviewed' ]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function make_reviewed_notification(Request $request)
    {
        $ids = $request->ids;
        $user_id = $request->user_id;
        $user_type = $request->user_type;

        // echo '<pre>'; print_r($ids);
        // echo '<pre>'; print_r($user_id);
        // echo '<pre>'; print_r($user_type);

        if($user_type == 3){
            $check_staff = DB::table('admins')->where('id',$user_id)->first();
            if($check_staff->parent_id == 1){

                DB::table('keyword_chat_notification')->whereIn('id',$ids)->update(['is_super_staff_reviewed'=>1]);

            }else{

                DB::table('keyword_chat_notification')->whereIn('id',$ids)->update(['is_lead_staff_reviewed'=>1]);

            }
        }else{
            if($user_type == 2){

                DB::table('keyword_chat_notification')->whereIn('id',$ids)->update(['is_affiliate_reviewed'=>1]);

            }else{
                DB::table('keyword_chat_notification')->whereIn('id',$ids)->update(['is_super_admin_reviewed'=>1]);
            }
        }

        return response()->json(['status' => true, 'message' => "Reviewed"]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    public function make_flagged_notification(Request $request)
    {
        $id = $request->id;
        $user_id = $request->user_id;
        $user_type = $request->user_type;

        if($user_type == 3){
            $check_staff = DB::table('admins')->where('id',$user_id)->first();
            if($check_staff->parent_id == 1){

                DB::table('keyword_chat_notification')->where('id',$id)->update(['is_super_staff_flagged'=>1]);

            }else{

                DB::table('keyword_chat_notification')->where('id',$id)->update(['is_lead_staff_flagged'=>1]);

            }
        }else{
            if($user_type == 2){

                DB::table('keyword_chat_notification')->where('id',$id)->update(['is_affiliate_flagged'=>1]);

            }else{
                DB::table('keyword_chat_notification')->where('id',$id)->update(['is_super_admin_flagged'=>1]);
            }
        }

        return response()->json(['status' => true, 'message' => "Flagged"]);
    }
/*+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/

}
