<?php


//  Bello Tips ...

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Hash;
use DB;
use App\appuser;
use Route;
use App\address;
use App\broadcast;
use App\tips;
use App\tips_attachments;
use Config;

use DateTimeZone;

// require_once('Twilio/autoload.php');
// use Twilio\Rest\Client;

class SubmittedTipsController extends Controller
{
    public function __construct()
    {

    }

//++++++++++++++++++++++++++++++++++++++++++++++// 
//++++++++++++++++++++++++++++++++++++++++++++++// 
//++++++++++++++++++++++++++++++++++++++++++++++// 
//++++++++++++++++++++++++++++++++++++++++++++++// 
//++++++++++++++++++++++++++++++++++++++++++++++// 
//++++++++++++++++++++++++++++++++++++++++++++++// 


    public function create_new_post(Request $request)
    {  
        $headers = apache_request_headers();
        if(empty($headers['Authorizations'])){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }

        $header = $headers['Authorizations'];
        $header = substr($header,7,strlen($header));

        if(empty($header)){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }
        $id = Crypt::decryptString($header);

        // echo $id; die;

                
        $input = $request->all();
        $user_id = $id;
        $appuser_details = DB::table('victims')->where('id',$user_id)->first();

        // $voice =  url('/public/uploads/ivr/Seeandsend.mp3'); 

        if($appuser_details === null)
            return response()->json(['status'=>false, 'message'=>"User not found"]);

        $data_arr = array();
        if(!empty($user_id)){
            $tips = new tips;            
            $tips->tips_information = !empty($input['tips_information'])?$input['tips_information']:'';
            if(empty($tips->tips_information)){
                return response()->json(['status'=>false, 'message' => "Please give Information For Tips"]);
            }            
            $tips->name = !empty($input['name'])?$input['name']:'';                        
            $tips->user_id = $user_id;            
            $tips->agency_id = !empty($input['agency_id'])?$input['agency_id']:'';

            if(empty($tips->agency_id))
                return response()->json(['status'=>false, 'message'=>"Please choose an agency"]);

            $get_agency_details = DB::table('admins')->select('admins.*')->where('admins.id','=',$tips->agency_id)->get();

            $agency_name = !empty($get_agency_details[0]->name)?$get_agency_details[0]->name:'';

            if(!empty($tips->agency_id)){                
                
                $tips->latitude = $get_agency_details[0]->latitude;
                $tips->longitude = $get_agency_details[0]->longitude;
                              
                $to_email = $get_agency_details[0]->email;                                  
                $content = "{$agency_name} has a new pending tip.<br/>The tip topic is {$tips->name}.<br/>Please login to view it  app.seeandsend.info.<br/>If you do not have access, please contact us immediately at info@seeandsend.info";   
                $subject = 'Pending Tip';                  
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "From: {$appuser_details->email}";

                $headers_sms  = 'MIME-Version: 1.0' . "\r\n";
                $headers_sms .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers_sms .= "From: From: no_reply@seeandsend.info";

            }else{
                $tips->latitude = '';
                $tips->longitude = '';
            }
            

            $tips->tips_type = $input['tips_type'];

            
            $tips->state = !empty($input['state'])?$input['state']:'';
            $tips->address = !empty($input['address'])?$input['address']:'';
            $tips->known_person = !empty($input['known_person'])?$input['known_person']:'';
            $tips->hear_about = !empty($input['hear_about'])?$input['hear_about']:'';
            $tips->is_anonymous = !empty($input['is_anonymous'])?$input['is_anonymous']:0;
            
            $tips->created_date = date('Y-m-d H:i:s');
            $tips->updated_date = '';            
            $tips->tips_status = '1';            
            $tips->save();
            $tips_id = $tips->id;
            
            if(!empty($tips_id)){               
                 if(!empty($request->images)) {                    
                    //$file = $request->file('uploads') ;
                    foreach($request->images as $all_images){                        
                        $img = $all_images;
                        $storage_path = public_path() . '/uploads/tips/';
                        $img->move($storage_path,$img->getClientOriginalName());
                        $image  = $img->getClientOriginalName();
                        DB::table('submitted_tips_attachments')->insert(['tips_id' => $tips_id, 'data' => $image, 'type' => 'image']);                        
                    }                     
                }

                if(!empty($request->video)) { 
                    $vid = $request->video;
                    $storage_path = public_path() . '/uploads/tips/';
                    $vid->move($storage_path,$vid->getClientOriginalName());
                    $video  = $vid->getClientOriginalName();

                    DB::table('submitted_tips_attachments')->insert(['tips_id' => $tips_id, 'data' => $video, 'type' => 'video']);                        
                                         
                }

                if(!empty($request->others)) {  
                    foreach($request->others as $all_others){      
                        $other = $all_others;
                        $storage_path = public_path() . '/uploads/tips/';
                        $other->move($storage_path,$other->getClientOriginalName());
                        $others  = $other->getClientOriginalName();

                        DB::table('submitted_tips_attachments')->insert(['tips_id' => $tips_id, 'data' => $others, 'type' => 'others']); 
                    }                       
                                         
                }


                               
            }

            return response()->json(['status'=>true,'token' => "Bearer ".$header, 'tips_id' => $tips_id,  'message' => 'Post added successfully']);
        }else{
            return response()->json(['status'=>false, 'message' => "Please give inputs to save"]);
        }

    }

//++++++++++++++++++++++++++++++++++++++++++++++// 

