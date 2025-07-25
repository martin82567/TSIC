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

class ElearningController extends Controller
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

    public function search_learning(Request $request)
    {
        $user_id = $this->user_id;
        
        $mentee = DB::table('mentee')->select('*')->where('id',$user_id)->first();
        
        if(empty($mentee)){
            return response()->json(['status'=>true, 'message' => '', 'data' => array('e_learning_list' => array())]);
        }
        

        $search_keyword = $request->search_keyword;
        if(empty($search_keyword)){
            $search_keyword = '';
        }

        // $e_learning_arr = DB::table('e_learning')->where('name','like', '%'.$search_keyword.'%')->where('affiliate_id',$mentee->assigned_by)->where('is_active','1')->get();
        
        $e_learning_arr = DB::table('e_learning AS e')->select('e.id','e.name','e.description','e.type','e.file','e.url','a.affiliate_id')->leftJoin('e_learning_affiliates AS a','a.e_learning_id','e.id')->leftJoin('e_learning_users AS u','u.e_learning_id','e.id')->where('e.name','like', '%'.$search_keyword.'%')->where('e.is_active', 1)->where('a.affiliate_id', $mentee->assigned_by)->where('u.user_type', 2)->get();
        
        return response()->json(['status'=>true, 'message' => '', 'data' => array('e_learning_list' => $e_learning_arr)]);
    }

    public function e_learningdetails(Request $request,$id)
    {
        $user_id = $this->user_id;
        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give resource id", 'data' => array()]);
        }

        $e_learning_details = DB::table('e_learning')->select('*')->where('id',$id)->where('is_active','1')->first();
        return response()->json(['status'=>true, 'message' => '', 'data' => array('e_learning_details' => $e_learning_details)]);
    }

}