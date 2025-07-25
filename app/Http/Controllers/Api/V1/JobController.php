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
use DateTime;

class JobController extends Controller
{

    protected $user_id;

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

            $chk_user = DB::table('mentee')->select('*')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit(); 
            }

            mentee_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }        
    }

    public function getlist(Request $request)
    {
        $user_id = $this->user_id;

        $search_text = $request->search_text;

        $data_list = DB::table('job')
                    ->select('job.id','job.job_title','job.application_url','job.summary')                    
                    // ->select('job.id','job.job_title','job.company','job.location','job.status','job.application_url','job.summary','job.responsibilities','job.skills_qualification','job.benefits','job.is_upload_desc','job.upload_desc')                    
                    // ->where('job.status',1)
                    ->where(function ($q) use ($search_text) {
                        $q->where('job_title', 'like', '%'.$search_text.'%')
                              ->orWhere('application_url', 'like', '%'.$search_text.'%');
                    })
                    ->get();
        // echo '<pre>'; print_r($data_list->toarray()); die;

        if(!empty($data_list->toarray())){
            $data_list = $data_list->toarray();
            foreach($data_list as $data){              

                // $data->upload_desc = !empty($data->upload_desc)?url('/public/uploads/jobdescription/'.$data->upload_desc):'';
                                
            }
        }else{
            $data_list = array();
        }
        
        $data = array();
        $data['datalist'] = $data_list;


        return response()->json(['status'=>true, 'message' => '', 'data' => $data]);
    }


    public function getappliedjob(Request $request)
    {
        $user_id = $this->user_id;

        $search_text = $request->search_text;

        $data_list = DB::table('job')
                    ->join('job_experience', 'job_experience.job_id', '=', 'job.id')
                    ->Leftjoin('job_application', 'job_application.job_id', '=', 'job.id')
                    ->select('job.id','job.job_title','job.location','job.start_date','job.end_date','job_experience.yr_of_exp','job_experience.exp_type','job.status')
                    ->where('job_application.user_id',$user_id)
                    ->where('job.status',1)
                    ->where(function ($q) use ($search_text) {
                        $q->where('job_title', 'like', '%'.$search_text.'%')
                              ->orWhere('company', 'like', '%'.$search_text.'%')
                              ->orWhere('location', 'like', '%'.$search_text.'%')
                              ->orWhere('summary', 'like', '%'.$search_text.'%');
                    })
                    ->get();
        

        if(!empty($data_list->toarray())){
            $data_list = $data_list->toarray();
            foreach($data_list as $data){
                if($data->start_date != '0000-00-00'){
                    $start_date = DateTime::createFromFormat("Y-m-d" , $data->start_date);                
                    $data->start_date = $start_date->format('m-d-Y'); 
                }
                if($data->end_date != '0000-00-00'){
                    $end_date = DateTime::createFromFormat("Y-m-d" , $data->end_date);                
                    $data->end_date = $end_date->format('m-d-Y'); 
                }

            }
        }else{
            $data_list = array();
        }
        
        $data = array();
        $data['datalist'] = $data_list;


        return response()->json(['status'=>true, 'message' => '', 'data' => $data]);
    }



    public function getdetails(Request $request)
    {
        $user_id = $this->user_id;

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        $datadetails = DB::table('job')
                    ->select('*')
                    ->where('id',$id)
                    ->where('status',1)
                    ->first();
        if(!empty($datadetails)){
            $datadetails->upload_desc = !empty($datadetails->upload_desc)?url('/public/uploads/jobdescription/'.$datadetails->upload_desc):'';
            // $datadetails->job_education = DB::table('job_education')->where('job_id',$id)->first();
            // $datadetails->job_experience = DB::table('job_experience')->where('job_id',$id)->first();
            // $datadetails->job_preferred_language = DB::table('job_preferred_language')->where('job_id',$id)->first();
            // $datadetails->job_preferred_location = DB::table('job_preferred_location')->where('job_id',$id)->first();

            // if($datadetails->start_date != '0000-00-00'){
            //     $start_date = DateTime::createFromFormat("Y-m-d" , $datadetails->start_date);                
            //     $datadetails->start_date = $start_date->format('m-d-Y'); 
            // }
            // if($datadetails->end_date != '0000-00-00'){
            //     $end_date = DateTime::createFromFormat("Y-m-d" , $datadetails->end_date);                
            //     $datadetails->end_date = $end_date->format('m-d-Y'); 
            // }

            // $datadetails1 = DB::table('job_application')
            //                                 ->select('*')
            //                                 ->where('job_id',$id)
            //                                 ->where('user_id',$user_id)
            //                                 ->first();
            // if(!empty($datadetails1)){                
            //     $datadetails->is_applied = 1;
            // }else{
            //     $datadetails->is_applied = 0;
            // }

        }

        return response()->json(['status'=>true, 'message' => '', 'data' => array('datadetails' => $datadetails)]);
    }




    public function apply(Request $request)
    {
        $user_id = $this->user_id;        

        $name = $request->name;

        if(empty($name)){
            return response()->json(['status'=>false, 'message' => "Please give name", 'data' => array()]);
        }

        $email = $request->email;

        if(empty($email)){
            return response()->json(['status'=>false, 'message' => "Please give email", 'data' => array()]);
        }

        $experience = $request->experience;

        if(empty($experience)){
            return response()->json(['status'=>false, 'message' => "Please give experience", 'data' => array()]);
        }

        $cover_letter = $request->cover_letter;

        if(empty($cover_letter)){
            return response()->json(['status'=>false, 'message' => "Please give cover letter", 'data' => array()]);
        }

        $files = $request->resume;

        

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        $datadetails = DB::table('job')
                            ->select('*')
                            ->where('id',$id)
                            ->where('status',1)
                            ->first();
        if(!empty($datadetails)){
            if($datadetails->if_resume == "Yes"){
                if(empty($files)){
                    return response()->json(['status'=>false, 'message' => "Please give Resume", 'data' => array()]);
                }
            }
        }

        $image_name  = '';
        if(!empty($files)) {     
            $img = $files;

            $image_name = time().uniqid(rand());
            $image_name = $image_name.'.'.$img->getClientOriginalExtension();

            $storage_path = public_path() . '/uploads/jobapplication/';
            $img->move($storage_path,$image_name);

        }

        DB::table('job_application')->insert(['name' => $name, 'job_id' => $id, 'user_id' => $user_id, 'email' => $email, 'experience' => $experience , 'cover_letter' => $cover_letter, 'resume' => $image_name]);
    

        return response()->json(['status'=>true, 'message' => 'Application Submited Successfully.', 'data' => array()]);
    }

    public function filedeletegoaltask(Request $request,$goaltaskuserfiles_id)
    {
        $user_id = $this->user_id;

        if(empty($goaltaskuserfiles_id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        $datadetails = DB::table('goaltaskuserfiles')
                            ->select('*')
                            ->where('goaltaskuserfiles.added_by',$user_id)
                            ->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)
                            ->first();
        if(empty($datadetails)){
            return response()->json(['status'=>false, 'message' => 'This is not assign to you.', 'data' => array()]);
        }

        DB::table('goaltaskuserfiles')->where('goaltaskuserfiles.id',$goaltaskuserfiles_id)->delete();
        return response()->json(['status'=>true, 'message' => 'Files deleted Successfully.', 'data' => array()]);
    }

}