    public function my_tips_new(Request $request)
    {        
        $headers = apache_request_headers();
        if(empty($headers['Authorizations'])){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }

        $header = $headers['Authorizations'];
        $header = substr($header,7,strlen($header));

        if(empty($header)){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }
        $user_id = Crypt::decryptString($header);

        $tips_arr = array();  
       

        $tips_data = DB::table("submitted_tips")
                            ->join("tips_status", "tips_status.id","submitted_tips.tips_status")
                            ->select("submitted_tips.*","tips_status.status_name")
                            ->where("submitted_tips.user_id",$user_id)
                            ->where("submitted_tips.tips_status","<>","4")
                            ->orderBy('submitted_tips.id', 'desc')
                            ->get(); 


        if(!empty($tips_data)){
            $i = 0;
            foreach($tips_data as $tips){

                // $tips_chat = DB::table('tips_message')->where('from_where','web')->where('receiver_id',$user_id)->where('tips_id',$tips->id)->where('app_is_read','0')->get()->toarray(); 

                // $tips->is_chat_unread = !empty($tips_chat)?count($tips_chat):0;


                $tips_arr[$i] = $tips; 
                    
                $submitted_tips_suspects = DB::table("submitted_tips_suspects")
                            ->select("*")->where("tips_id",$tips->id)->get();
                if(!empty($submitted_tips_suspects->toarray())){
                    $tips_arr[$i]->suspects = $submitted_tips_suspects->toarray();
                }

                $submitted_tips_vehicles = DB::table("submitted_tips_vehicles")
                            ->select("*")->where("tips_id",$tips->id)->get();
                if(!empty($submitted_tips_vehicles->toarray())){
                    $tips_arr[$i]->vehicles = $submitted_tips_vehicles->toarray();
                }

                $tips_attachments = DB::table("submitted_tips_attachments")
                            ->select("id","tips_id","type","data as image")->where("tips_id",$tips->id)->get();
                
                if(!empty($tips_attachments->toarray())){
                    $tips_arr[$i]->tips_attachments = $tips_attachments->toarray();
                }
                $i++;  
                // unset($tips->tips_status);              
            }
        } 

        //return response()->json(['status'=>true,'token' => "Bearer ".$header, 'result_count' => $tips_count, 'total_page' => $total_page, 'per_page' =>$per_page ,'current_page' => $current_page,  'details' => $tips_arr ]);

        return response()->json(['status'=>true,'token' => "Bearer ".$header, 'details' => $tips_arr ]);

    } 

//++++++++++++++++++++++++++++++++++++++++++++++// 

