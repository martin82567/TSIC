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
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
class ResourceController extends Controller
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
        $sort_needed = 0;
        if(!empty($request->sort)){
            $sort = $request->sort;
            $sort_needed = 1;
        }
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                        
                                })
                                ->where('resource.is_active',1)
                                ->orderBy('resource.id', 'desc')
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }else{
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                })
                                ->where('resource.is_active',1)
                                ->orderBy('resource.name', $sort)
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                        
                                })
                                ->where('resource.is_active',1)
                                ->where('resource.parent_id','=',Auth::user()->id)
                                ->orderBy('resource.id', 'desc')
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }else{
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                })
                                ->where('resource.is_active',1)
                                ->where('resource.parent_id','=',Auth::user()->id)
                                ->orderBy('resource.name', $sort)
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }
        }else{
            $admin_parent = DB::table('admins')->where('id',Auth::user()->parent_id)->first();
            if($admin_parent->type == 1){
                // echo Auth::user()->parent_id; echo '<pre>';
                // die('Admin');
                if(empty($sort)){
                    $user_arr = DB::table('resource')
                                    ->select('resource.*')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.is_active',1)
                                    ->orderBy('resource.id', 'desc')
                                    ->paginate(15);
                }else{
                    $user_arr = DB::table('resource')
                                    ->select('resource.*')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.is_active',1)
                                    ->orderBy('resource.name', $sort)
                                    ->paginate(15);
                }
            }else{
                
                // echo Auth::user()->parent_id; echo '<pre>';
                
                if(empty($sort)){
                    $user_arr = DB::table('resource')
                                    ->select('resource.*')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.parent_id', Auth::user()->parent_id)
                                    ->where('resource.is_active',1)
                                    ->orderBy('resource.id', 'desc')
                                    ->paginate(15);
                }else{
                    $user_arr = DB::table('resource')
                                    ->select('resource.*')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.parent_id', Auth::user()->parent_id)
                                    ->where('resource.is_active',1)
                                    ->orderBy('resource.name', $sort)
                                    ->paginate(15);


                }
            }



            
        }
        if(empty($sort)){
            $sort = 'asc';
        }

        // echo '<pre>'; print_r($user_arr); die;

        $user_arr->appends(array('search' => $query))->links();
        $user_arr->appends(array('sort' => $sort))->links();

        $type = 'active';
        
        return view('admin.resource.list',['user_arr' => $user_arr , 'sort' => $sort,'search' => $query,'sort_needed' => $sort_needed, 'type' => $type]);
    }

    public function inactive_resource(Request $request)
    {
        $query = $request->search;
        $sort_needed = 0;
        if(!empty($request->sort)){
            $sort = $request->sort;
            $sort_needed = 1;
        }
        if(Auth::user()->type == 1){
            if(empty($sort)){
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                        
                                })
                                ->where('resource.is_active',0)
                                ->orderBy('resource.id', 'desc')
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }else{
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                })
                                ->where('resource.is_active',0)
                                ->orderBy('resource.name', $sort)
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }
        }else if(Auth::user()->type == 2){
            if(empty($sort)){
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                        
                                })
                                ->where('resource.is_active',0)
                                ->where('resource.parent_id','=',Auth::user()->id)
                                ->orderBy('resource.id', 'desc')
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }else{
                $user_arr = DB::table('resource')
                                ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                ->where(function ($q) use ($query) {
                                    $q->where('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.name', 'like', '%'.$query.'%')
                                        ->orWhere('admins.name', 'like', '%'.$query.'%')
                                        ->orWhere('resource.email', 'like', '%'.$query.'%')
                                        ->orWhere('resource.address', 'like', '%'.$query.'%');
                                })
                                ->where('resource.is_active',0)
                                ->where('resource.parent_id','=',Auth::user()->id)
                                ->orderBy('resource.name', $sort)
                                ->select('resource.*', 'admins.name as admin_name')
                                ->paginate(15);
            }
        }else{
            $admin_parent = DB::table('admins')->where('id',Auth::user()->parent_id)->first();
            if($admin_parent->type == 1){
                if(empty($sort)){
                    $user_arr = DB::table('resource')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.is_active',0)
                                    ->orderBy('resource.id', 'desc')
                                    ->paginate(15);
                }else{
                    $user_arr = DB::table('resource')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.is_active',0)
                                    ->orderBy('resource.name', $sort)
                                    ->paginate(15);
                }
            }else{
                if(empty($sort)){
                    $user_arr = DB::table('resource')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.parent_id', Auth::user()->parent_id)
                                    ->where('resource.is_active',0)
                                    ->orderBy('resource.id', 'desc')
                                    ->paginate(15);
                }else{
                    $user_arr = DB::table('resource')
                                    ->join('admins', 'admins.id', '=', 'resource.parent_id')
                                    ->where(function ($q) use ($query) {
                                        $q->where('resource.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('admins.name', 'like', '%'.$query.'%')
                                            ->orWhere('resource.email', 'like', '%'.$query.'%')
                                            ->orWhere('resource.address', 'like', '%'.$query.'%');
                                    })
                                    ->where('resource.parent_id', Auth::user()->parent_id)
                                    ->where('resource.is_active',0)
                                    ->orderBy('resource.name', $sort)
                                    ->paginate(15);
                }
            }

            
        }
        if(empty($sort)){
            $sort = 'asc';
        }

        $user_arr->appends(array('search' => $query))->links();
        $user_arr->appends(array('sort' => $sort))->links();

        $type = 'inactive';
        
        return view('admin.resource.list',['user_arr' => $user_arr , 'sort' => $sort,'search' => $query,'sort_needed' => $sort_needed, 'type' => $type]);
    }

    public function add($id)
    {  
        try {
        $id = Crypt::decrypt($id);
        $user_details = DB::table('resource')->where('id',$id)->first();
        //$resource_files_details = DB::table('resource_files')->where('admin_id',$id)->get();

        $resource_category_details = DB::table('resource_category')->where('is_active',1)->get();

        $document_type_details = DB::table('document_type')->where('is_active',1)->get();

        $resource_files_details = DB::table('resource_files')
                    ->join('document_type', 'document_type.id', '=', 'resource_files.document_type')
                    ->select('resource_files.*', 'document_type.name as document_type_name')
                    ->where('resource',$id)
                    ->get();


        return view('admin.resource.add',['user_details' => $user_details, 'resource_files_details' => $resource_files_details, 'resource_category_details' => $resource_category_details, 'document_type_details' => $document_type_details]);
        }
        catch (\Exception $e) {
            return redirect()->route('admin.resource');
        }
    }

    public function save(Request $request)
    {
        $id = Input::get('id');
        if(!empty($id)){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:resource,email,'.Input::get('id'),
                
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'work_phone' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect('admin/resource/add/'.$id)
                            ->withErrors($validator)
                            ->withInput();
            }
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:resource,email,'.Input::get('id'),
                'password' => 'required',
                
                'address' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'work_phone' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect('admin/resource/add/0')
                            ->withErrors($validator)
                            ->withInput();
            }
        }
    

        $name = Input::get('name');        
        $email = Input::get('email');
        $password = Input::get('password');        
        $address = Input::get('address');
        $state = Input::get('state');
        if(empty($state)){
            $state = '';
        }
        $is_active = Input::get('is_active');
        if(empty($is_active)){ 
            $is_active = 0;
        }

        $latitude = Input::get('latitude');
        $longitude = Input::get('longitude');

        $resource_category = Input::get('resource_category');
        if(empty($resource_category)){
            $resource_category = 0;
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
        $profile_pic = Input::file('profile_pic');
        if(empty($profile_pic)){
            $profile_pic = '';
        }

        // print_r($profile_pic); die;


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

                    $storage_path = public_path() . '/uploads/resource_pic/';
                    $profile_pic->move($storage_path,$image_name);

                }else{
                    $image_name = '';
                }

                $parent_id = Auth::user()->id;

                if(Auth::user()->type == 3){
                    $parent_id = Auth::user()->parent_id;
                }
                
                $id = DB::table('resource')->insertGetId(['name' => $name, 'email' => $email,'password' => $password, 'profile_pic'=>$image_name, 'address' => $address ,'state' => $state , 'is_active'=> $is_active,'resource_category' => $resource_category,'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country,'parent_id' => $parent_id, 'latitude' => $latitude, 'longitude' => $longitude, 'added_by' => Auth::user()->id]); 
                session(['success_message' => 'Data added successfully']);
                
                $content = "<html><body><div>Hi, ".$name." thank you for registering to More To Life. <br/><table>
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

            }else{
                // print_r($profile_pic); die;


                if(!empty($profile_pic)){
                    $image_name = time().uniqid(rand());
                    $image_name = $image_name.'.'.$profile_pic->getClientOriginalExtension();

                    $storage_path = public_path() . '/uploads/resource_pic/';
                    $profile_pic->move($storage_path,$image_name);

                }else{
                    $resource_data = DB::table('resource')->where('id',$id)->first();
                    $image_name = $resource_data->profile_pic;
                }

                DB::table('resource')
                ->where('id', $id)
                ->update(['name' => $name, 'email' => $email,'password' => $password, 'profile_pic'=>$image_name, 'address' => $address ,'state' => $state , 'is_active'=> $is_active, 'resource_category' => $resource_category,'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country, 'latitude' => $latitude, 'longitude' => $longitude ]);
                session(['success_message' => 'Data updated successfully']);
            }
        }else{

            // echo $state;
            // echo $country;
            // echo $city;
            // die;

            // print_r($profile_pic); die;
            if(!empty($profile_pic)){
                $image_name = time().uniqid(rand());
                $image_name = $image_name.'.'.$profile_pic->getClientOriginalExtension();

                $storage_path = public_path() . '/uploads/resource_pic/';
                $profile_pic->move($storage_path,$image_name);

            }else{
                $resource_data = DB::table('resource')->where('id',$id)->first();
                $image_name = $resource_data->profile_pic;
            }
            

                DB::table('resource')
                ->where('id', $id)
                ->update(['name' => $name, 'email' => $email, 'profile_pic' => $image_name, 'address' => $address ,'state' => $state , 'is_active'=> $is_active, 'resource_category' => $resource_category,'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => $city,'country'=> $country, 'latitude' => $latitude, 'longitude' => $longitude ]);
                session(['success_message' => 'Data updated successfully']);
        }

        $document_type_arr = $request->document_type;

        if(!empty($request->images)) {    
            $i = 0;  
            foreach($request->images as $all_images){                        
                $img = $all_images;

                $image_name = time().uniqid(rand());
                $image_name = $image_name.'.'.$img->getClientOriginalExtension();

                $storage_path = public_path() . '/uploads/documents/';
                $img->move($storage_path,$image_name);
                // $img->move($storage_path,$img->getClientOriginalName());
                // $image  = $img->getClientOriginalName();

                $document_type = $document_type_arr[$i];

                DB::table('resource_files')->insertGetId(['resource' => $id, 'document_type'=> $document_type, 'file_name' => $image_name, 'added_by' => Auth::user()->id]); 
                $i++;
            }                     
        }


        return redirect()->action('Admin\ResourceController@index');

    }

    public function delete_resource_files($id,$resource,$file_name)
    {   
        if(!empty($id) && !empty($resource) && !empty($file_name)){            
            DB::table('resource_files')->where('id',$id)->where('resource',$resource)->delete();
            $file_path = public_path().'/uploads/documents/'.$file_name;
            File::delete($file_path);
            session(['success_message' => 'File deleted successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);            
            return redirect('/admin/resource/add/'.$resource);
        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            return redirect('/admin/resource/add/'.$resource);
        }

    }

    public function remove_resource_pic($id,$profile_pic)
    {
        if(!empty($id) && !empty($profile_pic)){
            DB::table('resource')->where('id',$id)->update(['profile_pic'=>'']);
            $file_path = public_path().'/uploads/resource_pic/'.$profile_pic;
            File::delete($file_path);
            session(['success_message' => 'Profile picture has been removed successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);     
            return redirect('/admin/resource/add/'.$id);
        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            return redirect('/admin/resource/add/'.$id);
        }
    }

    public function change_status_ajax(Request $request)
    {
        $id = !empty($request->id)?$request->id:'';

        if(!empty($id)){
            $resource = DB::table('resource')->where('id',$id)->first();
            if($resource->is_active == 1){
                DB::table('resource')->where('id',$id)->update(['is_active'=>0]);
                $message = 'Inactive';
            }else{
                DB::table('resource')->where('id',$id)->update(['is_active'=>1]);
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
            $resource = DB::table('resource')->where('id',$id)->first();
            if(!empty($resource->is_active)){
                DB::table('resource')->where('id',$id)->update(['is_active'=>0]);
                session(['success_message' => 'Status deactivated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }else{
                DB::table('resource')->where('id',$id)->update(['is_active'=>1]);
                session(['success_message' => 'Status activated successfully']);
                session(['success_color' => 'success']);
                session(['success_icon' => 'check']);
            }
            if($uri == 'resource'){
                return redirect('/admin/resource');
            }else{
                return redirect('/admin/inactiveresource');
            }
            
        }
    }





}