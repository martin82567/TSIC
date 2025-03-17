<?php


namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Hash;
use Artisan;
use Config;
use Auth;
use DB;

class ResourceController extends Controller
{

    public function __construct(Request $request)
    {
        $header = $request->header('Authorizations');        
        if(empty($header)){
            response()->json(array('status'=>false,'message'=>"Authentication failed"))->send();
            exit();             
        }        
        if(substr($header, 0, 7) != "Bearer "){
            response()->json(array('status'=>false,'message'=>"Please add prefix and a space before the token"))->send(); 
            exit();            
        }
        try{
            $this->header_str = substr($header,7,strlen($header));
            $this->user_id = Crypt::decryptString($this->header_str);   

            $chk_user = DB::table('mentee')->where('id',$this->user_id)->first();

            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit(); 
            }

            if(!empty($chk_user->is_logged_out)){
                response()->json(array('status'=>false,'message'=>"Logged Out"))->send();
                exit(); 
            }

            mentee_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }
        
    }

    public function searchresource(Request $request)
    {
        $user_id = $this->user_id;

        $user_details = DB::table('mentee')->where('id',$user_id)->first();
        $assigned_by = $user_details->assigned_by;

        

        $search_keyword = $request->search_keyword;
        

        $resource_category_arr = DB::table('resource_category')->where('name','like', '%'.$search_keyword.'%')->where('is_active','1')->get();

        // echo '<pre>'; print_r($resource_category_arr); 
        $category_id_arr = array();
        if(!empty($resource_category_arr)){
            foreach($resource_category_arr as $res_cat){
                $category_id_arr[] = $res_cat->id;
            }
        }

        // echo '<pre>'; print_r($category_id_arr); 

        $victim_details = DB::table('assign_victim')->select('admin_id')->where('victim_id',$user_id)->where('status',1)->first();

        

        $post_data_arr = array();
        $post_data_id = array();

        if(!empty($assigned_by)){
            if(!empty($search_keyword)){
                if(!empty($category_id_arr)){
                    $category_id_arr = implode(',',$category_id_arr);
                    $data_arr = DB::select( DB::raw("SELECT resource.id,resource.name,resource.firstname,resource.middlename,resource.lastname,resource.email,resource.profile_pic AS pic_url,resource.address,resource.state,resource.cell_phone,resource.work_phone,resource.website,resource.description,resource.latitude,resource.longitude,resource_category.name as category FROM resource JOIN resource_category ON resource_category.id = resource.resource_category  WHERE resource.is_active = 1 AND resource.resource_category IN (".$category_id_arr.") AND (parent_id = ".$assigned_by." OR parent_id = 1) ORDER BY resource.name ASC"));

                    if(!empty($data_arr)){
                        foreach($data_arr as $data){
                            if(!empty($data->pic_url)){
                                $data->pic_url = url('public').'/uploads/resource_pic/'.$data->pic_url;
                            }
                            if(!in_array($data->id, $post_data_id)){
                                $post_data_arr[] = $data;
                            }else{
                                $post_data_id[] = $data->id;
                            }
                            $resource_files_list = DB::table('resource_files')
                                                        ->join('document_type', 'document_type.id', '=', 'resource_files.document_type')
                                                        ->select('resource_files.file_name as file_path', 'document_type.name as file_name')
                                                        ->where('resource_files.resource',$data->id)
                                                        ->get();
                            if(!empty($resource_files_list)){
                                foreach($resource_files_list as $resource_file){
                                    $resource_file->file_path = url('public').'/uploads/resource_pic/'.$resource_file->file_path;
                                }
                                $data->resource_files_list = $resource_files_list;
                            }else{
                                $data->resource_files_list = array();
                            }
                        }
                    }
                }

                // echo '<pre>'; print_r($data_arr);

                // die('Hi');    
                // echo $assigned_by; die;               

                $data_arr = DB::select( DB::raw("SELECT resource.id,resource.name,resource.firstname,resource.middlename,resource.lastname,resource.email,resource.profile_pic AS pic_url,resource.address,resource.state,resource.cell_phone,resource.work_phone,resource.website,resource.description,resource.latitude,resource.longitude,resource_category.name as category FROM resource JOIN resource_category ON resource_category.id = resource.resource_category  WHERE resource.is_active = 1 AND resource.name LIKE '%".$search_keyword."%'  AND (parent_id = ".$assigned_by." OR parent_id = 1) ORDER BY resource.name ASC"));

                // echo '<pre>'; print_r($data_arr); die;

                if(!empty($data_arr)){
                    foreach($data_arr as $data){
                        if(!empty($data->pic_url)){
                            $data->pic_url = url('public').'/uploads/resource_pic/'.$data->pic_url;
                        }
                        if(!in_array($data->id, $post_data_id)){
                            $post_data_arr[] = $data;
                        }else{
                            $post_data_id[] = $data->id;
                        }

                        $resource_files_list = DB::table('resource_files')
                                                        ->join('document_type', 'document_type.id', '=', 'resource_files.document_type')
                                                        ->select('resource_files.file_name as file_path', 'document_type.name as file_name')
                                                        ->where('resource_files.resource',$data->id)
                                                        ->get();
                        if(!empty($resource_files_list)){
                            foreach($resource_files_list as $resource_file){
                                $resource_file->file_path = url('public').'/uploads/resource_pic/'.$resource_file->file_path;
                            }
                            $data->resource_files_list = $resource_files_list;
                        }else{
                            $data->resource_files_list = array();
                        }
                    }
                }
            }else{
                $data_arr = DB::select( DB::raw("SELECT resource.id,resource.name,resource.firstname,resource.middlename,resource.lastname,resource.email,resource.profile_pic AS pic_url,resource.address,resource.state,resource.cell_phone,resource.work_phone,resource.website,resource.description,resource.latitude,resource.longitude,resource_category.name as category FROM resource JOIN resource_category ON resource_category.id = resource.resource_category  WHERE resource.is_active = 1 AND (parent_id = ".$assigned_by." OR parent_id = 1) ORDER BY resource.name ASC"));

                if(!empty($data_arr)){
                    foreach($data_arr as $data){
                        if(!empty($data->pic_url)){
                            $data->pic_url = url('public').'/uploads/resource_pic/'.$data->pic_url;
                        }
                        $resource_files_list = DB::table('resource_files')
                                                            ->join('document_type', 'document_type.id', '=', 'resource_files.document_type')
                                                            ->select('resource_files.file_name as file_path', 'document_type.name as file_name')
                                                            ->where('resource_files.resource',$data->id)
                                                            ->get();
                        if(!empty($resource_files_list)){
                            foreach($resource_files_list as $resource_file){
                                $resource_file->file_path = url('public').'/uploads/resource_pic/'.$resource_file->file_path;
                            }
                            $data->resource_files_list = $resource_files_list;
                        }else{
                            $data->resource_files_list = array();
                        }
                        $post_data_arr[] = $data;
                    }
                }
            }

            

            return response()->json(['status'=>true, 'message' => '', 'data' => array('resource' => $post_data_arr)]);
        }else{
            return response()->json(['status'=>true, 'message' => 'No data found', 'data' => array('resource' => array() )]);
        }
    }

    

    public function resourcedetails(Request $request,$id)
    {        
        $user_id = $this->user_id;
        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give resource id", 'data' => array()]);
        }

        $resource_details = DB::table('resource')
                                    ->join('resource_category', 'resource_category.id', '=', 'resource.resource_category')
                                    ->select('resource.id','resource.name','resource.firstname','resource.middlename','resource.lastname','resource.email','resource.address','resource.state','resource.cell_phone','resource.work_phone','resource.website','resource.description','resource.profile_pic as pic_url','resource_category.name as category')
                                    ->where('resource.id',$id)
                                    ->where('resource.is_active','1')
                                    ->first();
        if(!empty($resource_details->pic_url)){
            $resource_details->pic_url = url('public').'/uploads/resource_pic/'.$resource_details->pic_url;
        }         
        
        $resource_files_list = DB::table('resource_files')
                                                ->join('document_type', 'document_type.id', '=', 'resource_files.document_type')
                                                ->select('resource_files.file_name as file_path', 'document_type.name as file_name')
                                                ->where('resource_files.resource',$id)
                                                ->get();
        if(!empty($resource_files_list)){
            foreach($resource_files_list as $resource_file){
                $resource_file->file_path = url('public').'/uploads/resource_pic/'.$resource_file->file_path;
            }
            $resource_details->resource_files_list = $resource_files_list;
        }else{
            $resource_details->resource_files_list = array();
        }

        return response()->json(['status'=>true, 'message' => '', 'data' => array('resource_details' => $resource_details)]);
    }

}