    public function multi_suspect_vehicle_new(Request $request)
    {        
        $headers = apache_request_headers();
        if(empty($headers['Authorizations'])){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }

        $header = $headers['Authorizations'];
        $header = substr($header,7,strlen($header));

        if(empty($header)){
            return response()->json(['status'=>false, 'message' => "Please give token", 'data' => array()]);
        }
        $user_id = Crypt::decryptString($header);

        $data_arr = array();
        $input = $request->all();        
        if(!empty($user_id)){
            //$tips = new tips;
            

            $tips_id = $input['tips_id'];
            if(!empty($tips_id)){

                if(!empty($request->arrPerson)){
                    foreach($request->arrPerson as $suspect){
                        //print_r($suspect);
                        if(!empty($suspect['suspect_name']) || !empty($suspect['suspect_phone']) || !empty($suspect['suspect_person_type']) || !empty($suspect['suspect_sex']) || !empty($suspect['suspect_height']) || !empty($suspect['suspect_weight']) || !empty($suspect['suspect_race']) || !empty($suspect['suspect_eye_color']) || !empty($suspect['suspect_hair_color']) || !empty($suspect['suspect_age']) || !empty($suspect['suspect_tattoos_marks']) || !empty($suspect['suspect_other_info'])){

                        $suspect['suspect_name'] = !empty($suspect['suspect_name'])?$suspect['suspect_name']:'';
                        $suspect['suspect_phone'] = !empty($suspect['suspect_phone'])?$suspect['suspect_phone']:'';
                        $suspect['suspect_person_type'] = !empty($suspect['suspect_person_type'])?$suspect['suspect_person_type']:'';
                        $suspect['suspect_sex'] = !empty($suspect['suspect_sex'])?$suspect['suspect_sex']:'';
                        $suspect['suspect_height'] = !empty($suspect['suspect_height'])?$suspect['suspect_height']:'';
                        $suspect['suspect_weight'] = !empty($suspect['suspect_weight'])?$suspect['suspect_weight']:'';
                        $suspect['suspect_race'] = !empty($suspect['suspect_race'])?$suspect['suspect_race']:'';
                        $suspect['suspect_eye_color'] = !empty($suspect['suspect_eye_color'])?$suspect['suspect_eye_color']:'';
                        $suspect['suspect_hair_color'] = !empty($suspect['suspect_hair_color'])?$suspect['suspect_hair_color']:'';
                        $suspect['suspect_age'] = !empty($suspect['suspect_age'])?$suspect['suspect_age']:'';
                        $suspect['suspect_tattoos_marks'] = !empty($suspect['suspect_tattoos_marks'])?$suspect['suspect_tattoos_marks']:'';
                        $suspect['suspect_other_info'] = !empty($suspect['suspect_other_info'])?$suspect['suspect_other_info']:'';
                        

                            DB::table('submitted_tips_suspects')->insert(
                            ['tips_id' => $tips_id, 'suspect_name' => $suspect['suspect_name'], 'suspect_phone' => $suspect['suspect_phone'], 'suspect_person_type' => $suspect['suspect_person_type'], 'suspect_sex' => $suspect['suspect_sex'], 'suspect_height' => $suspect['suspect_height'], 'suspect_weight' => $suspect['suspect_weight'], 'suspect_race' => $suspect['suspect_race'], 'suspect_eye_color' => $suspect['suspect_eye_color'] , 'suspect_hair_color' => $suspect['suspect_hair_color'], 'suspect_age' => $suspect['suspect_age'], 'suspect_tattoos_marks' => $suspect['suspect_tattoos_marks'], 'suspect_other_info' => $suspect['suspect_other_info'] ]
                            );
                        }                        

                    }
                }

                if(!empty($request->arrVehicle)){
                    foreach($request->arrVehicle as $vehicle){
                        //echo '<pre>'; print_r($vehicle);
                        if( !empty($vehicle['vehicle_year']) || !empty($vehicle['vehicle_make']) || !empty($vehicle['vehicle_model']) || !empty($vehicle['vehicle_type']) || !empty($vehicle['vehicle_color']) || !empty($vehicle['vehicle_tag']) || !empty($vehicle['vehicle_state']) || !empty($vehicle['vehicle_description']) ){

                            $vehicle['vehicle_year'] = !empty($vehicle['vehicle_year'])?$vehicle['vehicle_year']:'';
                            $vehicle['vehicle_make'] = !empty($vehicle['vehicle_make'])?$vehicle['vehicle_make']:'';
                            $vehicle['vehicle_model'] = !empty($vehicle['vehicle_model'])?$vehicle['vehicle_model']:'';
                            $vehicle['vehicle_type'] = !empty($vehicle['vehicle_type'])?$vehicle['vehicle_type']:'';
                            $vehicle['vehicle_color'] = !empty($vehicle['vehicle_color'])?$vehicle['vehicle_color']:'';
                            $vehicle['vehicle_tag'] = !empty($vehicle['vehicle_tag'])?$vehicle['vehicle_tag']:'';
                            $vehicle['vehicle_state'] = !empty($vehicle['vehicle_state'])?$vehicle['vehicle_state']:'';
                            $vehicle['vehicle_description'] = !empty($vehicle['vehicle_description'])?$vehicle['vehicle_description']:'';

                                DB::table('submitted_tips_vehicles')->insert(
                                ['tips_id' => $tips_id, 'vehicle_year' => $vehicle['vehicle_year'], 'vehicle_make' => $vehicle['vehicle_make'], 'vehicle_model' => $vehicle['vehicle_model'], 'vehicle_type' => $vehicle['vehicle_type'], 'vehicle_color' => $vehicle['vehicle_color'], 'vehicle_tag' => $vehicle['vehicle_tag'], 'vehicle_state' => $vehicle['vehicle_state'], 'vehicle_description' => $vehicle['vehicle_description'] ]
                                );


                        }
                    }
                }

                return response()->json(['status'=>true,'token' => "Bearer ".$header, 'tips_id' => $tips_id,  'data' => $input]);

                //return response()->json(['status'=>true,'token' => "Bearer ".$header, 'tips_id' => $tips_id,  'message' => 'Post added successfully']);



            }

        }


    }    
    
//++++++++++++++++++++++++++++++++++++++++++++++// 

    

}