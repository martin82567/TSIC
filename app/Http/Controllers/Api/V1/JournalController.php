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

class JournalController extends Controller
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

    public function my_journal(Request $request)
    {
        
        $user_id = $this->user_id;

        $my_journal = array();

        $my_journal = DB::table('journal')->where('victim_id',$user_id)->orderBy('updated_at','desc')->get()->toarray();

        if(!empty($my_journal)){
            foreach($my_journal as $j){
                $j->created_at = date('m-d-Y H:i:s', strtotime($j->created_at));
                $j->updated_at = date('m-d-Y H:i:s', strtotime($j->updated_at));
            }
        }

        return response()->json(['status'=>true, 'message' => '', 'data' => $my_journal]);
    }


    public function journal_details(Request $request)
    {
        
        $user_id = $this->user_id;

        $id = $request->id;

        if(empty($id)){
            return response()->json(['status'=>false, 'message' => "Please give id", 'data' => array()]);
        }

        $journal = DB::table('journal')->where('id',$id)->first();

        if(!empty($journal)){
            
            $journal->created_at = date('m-d-Y H:i:s', strtotime($journal->created_at));
            $journal->updated_at = date('m-d-Y H:i:s', strtotime($journal->updated_at));
            
        }

        return response()->json(['status'=>true, 'message' => '', 'data' => $journal]);
    }


    public function add_journal(Request $request)
    {
        
        $user_id = $this->user_id;

        $id = !empty($request->id)?$request->id:'';
        $title = !empty($request->title)?$request->title:'';
        $description = !empty($request->description)?$request->description:'';

        if(empty($title))
            return response()->json(['status'=>false, 'message'=>"Title is required", 'data' => array()]);

        if(empty($id)){
            $id = DB::table('journal')->insertGetId(['victim_id'=>$user_id , 'title'=>$title , 'description'=>$description , 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            return response()->json(['status'=>true, 'message'=>"Journal created successfully", 'data' => array() ]);
        }else{
            DB::table('journal')->where('id',$id)->update(['victim_id'=>$user_id , 'title'=>$title , 'description'=>$description , 'updated_at' => date('Y-m-d H:i:s')]);

            return response()->json(['status'=>true, 'message'=>"Journal updated successfully", 'data' => array()]);
        }



    }


    

}