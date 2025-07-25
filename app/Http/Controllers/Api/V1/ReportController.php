<?php


namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Hash;
use Artisan;
use Config;
use Auth;
use DB;

use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
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

            mentee_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit(); 
        }
        
    }

/**++++++++++++++++++++++++++++++++++++++++++++++**/

    public function list()
    {
        $report_list = DB::table('report')->select('*')->where('mentee_id',$this->user_id)->orderBy('id','desc')->get();
        if(!empty($report_list->toarray())){
            $report_list = $report_list->toarray();
        }else{
            $report_list = array();
        }
        if(!empty($report_list)){
            foreach($report_list as $re){
                $re->created_date = date('m-d-Y H:i:s',strtotime($re->created_date));
            }
        }
        return response()->json(['status'=>true, 'message' => "report list", 'data' => $report_list ]);
    }

/**++++++++++++++++++++++++++++++++++++++++++++++**/   

    public function add(Request $request)
    {

        $name = !empty($request->name)?$request->name:'';
        $image = !empty($request->image)?$request->image:'';
        $id = !empty($request->id)?$request->id:'';
        
        if (empty($name)) {
            return response()->json(['status'=>false, 'message' => "Please give name", 'data' => array()]);
        }

        if (empty($image)) {
            return response()->json(['status'=>false, 'message' => "Please give image", 'data' => array()]);
        }

        $report = DB::table('report')
                    ->where('id',$id)
                    ->first();

        if(!empty($image)){
            $image_name = time().uniqid(rand());
            $image_name = $image_name.'.'.$image->getClientOriginalExtension();

            // $storage_path = public_path() . '/uploads/report/';
            // $image->move($storage_path,$image_name);

            $filePath = 'report/' . $image_name;
            Storage::disk('s3')->put($filePath, file_get_contents($image), 'public');
        }else{
            $image_name = $report->image;
        } 
        if(empty($id)){
            $id = DB::table('report')->insertGetId(['name' => $name,'image' => $image_name,'mentee_id' => $this->user_id]); 
            $message =  'Data added successfully';
        }else{
            $id = DB::table('report')->where('id', $id)->update(['name'=>$name,'image'=>$image_name]);
            $message =  'Data updated successfully';
        }
        
        return response()->json(['status'=>true, 'message' => $message, 'data' => array()]);       

        
    }

    
}