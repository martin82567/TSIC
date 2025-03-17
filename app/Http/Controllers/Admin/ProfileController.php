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

class ProfileController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    public function index()
    {  
        
        $id = Auth::user()->id;
        $user_details = DB::table('admins')->where('id',$id)->first();
        

        $contact_type_details = DB::table('contact_type')->where('is_active',1)->get();
        
        $document_type_details = DB::table('document_type')->where('is_active',1)->get();
        $services = DB::table('admin_services')->where('user_id',$id)->get()->toarray();
        $states = DB::table('states')->get()->toarray();
        $timezones = DB::table('timezones')->where('status',1)->get()->toarray();

        $admin_files_details = DB::table('admin_files')
                    ->join('document_type', 'document_type.id', '=', 'admin_files.document_type')
                    ->select('admin_files.*', 'document_type.name as document_type_name')
                    ->where('admin_id',$id)
                    ->get();

        $today = date('Y-m-d H:i:s');
        // echo $today; die;
        $system_messaging = DB::table(SYSTEM_MESSAGING.' AS msg')->select('msg.id','msg.message','msg.start_datetime','msg.end_datetime','appids.app_id');        
        $system_messaging = $system_messaging->leftJoin(SYSTEM_MESSAGING_APPIDS.' AS appids',function ($join) {
                $join->on('appids.message_id', '=' , 'msg.id') ;
                $join->where('appids.app_id', '=' , 3) ;
            });
        $system_messaging = $system_messaging->whereRaw("msg.start_datetime <= '".$today."' AND msg.end_datetime >= '".$today."'");
        $system_messaging = $system_messaging->where('msg.is_expired',0);
        $system_messaging = $system_messaging->where('msg.created_by',1);
        $system_messaging = $system_messaging->where('appids.app_id', 3);
        $system_messaging = $system_messaging->get()->toarray();


        return view('admin.profile.add',['user_details' => $user_details, 'admin_files_details' => $admin_files_details, 'contact_type_details' => $contact_type_details, 'document_type_details' => $document_type_details, 'states' => $states , 'services' => $services , 'timezones' => $timezones , 'system_messaging' => $system_messaging ]);
    }

    public function user_services($user_id)
    {
        // $user_id = $request->user_id;
        $services = DB::table('admin_services')->where('user_id',$user_id)->get()->toarray();
        if(!empty($services)){
            return $services;
        }else{
            return array();
        }
    }

    public function save(Request $request)
    {
        $id = Input::get('id');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'state' => 'required',
            // 'address' => 'required',
            // 'latitude' => 'required',
            // 'longitude' => 'required',
            // 'work_phone' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('admin/profile/')
                        ->withErrors($validator)
                        ->withInput();
        }
        
    

        $name = Input::get('name');        
        $password = Input::get('password');
        $map_type = Input::get('map_type');
        // $address = Input::get('address');
        // $latitude = Input::get('latitude');
        // $longitude = Input::get('longitude');
        // $jurisdiction_radius = Input::get('jurisdiction_radius');
        // if(empty($jurisdiction_radius)){
        //     $jurisdiction_radius = '';
        // }
                
        // $services = Input::get('services');

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
        
        // $zipcode = Input::get('zipcode');
        // if(empty($zipcode)){
        $zipcode = '';
        // }

        $profile_pic = Input::file('profile_pic');
        if(empty($profile_pic)){
            $profile_pic = '';
        }

        if(Auth::user()->type == 2){
            $timezone = Input::get('timezone');

            // echo $id; die;

            $staff_ids = array();
            $staffs = DB::table('admins')->where('parent_id',$id)->get()->toarray();
            if(!empty($staffs)){
                foreach($staffs as $sf){
                    $staff_ids[] = $sf->id;
                }
            }
            DB::table('admins')->whereIn('id',$staff_ids)->update(['timezone'=>$timezone]);          
            DB::table('mentee')->where('assigned_by',$id)->update(['timezone'=>$timezone]);

        }else if(Auth::user()->type == 3){
            $parent_id = Auth::user()->parent_id;
            $timezone_data = DB::table('admins')->where('id',$parent_id)->first();
            $timezone = $timezone_data->timezone;
        }else{
            $timezone = Auth::user()->timezone;
        }

        $characters = str_split('abcdefghijklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789_');
        shuffle($characters);
        $rand_char = '';
        foreach (array_rand($characters, 30) as $k) $rand_char .= $characters[$k];
        $system_email =  $rand_char.rand(10000,99999)."@seeandsend.info";

        if(!empty($password)){
            $password_original = Input::get('password');
            $password = bcrypt(Input::get('password'));

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

            DB::table('admins')
            ->where('id', $id)
            ->update([ 'timezone'=>$timezone, 'name' => $name, 'password' => $password, 'profile_pic'=>$image_name, 'map_type'=>'' , 'address' => '' ,'state' => '' , 'latitude' => '' , 'longitude' => '' ,'jurisdiction_radius' => '' ,'type' => Auth::user()->type,'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => '','country'=> '' ]);
            session(['success_message' => 'Data updated successfully']);

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

            /*===Admin Services===*/
            
            $ads = DB::table('admin_services')->where('user_id',$id)->get()->toarray();

            // echo '<pre>';print_r($ads);
            $s_ids = $admin_services = $diff = $intersect =  array();
            if(!empty($ads)){
                foreach($ads as $ser){
                    $s_ids[] = $ser->service_id;
                }

            }

            echo '<pre>'; print_r($s_ids);

            if(!empty($services)) { 
                foreach($services as $key => $value){ 
                    
                    $admin_services[] = $value;
                }                     
            }

            echo '<pre>'; print_r($admin_services);

            $intersect = array_intersect($admin_services,$s_ids);

            echo '<pre>'; print_r($intersect); 

            $diff = array_diff($admin_services,$s_ids) ;
            $diff1 = array_diff($s_ids,$admin_services) ;

            echo '<pre>'; print_r($diff); 
            echo '<pre>'; print_r($diff1); 

            // die;

            if(empty($intersect)){
                DB::table('admin_services')->where('user_id',$id)->whereIn('service_id',$s_ids)->delete();
            }else if(!empty($diff1)){
                DB::table('admin_services')->where('user_id',$id)->whereIn('service_id',$diff1)->delete();
            }

            if(!empty($diff)){
                foreach($diff as $k => $v){
                    DB::table('admin_services')->insert(['user_id' => $id, 'service_id' => $v]);
                }
            } 

            /*=========*/           

            DB::table('admins')
            ->where('id', $id)
            ->update([ 'timezone'=>$timezone, 'name' => $name, 'profile_pic' => $image_name, 'map_type'=> '' , 'address' => '' ,'state' => '' , 'latitude' => '' , 'longitude' => '' ,'jurisdiction_radius' => '' ,'type' => Auth::user()->type, 'cell_phone'=>$cell_phone , 'work_phone' => $work_phone ,'website' => $website , 'fax' => $fax , 'zipcode' => $zipcode ,'description' => $description ,'city' => '','country'=> '' ]);
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


        return redirect()->action('Admin\ProfileController@index');

    }

    public function remove_profile_pic($id,$profile_pic)
    {
        if(!empty($id) && !empty($profile_pic)){
            DB::table('admins')->where('id',$id)->update(['profile_pic'=>'']);
            // $file_path = public_path().'/uploads/agency_pic/'.$profile_pic;
            // File::delete($file_path);
            $filePath = 'agency_pic/'.$profile_pic;
            Storage::delete($filePath);
            session(['success_message' => 'Profile picture has been removed successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);     
            return redirect('/admin/profile');
        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            return redirect('/admin/profile');
        }
    }

    public function deletefile($dataid)
    {
        $id = Auth::user()->id;

        $admin_files_details = DB::table('admin_files')
                                ->where('added_by',$id)
                                ->where('id',$dataid)
                                ->first();

        if(!empty($admin_files_details)){
            DB::table('admin_files')->where('admin_id',$id)->where('id',$dataid)->delete();
            // $file_path = public_path().'/uploads/documents/'.$admin_files_details->file_name;
            // File::delete($file_path);
            $filePath = 'documents/'.$admin_files_details->file_name;
            Storage::delete($filePath);
            session(['success_message' => 'Document has been removed successfully']);
            session(['success_color' => 'success']);
            session(['success_icon' => 'check']);     
            return redirect('/admin/profile');
        }else{
            session(['success_message' => 'Oops!Something went wrong']);
            session(['success_color' => 'danger']);
            session(['success_icon' => 'close']);
            return redirect('/admin/profile');
        }
    }

    

}