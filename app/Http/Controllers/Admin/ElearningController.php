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
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ElearningController extends Controller
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
        $search = !empty($request->search)?$request->search:'';
        $sort = !empty($request->sort)?$request->sort:'';
        
        $sort_needed = 0;
        if(!empty($sort)){            
            $sort_needed = 1;
        }

        $e_learning_arr = DB::table('e_learning')
                                    ->select('e_learning.id','e_learning.name','e_learning.is_active','e_learning.type','admins.name as creator_name','admins.type as created_type');
        $e_learning_arr = $e_learning_arr->selectRaw("(SELECT GROUP_CONCAT(admins.name) FROM e_learning_affiliates LEFT JOIN admins ON admins.id = e_learning_affiliates.affiliate_id WHERE e_learning_affiliates.e_learning_id = e_learning.id) AS affiliate_names");

        $e_learning_arr = $e_learning_arr->join('admins', 'admins.id', '=', 'e_learning.added_by');
                                   

        if(Auth::user()->type == 1){

        }else if(Auth::user()->type == 2){
            $e_learning_arr = $e_learning_arr->leftJoin('e_learning_affiliates','e_learning_affiliates.e_learning_id','e_learning.id');
            $e_learning_arr = $e_learning_arr->where('e_learning_affiliates.affiliate_id', Auth::user()->id);

        }else if(Auth::user()->type == 3){
            $e_learning_arr = $e_learning_arr->leftJoin('e_learning_affiliates','e_learning_affiliates.e_learning_id','e_learning.id');
            if(Auth::user()->parent_id != 1){
                $e_learning_arr = $e_learning_arr->where('e_learning_affiliates.affiliate_id', Auth::user()->parent_id);
            }            

        } 

        if(!empty($search)){
            $e_learning_arr = $e_learning_arr->where(function ($q) use ($search) {
                                        $q->where('e_learning.name', 'like', '%'.$search.'%');
                                    });
        }
        
        $e_learning_arr = $e_learning_arr->where('e_learning.is_active', 1);

        if(!empty($sort)){
            $e_learning_arr = $e_learning_arr->orderBy('e_learning.name', $sort);
        }else{
            $e_learning_arr = $e_learning_arr->orderBy('e_learning.id', 'desc');
        }
        
        $e_learning_arr = $e_learning_arr->paginate(15);         
        $e_learning_arr->appends(array('search' => $search))->links();
        $e_learning_arr->appends(array('sort' => $sort))->links();
        

        return view('admin.e_learning.list',['e_learning_arr' => $e_learning_arr,'sort' => $sort,'search' => $search,'sort_needed' => $sort_needed]);
    }

    public function inactive_elearning(Request $request)
    {
        $search = !empty($request->search)?$request->search:'';
        $sort = !empty($request->sort)?$request->sort:'';
        
        $sort_needed = 0;
        if(!empty($sort)){            
            $sort_needed = 1;
        }

        $e_learning_arr = DB::table('e_learning')
                                    ->select('e_learning.id','e_learning.name','e_learning.is_active','e_learning.type','admins.name as creator_name','admins.type as created_type');
        $e_learning_arr = $e_learning_arr->selectRaw("(SELECT GROUP_CONCAT(admins.name) FROM e_learning_affiliates LEFT JOIN admins ON admins.id = e_learning_affiliates.affiliate_id WHERE e_learning_affiliates.e_learning_id = e_learning.id) AS affiliate_names");

        $e_learning_arr = $e_learning_arr->join('admins', 'admins.id', '=', 'e_learning.added_by');
                                   

        if(Auth::user()->type == 1){

        }else if(Auth::user()->type == 2){
            $e_learning_arr = $e_learning_arr->leftJoin('e_learning_affiliates','e_learning_affiliates.e_learning_id','e_learning.id');
            $e_learning_arr = $e_learning_arr->where('e_learning_affiliates.affiliate_id', Auth::user()->id);

        }else if(Auth::user()->type == 3){
            $e_learning_arr = $e_learning_arr->leftJoin('e_learning_affiliates','e_learning_affiliates.e_learning_id','e_learning.id');
            if(Auth::user()->parent_id != 1){
                $e_learning_arr = $e_learning_arr->where('e_learning_affiliates.affiliate_id', Auth::user()->parent_id);
            }            

        } 

        if(!empty($search)){
            $e_learning_arr = $e_learning_arr->where(function ($q) use ($search) {
                                        $q->where('e_learning.name', 'like', '%'.$search.'%');
                                    });
        }
        
        $e_learning_arr = $e_learning_arr->where('e_learning.is_active', 0);

        if(!empty($sort)){
            $e_learning_arr = $e_learning_arr->orderBy('e_learning.name', $sort);
        }else{
            $e_learning_arr = $e_learning_arr->orderBy('e_learning.id', 'desc');
        }
        
        $e_learning_arr = $e_learning_arr->paginate(15);         
        $e_learning_arr->appends(array('search' => $search))->links();
        $e_learning_arr->appends(array('sort' => $sort))->links();
        

        return view('admin.e_learning.list',['e_learning_arr' => $e_learning_arr,'sort' => $sort,'search' => $search,'sort_needed' => $sort_needed]);
        
    }


    public function add($id)
    {  
        try {
            $id = Crypt::decrypt($id);

            $user_details = $e_learning_affiliates = $affiliate_ids = $e_learning_users = array();

            if(!empty($id)){
                $user_details = DB::table('e_learning')->where('id',$id)->first();

                $e_learning_affiliates = DB::table('e_learning_affiliates')->where('e_learning_id',$id)->get()->toarray();

                if(!empty($e_learning_affiliates)){
                    foreach ($e_learning_affiliates as $af) {
                        # code...
                        $affiliate_ids[] = $af->affiliate_id;
                    }
                }

                $e_learning_users = DB::table('e_learning_users')->where('e_learning_id',$id)->get()->toarray();


            }

            $user_types = array(array('id'=>1,'name'=>'Mentor App'),array('id'=>2,'name'=>'Mentee App')); 

            // echo '<pre>'; print_r($user_types); die;

            

            $affiliate_arr = DB::table('admins')->select('id','name','type','is_active')->where('type',2)->where('is_active',1)->get()->toarray();

            // echo '<pre>'; print_r($e_learning_users); die;


            
            return view('admin.e_learning.add',['user_details' => $user_details, 'affiliate_arr' => $affiliate_arr  , 'affiliate_ids' => $affiliate_ids , 'e_learning_users' => $e_learning_users , 'user_types' => $user_types ]);
        }
        catch (\DecryptException $e) {
            return redirect()->route('admin.e_learning');
        }
    }

    public function save(Request $request)
    {

        $id = Input::get('id');
        $messages = [
            'name.required'     => 'Name is required',
            'type.required'     => 'Type is required',
            'user_types.required'     => 'Choose at least one user',
            'image.max'         => 'File should be under 10MB',
        ];

        $validator = Validator::make($request->all(), [
                                        'name' => 'required',
                                        'type' => 'required',
                                        'user_types' => 'required',
                                        'image' => 'file|max:10240'
                                    ],$messages
        );
        
        
        if ($validator->fails()) {
            return redirect('admin/e_learning/add/'.Crypt::encrypt($id))
                        ->withErrors($validator)
                        ->withInput();
        }

        $name = Input::get('name');
        $affiliates = Input::get('affiliates');
        $user_types = Input::get('user_types');
        $is_active = Input::get('is_active');
        if(empty($is_active)){
            $is_active = 0;
        }
        $type = Input::get('type');
        $description = Input::get('description');
        if(empty($description)){
            $description = '';
        }

        $url = Input::get('url');
        if(empty($url)){
            $url = '';
        }



        $image = '';

        if(!empty($request->image)){
            $img = $request->image;
            if($img->getClientOriginalExtension() != 'png' && $img->getClientOriginalExtension() != 'jpg' && $img->getClientOriginalExtension() != 'jpeg' && $img->getClientOriginalExtension() != 'mp4' && $img->getClientOriginalExtension() != '3gp' && $img->getClientOriginalExtension() != 'pdf'){
                session(['success_message' => 'Please upload correct file type.']);
                return redirect('admin/e_learning/add/'.Crypt::encrypt($id));
            }
            if($type == 'image'){
                if($img->getClientOriginalExtension() != 'png' && $img->getClientOriginalExtension() != 'jpg' && $img->getClientOriginalExtension() != 'jpeg'){
                    session(['success_message' => 'Please upload Image file.']);
                    return redirect('admin/e_learning/add/'.Crypt::encrypt($id));
                }
            }else if($type == 'video'){
                if($img->getClientOriginalExtension() != 'mp4' && $img->getClientOriginalExtension() != '3gp'){
                    session(['success_message' => 'Please upload video file.']);
                    return redirect('admin/e_learning/add/'.Crypt::encrypt($id));
                } 
            }else if($type == 'pdf'){
                if($img->getClientOriginalExtension() != 'pdf'){
                    session(['success_message' => 'Please upload pdf file.']);
                    return redirect('admin/e_learning/add/'.Crypt::encrypt($id));
                } 
            }
            // $storage_path = public_path() . '/uploads/e_learning/';
            $image_name = time().uniqid(rand());
            $image = $image_name.'.'.$img->getClientOriginalExtension();
            $filePath = 'e_learning/' . $image;
            Storage::disk('s3')->put($filePath, file_get_contents($img), 'public');    
        }

        // die($url);


        if(!empty($url)){
            $type = 'url';
        }

        if(!empty(Auth::user()->timezone)){
            date_default_timezone_set(Auth::user()->timezone);
            $created_date = date('Y-m-d H:i:s');
        }else{
            $created_date = date('Y-m-d H:i:s');
        }
        
        if(empty(Input::get('affiliate_id'))){
            if(Auth::user()->type == 3){
                $affiliate_id = Auth::user()->parent_id;
            }else if(Auth::user()->type == 2){
                $affiliate_id = Auth::user()->id;
            }
        }else{
            $affiliate_id = Input::get('affiliate_id');
        } 
            

        if(empty($id)){
            
            $id = DB::table('e_learning')->insertGetId(['name' => $name,'description' => $description,'is_active' => $is_active,'type' => $type, 'file' => $image, 'url' => $url,  'added_by' => Auth::user()->id , 'created_date' => $created_date]); 



            if(!empty($affiliates)){
                foreach($affiliates as $a){
                    DB::table('e_learning_affiliates')->insert(['e_learning_id'=>$id,'affiliate_id'=>$a,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);

                }
            }else{
                DB::table('e_learning_affiliates')->insert(['e_learning_id'=>$id,'affiliate_id'=>$affiliate_id,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }

            if(!empty($user_types)){
                foreach($user_types as $u){
                    DB::table('e_learning_users')->insert(['e_learning_id'=>$id,'user_type'=>$u,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);

                }
            }else{
                DB::table('e_learning_users')->insert(['e_learning_id'=>$id,'user_type'=>$u,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }


            session(['success_message' => 'Data added successfully']);
        }else{
            if(!empty($image)){
                DB::table('e_learning')
                    ->where('id', $id)
                    ->update(['name' => $name,'description' => $description,'is_active' => $is_active,'type' => $type, 'file' => $image , 'url' => $url]);
            }else{
                DB::table('e_learning')
                    ->where('id', $id)
                    ->update(['name' => $name,'description' => $description,'is_active' => $is_active,'type' => $type, 'url' => $url]);
            }

            $affiliate_arr = DB::table('e_learning_affiliates')->where('e_learning_id',$id)->get()->toarray();
            $user_arr = DB::table('e_learning_users')->where('e_learning_id',$id)->get()->toarray();

            $exist_id_arr = $user_type_arr =  array();

            if(!empty($affiliates)){
                foreach($affiliates as $key => $value){
                    $user_resource = DB::table('e_learning_affiliates')->where('e_learning_id',$id)->where('affiliate_id',$value)->first();
                    if(empty($user_resource)){
                        DB::table('e_learning_affiliates')->insert(['e_learning_id'=>$id,'affiliate_id'=>$value,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s') ]);
                    }
                    $exist_id_arr[] = $value;
                }
            }
            if(!empty($user_types)){
                foreach($user_types as $key => $value){
                    $users = DB::table('e_learning_users')->where('e_learning_id',$id)->where('user_type',$value)->first();
                    if(empty($users)){
                        DB::table('e_learning_users')->insert(['e_learning_id'=>$id,'user_type'=>$value,'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s') ]);
                    }
                    $user_type_arr[] = $value;
                }
            }

            // echo '<pre>'; print_r($exist_id_arr);
            // echo '<pre>'; print_r($affiliate_arr);
            // die;
            $is_super = 0;         
            if(Auth::user()->type == 1){
                $is_super = 1;                
            }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
                $is_super = 1;                
            }
            if(!empty($is_super)){
                if(!empty($affiliate_arr)){
                    foreach($affiliate_arr as $a){
                        if(!in_array($a->affiliate_id,$exist_id_arr)){
                            DB::table('e_learning_affiliates')->where('e_learning_id',$id)->where('affiliate_id',$a->affiliate_id)->delete();
                        }
                    }
                }
            }
            
            if(!empty($user_arr)){
                foreach($user_arr as $a){
                    if(!in_array($a->user_type,$user_type_arr)){
                        DB::table('e_learning_users')->where('e_learning_id',$id)->where('user_type',$a->user_type)->delete();
                    }
                }
            }
            
            session(['success_message' => 'Data updated successfully']);
        }
        return redirect()->action('Admin\ElearningController@index');
    }

    public function changestatus(Request $request){
        $id = Input::get('id');
        $e_learning_data = DB::table('e_learning')->where('id',$id)->first();
        if(!empty($e_learning_data)){
            if(!empty($e_learning_data->is_active)){
                DB::table('e_learning')
                    ->where('id', $id)
                    ->update(['is_active' => 0]);
                echo "Inactive";
            }else{
                DB::table('e_learning')
                    ->where('id', $id)
                    ->update(['is_active' => 1]);
                echo  "Active";
            }
        }
    } 

    public function change_status($id,$uri)
    {
        if(!empty($id)){
            $e_learning = DB::table('e_learning')->where('id',$id)->first();
            if(!empty($e_learning->is_active)){
                DB::table('e_learning')->where('id',$id)->update(['is_active'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('e_learning')->where('id',$id)->update(['is_active'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'e_learning'){
                return redirect('/admin/e_learning');
            }else{
                return redirect('/admin/inactiveelearning');
            }
        }
    }


}