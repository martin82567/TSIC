<?php
namespace App\Http\Controllers;
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
// use File;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class CronController extends Controller
{

	private $admin_email;
	private $fcmApiKey;

	public function __construct()
	{
		$admin_data = get_single_data_id('admins',1);
		$this->admin_email = $admin_data->email;
		$this->fcmApiKey = config('app.fcmApiKey');
	}

	public function checking_email(Request $request)
	{

        	// mentee_last_activity(1);

        	$message = '<html><body>';
		$message .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
		$message .= "<tr style='background: #eee;'><td><strong>Name:</strong> </td><td>Test User</td></tr>";
		$message .= "<tr><td><strong>Email:</strong> </td><td>test@user.com</td></tr>";
		$message .= "<tr><td><strong>Type of Change:</strong> </td><td>Test Type</td></tr>";
		$message .= "<tr><td><strong>Urgency:</strong> </td><td>Urgent</td></tr>";
		$message .= "<tr><td><strong>URL To Change (main):</strong> </td><td>https://app.seeandsend.info/login</td></tr>";
		$message .= "<tr><td><strong>NEW Content:</strong> </td><td>Lorem Ipsum</td></tr>";
		$message .= "</table>";
		$message .= "</body></html>";

		// return $message;
		email_send('aquatechdev4@gmail.com','Test Email',$message);
		die;

		$url = 'https://fcm.googleapis.com/fcm/send';

		$fcmApiKey = $this->fcmApiKey;

		echo '<pre>'; echo $fcmApiKey;

		$firebase_token = 'cWbURaAWSQeUT5mGRM2lQa:APA91bHB_0P1otPKQNW9jBlF3pwRd124wZV8_znM_QIFHc8S_pbkacWAPysK94TEZCaJu6-K89wLiMdLssitAFUQU8VKVyXhSpfCzSFq_dLxXmIPoRTwcri4a8yeDcsxkLkN323FxmHi';

		$message = "Test Push";

		$send_data = array('title' => "Test",'type' => 'meeting' , 'meeting_id' => 10,'message'=>$message,'firebase_token' => $firebase_token , 'unread_chat'=> 5, 'unread_task' => 10 );
		$data_arr = array('meeting_data' => $send_data);

		if($request->device_type == "iOS"){



		    $msg = array('message' => $message,'title' => "Test", 'sound'=>"default", 'badge'=>1 );
		    // $msg = array('body' => $message,'title' => "Test"  );
		    // $alert = array('alert' => $msg);
		    // $fields = array('to' => $firebase_token,'aps' => $alert,'data' => $data_arr, 'priority'=>'high'); // For IOS



		    $fields = array('to' => $firebase_token,'notification'=> $msg,'data'=>$data_arr);



		}else if($request->device_type == "android"){

			$fields = array('to' => $firebase_token,'data' => $send_data ); // For Android
		}

		$headers = array(
		    'Authorization: key=' . $fcmApiKey,
		    'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch );

		if ($result === FALSE) {
		    die('Curl failed: ' . curl_error($ch));
		}
		// Close connection
		curl_close($ch);

		$result_arr = json_decode($result);
		echo '<pre>'; print_r($fields);
		echo '<pre>'; print_r($result_arr);
		// echo '<pre>'; print_r($result);
		// echo '<pre>'; echo $result_arr->success;


	}

	public function test_entry(Request $request)
	{
		# code...


	}


	public function mapMentee(Request $request)
	{
		$user_id_encrypt = !empty($request->user_id_encrypt)?$request->user_id_encrypt:'';
		if(!empty($user_id_encrypt)){
			echo '<pre>'; echo Crypt::encryptString($user_id_encrypt);
		}

		// $getStarToken = getStarToken();

		// // echo $getStarToken;

		// $cURLConnection = curl_init();
	 //        $url = 'https://unison.tsic.org/api/Mentors/GetByOffice/3';

	 //        curl_setopt($cURLConnection, CURLOPT_URL,$url);
	 //        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
	 //        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
	 //                'Authorization: Bearer '.$getStarToken
	 //            ));

	 //        $dataresponce = curl_exec($cURLConnection);
	 //        $http_status = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
	 //        curl_close($cURLConnection);
	 //        $mentorlist = json_decode($dataresponce);

	 //        // echo '<pre>'; print_r($dataresponce);
	 //        echo '<pre>'; print_r($mentorlist); die;


		// $data = DB::table('video_chat_rooms')->select('id','chat_code','room_sid','created_at')->where('is_week_completed',1)->get()->toarray();

		// $data = DB::select(DB::raw("SELECT SUM(duration) AS used_duration, chat_code FROM video_chat_rooms WHERE is_week_completed = 0 GROUP BY chat_code"));

		// foreach($data as $d){
		// 	// $remaining = (1800 - $d->used_duration);
		// 	// DB::table('video_chat_user')->where('chat_code',$d->chat_code)->update(['remaining_time'=>$remaining]);
		// 	// $week = DB::table('video_chat_week')->where('chat_code',$d->chat_code)->where('start_date','2021-10-04')->where('end_date','2021-10-10')->first();

		// 	// if(!empty($week)){
		// 	// 	$remaining = (1800 - $d->used_duration);
		// 	// 	$d->remaining = $remaining;
		// 	// 	DB::table('video_chat_week')->where('id',$week->id)->update(['used'=> $d->used_duration,'remaining'=>$remaining]);
		// 	// }
		// }


		// echo '<pre>'; print_r($data); die;



		// $session = DB::table('session AS s')->select('s.*','mentor.stardbID AS mentor_stardbID','mentee.stardbID AS mentee_stardbID')->leftJoin('mentor', 'mentor.id','s.mentor_id')->leftJoin('mentee', 'mentee.id','s.mentee_id')->whereIn('s.id', [32016])->get()->toarray();

		// foreach($session as $d){

		// 	$session_method_location = DB::table('session_method_location')->where('id',$d->session_method_location_id)->first();
  //       	$method_id = $session_method_location->method_id;


		// 	$data = array(
  //                           "studentID" => $d->mentee_stardbID,
  //                           "mentorID" => $d->mentor_stardbID,
  //                           "sessionDate" => $d->schedule_date,
  //                           "sessionDuration" => (int) $d->time_duration,
  //                           "sessionNote" => $d->name,
  //                           "sessionTypeID" => (int) $d->type,
  //                           "sessionSourceID" => 2,
  //                           "sessionLocationID" => $method_id
  //                       );
  //           $data_string = json_encode($data);

  //           // echo '<pre>'; print_r($data_string); die;

  //           $ch = curl_init('https://unison.tsic.org/api/MentoringSessions/');
  //           curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  //           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  //               'Content-Type: application/json',
  //               'Authorization: Bearer '.$getStarToken,
  //               'Content-Length: ' . strlen($data_string))
  //           );

  //           $result = curl_exec($ch);
  //           curl_close($ch);

  //           // echo  $result; die;

  //           DB::table('session')->where('id',$d->id)->update(['stardbID'=>$result,'creationmethod'=>'stardbapi']);



		// }

		// echo '<pre>'; print_r($session);



	}

	public function get_user_address()
	{
		$data = array();
		$data = DB::table('school')->select('id','name','address','state','city','latitude','longitude','stardbID')->where('address','!=','')->where('creationmethod', 'salesforceapi')->whereRaw('id BETWEEN 851 AND 2007')->get()->toarray();
		// $data = DB::table('school')->select('id','name','address','state','city','latitude','longitude','stardbID')->where('address','!=','')->where('creationmethod', 'stardbapi')->get()->toarray();



		if(!empty($data)){
			foreach($data as $d){
				$address =  str_replace(" ", "+", $d->address.' '.$d->city.' '.$d->state.' USA');
				$d->new_add = $address;
				$addressValGeoCode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&address='.$address.'&sensor=false');

				$json_val = json_decode($addressValGeoCode);

				if(!empty($json_val->results)){
					$lat = $json_val->results[0]->geometry->location->lat;
				    $long = $json_val->results[0]->geometry->location->lng;

				    $d->lat = $lat;
				    $d->long = $long;

				    // DB::table('school')->where('id',$d->id)->update(['latitude'=>$lat,'longitude'=>$long]);

				}else{
					$d->lat = '';
				    $d->long = '';
				}

				// $d->new_add = $json_val;



			}
		}
		echo '<pre>'; print_r($data); die;
		/*
		$address = str_replace(" ", "+", "2802 NE 8th Avenue");

		$addressValGeoCode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&address='.$address.'&sensor=false');

		$json_val = json_decode($addressValGeoCode);

		echo '<pre>'; print_r($json_val);

		$lat = $json_val->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		$lng = $json_val->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

		echo $lat; echo '<pre>';
		echo $lng; die;
		*/
	}

	public function get_timezone_latlong()
	{


		$admin_email = $this->admin_email;
		$data = DB::table('mentor_mentee_chat_threads AS mmct')->select('mmct.*')->where('mmct.is_keyword_checked',0)->where('mmct.id', 483676)->get()->toarray();


		// echo '<pre>'; print_r($data); die;

		if(!empty($data)){
			foreach($data as $d){
				$chat_code = $d->chat_code;
				$mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('code',$chat_code)->first();
				// echo '<pre>';print_r($mentor_mentee_chat_codes); die;
				if(!empty($mentor_mentee_chat_codes)){
					$mentor_id = $mentor_mentee_chat_codes->mentor_id;
					$mentor_data = get_single_data_id('mentor',$mentor_id);

					$affiliate_id = $mentor_data->assigned_by;
					$affiliate_data = get_single_data_id('admins',$affiliate_id);
					$timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';

					$get_keyword_access = get_keyword_access($affiliate_id);
					$keyword_email = !empty($get_keyword_access)?$get_keyword_access[0]->email:'';
					$keyword_name = !empty($get_keyword_access)?$get_keyword_access[0]->name:'';

					if(!empty($get_keyword_access)){
						if($get_keyword_access[0]->type == 3){
							$keyword_staff_id = $get_keyword_access[0]->id;
						}else{
							$keyword_staff_id = 0;
						}
					}else{
						$keyword_staff_id = 0;
					}

					$keyword_super_staff = DB::table('admins')->select('id','name','email')->where('parent_id', 1)->where('is_allow_keyword_notification', 1)->where('is_active', 1)->first();

					$keyword_super_staff_id = !empty($keyword_super_staff)?$keyword_super_staff->id:0;

					$keyword_super_staff_email = !empty($keyword_super_staff)?$keyword_super_staff->email:'';

					// echo '<pre>'; echo $affiliate_email;
					date_default_timezone_set($timezone);
                	$created_at = date('Y-m-d H:i:s');

					if($d->from_where == 'mentor'){
	    				$sender_user = "Mentor";
	    				$sender_data = get_single_data_id('mentor',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Mentee";
	    				$receiver_data = get_single_data_id('mentee',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;
	    			}else{
	    				$sender_user = "Mentee";
	    				$sender_data = get_single_data_id('mentee',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Mentor";
	    				$receiver_data = get_single_data_id('mentor',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;

	    			}

	    			$stripped_message = $d->message;
	    			$stripped_message = preg_replace('/[ ,]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ !]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ ?]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ .]+/', ' ', $stripped_message);

					// echo $stripped_message;



					$stripped_message = remove_emoji($stripped_message);
					echo '<pre>';  echo $stripped_message;
					// die;

					// DB::table('mentor_mentee_chat_threads')->where('id',$d->id)->update(['stripped_message'=>$stripped_message]);

	    			$message = explode(" ",$stripped_message);
	    			// $message = explode(" ",$d->message);
					$whereOR = "";
					if(!empty($message)){
						$i=1;
						foreach($message as $m){

							$m = str_replace("'","\'", $m);
                            $m = str_replace("\\","\\\\", $m);

							echo '<pre>'; echo $m; echo '<br/>';

							if(count($message) == 1){
								$whereOR .= "title = '".$m."' ";
							}else if(count($message) > 1){
								if($i == count($message)){
									$whereOR .= "title = '".$m."'  ";
								}else{
									$whereOR .= "title = '".$m."' OR ";
								}
							}
							$i++;
						}
					}

					// die;

					// $check_keyword = DB::table('keyword')->select('*')->where('status', 1)->whereRaw(" ( ".$whereOR." ) ")->get()->toarray();
					$check_keyword = DB::select(DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND  ( ".$whereOR." ) "));

					echo '<pre>'; print_r($check_keyword); die;

					$keywords_arr = array();

					$d->is_keyword_got = 0;
					if(!empty($check_keyword)){
						$d->is_keyword_got = 1;
						// $is_keyword_checked = true;
						$d->keyword_email = $keyword_email;

		                $to = $keyword_email;
						$subject = 'TSIC Keyword Notification';
						$content = "";
						$content .= "<html><body>";
		                $content .= "<div>Hi, ".$keyword_name.", a keyword specific chat has been identified.";
		                $content .= "Please review immediately.";
		                $content .= "<br/>Regards";
		                $content .= "<div>";
		                $content .= "<table>";
				        $content .= "<tr>
									<th>Sender</th>
									<th>Receiver</th>
									<th>Messsage</th>
									<th>Date&Time</th>
									</tr>
									<tr>
									<td>".$sender_name." (".$sender_user.")</td>
									<td>".$receiver_name." (".$receiver_user.")</td>
									<td>".$d->message."</td>
									<td>".date('m-d-Y H:i', strtotime($created_at))."</td>
									</tr>";
				        $content .= "</table>";
		                $content .= "</div>";
		                $content .= "</div></body></html>";

		                if(!empty($to)){
		                	// email_send($to,$subject,$content);
		                }

		                if(!empty($keyword_super_staff_email)){
		                	// email_send($keyword_super_staff_email,$subject,$content);
		                }

		                foreach($check_keyword as $ck){
							$keywords_arr[] = $ck->title;
						}

						$keywords = !empty($keywords_arr)?implode(",",$keywords_arr):'';

		         //        DB::table('keyword_chat_notification')->insert([
											// 	'chat_type'=>'mentor-mentee',
											// 	'from_where'=>$d->from_where,
											// 	'sender_id'=>$d->sender_id,
											// 	'receiver_id'=>$d->receiver_id,
											// 	'affiliate_id'=>$affiliate_id,
											// 	'staff_id'=>$keyword_staff_id,
											// 	'super_staff_id'=>$keyword_super_staff_id,
											// 	'message'=>$d->message,
											// 	'keywords'=>$keywords,
											// 	'chat_code'=>$d->chat_code,
											// 	'created_at'=> $created_at
											// ]);







					}



				}

				// DB::table('mentor_mentee_chat_threads')->where('id',$d->id)->update(['is_keyword_checked'=>1]);

			}
		}



		echo '<pre>'; print_r($data); die;


	}

	public function get_duplicate_entry()
	{
		$duplicate_email = $email_ids = $du =  array();
		$duplicate_email = DB::select(DB::raw("SELECT email, COUNT(email) FROM `mentee` GROUP BY email HAVING COUNT(email) > 1"));

		if(!empty($duplicate_email)){
			foreach($duplicate_email as $de){
				$email_ids[] = $de->email;
			}
		}

		if(!empty($email_ids)){
			foreach($email_ids as $e){
				$du = DB::table('mentee')->select('id')->where('email',$e)->get()->toarray();

				if(!empty($du)){
					$i = 0;
					foreach($du as $d){
						if($i > 0){
							DB::table('mentee')->where('id',$d->id)->delete();
						}
						$i++;
					}
				}
			}
		}

		echo '<pre>'; print_r($email_ids);
		// echo '<pre>'; print_r($du);
	}

	public function mentor_staff_chat_email()
	{
		// die("Hi");
		$admin_email = $this->admin_email;
		$data = DB::table('mentor_staff_chat_threads AS msct')->select('msct.*')->where('msct.is_keyword_checked',0)->get()->toarray();
		if(!empty($data)){
			foreach($data as $d){
				$chat_code = $d->chat_code;
				$mentor_staff_chat_codes = DB::table('mentor_staff_chat_codes')->where('code',$chat_code)->first();
				if(!empty($mentor_staff_chat_codes)){

					$staff_id = $mentor_staff_chat_codes->staff_id;
					$staff_data = get_single_data_id('admins',$staff_id);
					$parent_id = $staff_data->parent_id;
					$timezone = !empty($staff_data->timezone)?$staff_data->timezone:'America/New_York';

					$get_keyword_access = get_keyword_access($parent_id);
					$keyword_email = !empty($get_keyword_access)?$get_keyword_access[0]->email:'';
					$keyword_name = !empty($get_keyword_access)?$get_keyword_access[0]->name:'';

					if(!empty($get_keyword_access)){
						if($get_keyword_access[0]->type == 3){
							$keyword_staff_id = $get_keyword_access[0]->id;
						}else{
							$keyword_staff_id = 0;
						}
					}else{
						$keyword_staff_id = 0;
					}

					$keyword_super_staff = DB::table('admins')->select('id','name','email')->where('parent_id', 1)->where('is_allow_keyword_notification', 1)->where('is_active', 1)->first();

					$keyword_super_staff_id = !empty($keyword_super_staff)?$keyword_super_staff->id:0;
					$keyword_super_staff_email = !empty($keyword_super_staff)?$keyword_super_staff->email:'';


					date_default_timezone_set($timezone);
                	$created_at = date('Y-m-d H:i:s');


					if($d->from_where == 'mentor'){
	    				$sender_user = "Mentor";
	    				$sender_data = get_single_data_id('mentor',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Staff";
	    				$receiver_data = get_single_data_id('admins',$d->receiver_id);
	    				$receiver_name = $receiver_data->name;
	    			}else{
	    				$sender_user = "Staff";
	    				$sender_data = get_single_data_id('admins',$d->sender_id);
	    				$sender_name = $sender_data->name;

	    				$receiver_user = "Mentor";
	    				$receiver_data = get_single_data_id('mentor',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;
	    			}

	    			$stripped_message = $d->message;
	    			$stripped_message = preg_replace('/[ ,]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ !]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ ?]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ .]+/', ' ', $stripped_message);

					$stripped_message = remove_emoji($stripped_message);

					DB::table('mentor_staff_chat_threads')->where('id',$d->id)->update(['stripped_message'=>$stripped_message]);


					$message = explode(" ",$stripped_message);
					// $is_keyword_checked = false;
					$whereOR = "";
					if(!empty($message)){
						$i=1;
						foreach($message as $m){

							// echo count($message);
							$m = str_replace("'","\'", $m);
                            $m = str_replace("\\","\\\\", $m);

							if(count($message) == 1){
								$whereOR .= "title = '".$m."' ";
							}else if(count($message) > 1){
								if($i == count($message)){
									$whereOR .= "title = '".$m."'  ";
								}else{
									$whereOR .= "title = '".$m."' OR ";
								}
							}
							$i++;
						}
					}

					$check_keyword = DB::select(DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND   ( ".$whereOR." ) "));

					$keywords_arr = array();

					$d->is_keyword_got =  0;

					if(!empty($check_keyword)){
						$d->is_keyword_got = 1;
						// $is_keyword_checked = true;
						$to = $keyword_email;
						$subject = 'TSIC Keyword Notification';
						$content = "";
						$content .= "<html><body>";
		                $content .= "<div>Hi, ".$keyword_name.", a keyword specific chat has been identified.";
		                $content .= "Please review immediately.";
		                $content .= "<br/>Regards";
		                $content .= "<div>";
		                $content .= "<table>";
				        $content .= "<tr>
									<th>Sender</th>
									<th>Receiver</th>
									<th>Messsage</th>
									<th>Date&Time</th>
									</tr>
									<tr>
									<td>".$sender_name." (".$sender_user.")</td>
									<td>".$receiver_name." (".$receiver_user.")</td>
									<td>".$d->message."</td>
									<td>".date('m-d-Y H:i', strtotime($created_at))."</td>
									</tr>";
				        $content .= "</table>";
		                $content .= "</div>";
		                $content .= "</div></body></html>";

		                if(!empty($to)){
		                	email_send($to,$subject,$content);
		                }

		                if(!empty($keyword_super_staff_email)){
		                	email_send($keyword_super_staff_email,$subject,$content);
		                }

		                foreach($check_keyword as $ck){
							$keywords_arr[] = $ck->title;
						}

						$keywords = !empty($keywords_arr)?implode(",",$keywords_arr):'';

		                DB::table('keyword_chat_notification')->insert([
											'chat_type'=>'mentor-staff',
											'from_where'=>$d->from_where,
											'sender_id'=>$d->sender_id,
											'receiver_id'=>$d->receiver_id,
											'affiliate_id'=>$parent_id,
											'staff_id'=>$keyword_staff_id,
											'super_staff_id'=>$keyword_super_staff_id,
											'message'=>$d->message,
											'keywords'=>$keywords,
											'chat_code'=>$d->chat_code,
											'created_at'=> $created_at
										]);


					}

				}

				DB::table('mentor_staff_chat_threads')->where('id',$d->id)->update(['is_keyword_checked'=>1]);



			}
		}

		echo '<pre>'; print_r($data);
	}


	public function mentor_mentee_chat_email()
	{
		// die('Hi');
		$admin_email = $this->admin_email;
		$data = DB::table('mentor_mentee_chat_threads AS mmct')->select('mmct.*')->where('mmct.is_keyword_checked',0)->get()->toarray();


		if(!empty($data)){
			foreach($data as $d){
				$chat_code = $d->chat_code;
				$mentor_mentee_chat_codes = DB::table('mentor_mentee_chat_codes')->where('code',$chat_code)->first();
				// echo '<pre>';print_r($mentor_mentee_chat_codes); die;
				if(!empty($mentor_mentee_chat_codes)){
					$mentor_id = $mentor_mentee_chat_codes->mentor_id;
					$mentor_data = get_single_data_id('mentor',$mentor_id);

					$affiliate_id = $mentor_data->assigned_by;
					$affiliate_data = get_single_data_id('admins',$affiliate_id);
					$timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';

					$get_keyword_access = get_keyword_access($affiliate_id);
					$keyword_email = !empty($get_keyword_access)?$get_keyword_access[0]->email:'';
					$keyword_name = !empty($get_keyword_access)?$get_keyword_access[0]->name:'';

					if(!empty($get_keyword_access)){
						if($get_keyword_access[0]->type == 3){
							$keyword_staff_id = $get_keyword_access[0]->id;
						}else{
							$keyword_staff_id = 0;
						}
					}else{
						$keyword_staff_id = 0;
					}

					$keyword_super_staff = DB::table('admins')->select('id','name','email')->where('parent_id', 1)->where('is_allow_keyword_notification', 1)->where('is_active', 1)->first();

					$keyword_super_staff_id = !empty($keyword_super_staff)?$keyword_super_staff->id:0;

					$keyword_super_staff_email = !empty($keyword_super_staff)?$keyword_super_staff->email:'';

					// echo '<pre>'; echo $affiliate_email;
					date_default_timezone_set($timezone);
                	$created_at = date('Y-m-d H:i:s');

					if($d->from_where == 'mentor'){
	    				$sender_user = "Mentor";
	    				$sender_data = get_single_data_id('mentor',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Mentee";
	    				$receiver_data = get_single_data_id('mentee',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;
	    			}else{
	    				$sender_user = "Mentee";
	    				$sender_data = get_single_data_id('mentee',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Mentor";
	    				$receiver_data = get_single_data_id('mentor',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;

	    			}

	    			$stripped_message = $d->message;
	    			$stripped_message = preg_replace('/[ ,]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ !]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ ?]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ .]+/', ' ', $stripped_message);

					$stripped_message = remove_emoji($stripped_message);

					DB::table('mentor_mentee_chat_threads')->where('id',$d->id)->update(['stripped_message'=>$stripped_message]);

	    			$message = explode(" ",$stripped_message);
	    			// $message = explode(" ",$d->message);
					$whereOR = "";
					if(!empty($message)){
						$i=1;
						foreach($message as $m){

                            $m = str_replace("\\","\\\\", $m);
							$m = str_replace("'","\'", $m);

							if(count($message) == 1){
								$whereOR .= "title = '".$m."' ";
							}else if(count($message) > 1){
								if($i == count($message)){
									$whereOR .= "title = '".$m."'  ";
								}else{
									$whereOR .= "title = '".$m."' OR ";
								}
							}
							$i++;
						}
					}

					$check_keyword = DB::select(DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND  ( ".$whereOR." ) "));

					$keywords_arr = array();

					$d->is_keyword_got = 0;
					if(!empty($check_keyword)){
						$d->is_keyword_got = 1;
						// $is_keyword_checked = true;
						$d->keyword_email = $keyword_email;

		                $to = $keyword_email;
						$subject = 'TSIC Keyword Notification';
						$content = "";
						$content .= "<html><body>";
		                $content .= "<div>Hi, ".$keyword_name.", a keyword specific chat has been identified.";
		                $content .= "Please review immediately.";
		                $content .= "<br/>Regards";
		                $content .= "<div>";
		                $content .= "<table>";
				        $content .= "<tr>
									<th>Sender</th>
									<th>Receiver</th>
									<th>Messsage</th>
									<th>Date&Time</th>
									</tr>
									<tr>
									<td>".$sender_name." (".$sender_user.")</td>
									<td>".$receiver_name." (".$receiver_user.")</td>
									<td>".$d->message."</td>
									<td>".date('m-d-Y H:i', strtotime($created_at))."</td>
									</tr>";
				        $content .= "</table>";
		                $content .= "</div>";
		                $content .= "</div></body></html>";

		                if(!empty($to)){
		                	email_send($to,$subject,$content);
		                }

		                if(!empty($keyword_super_staff_email)){
		                	email_send($keyword_super_staff_email,$subject,$content);
		                }

		                foreach($check_keyword as $ck){
							$keywords_arr[] = $ck->title;
						}

						$keywords = !empty($keywords_arr)?implode(",",$keywords_arr):'';

		                DB::table('keyword_chat_notification')->insert([
												'chat_type'=>'mentor-mentee',
												'from_where'=>$d->from_where,
												'sender_id'=>$d->sender_id,
												'receiver_id'=>$d->receiver_id,
												'affiliate_id'=>$affiliate_id,
												'staff_id'=>$keyword_staff_id,
												'super_staff_id'=>$keyword_super_staff_id,
												'message'=>$d->message,
												'keywords'=>$keywords,
												'chat_code'=>$d->chat_code,
												'created_at'=> $created_at
											]);







					}



				}

				DB::table('mentor_mentee_chat_threads')->where('id',$d->id)->update(['is_keyword_checked'=>1]);

			}
		}

		echo '<pre>'; print_r($data);

	}

	public function mentee_staff_chat_email()
	{


		$admin_email = $this->admin_email;
		// $data = DB::table('mentee_staff_chat_threads AS msct')->select('msct.*')->where('msct.is_keyword_checked',0)->whereRaw('msct.id BETWEEN 374 AND 450')->get()->toarray();
		$data = DB::table('mentee_staff_chat_threads AS msct')->select('msct.*')->where('msct.is_keyword_checked',0)->get()->toarray();
		if(!empty($data)){
			foreach($data as $d){
				$chat_code = $d->chat_code;
				$mentee_staff_chat_codes = DB::table('mentee_staff_chat_codes')->where('code',$chat_code)->first();
				if(!empty($mentee_staff_chat_codes)){

					$staff_id = $mentee_staff_chat_codes->staff_id;
					$staff_data = get_single_data_id('admins',$staff_id);
					$parent_id = $staff_data->parent_id;
					$timezone = !empty($staff_data->timezone)?$staff_data->timezone:'America/New_York';
					$get_keyword_access = get_keyword_access($parent_id);
					$keyword_email = !empty($get_keyword_access)?$get_keyword_access[0]->email:'';
					$keyword_name = !empty($get_keyword_access)?$get_keyword_access[0]->name:'';

					if(!empty($get_keyword_access)){
						if($get_keyword_access[0]->type == 3){
							$keyword_staff_id = $get_keyword_access[0]->id;
						}else{
							$keyword_staff_id = 0;
						}
					}else{
						$keyword_staff_id = 0;
					}


					$keyword_super_staff = DB::table('admins')->select('id','name','email')->where('parent_id', 1)->where('is_allow_keyword_notification', 1)->where('is_active', 1)->first();

					$keyword_super_staff_id = !empty($keyword_super_staff)?$keyword_super_staff->id:0;

					$keyword_super_staff_email = !empty($keyword_super_staff)?$keyword_super_staff->email:'';

					date_default_timezone_set($timezone);
					$created_at = date('Y-m-d H:i:s');

					if($d->from_where == 'mentee'){
	    				$sender_user = "Mentee";
	    				$sender_data = get_single_data_id('mentee',$d->sender_id);
	    				$sender_name = $sender_data->firstname.' '.$sender_data->middlename.' '.$sender_data->lastname;

	    				$receiver_user = "Staff";
	    				$receiver_data = get_single_data_id('admins',$d->receiver_id);
	    				$receiver_name = $receiver_data->name;
	    			}else{
	    				$sender_user = "Staff";
	    				$sender_data = get_single_data_id('admins',$d->sender_id);
	    				$sender_name = $sender_data->name;

	    				$receiver_user = "Mentee";
	    				$receiver_data = get_single_data_id('mentee',$d->receiver_id);
	    				$receiver_name = $receiver_data->firstname.' '.$receiver_data->middlename.' '.$receiver_data->lastname;

	    			}

	    			$stripped_message = $d->message;
	    			$stripped_message = preg_replace('/[ ,]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ !]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ ?]+/', ' ', $stripped_message);
					$stripped_message = preg_replace('/[ .]+/', ' ', $stripped_message);

					$stripped_message = remove_emoji($stripped_message);

					DB::table('mentee_staff_chat_threads')->where('id',$d->id)->update(['stripped_message'=>$stripped_message]);

	    			$message = explode(" ",$stripped_message);
					// $message = explode(" ",$d->message);
					// $is_keyword_checked = false;
					$whereOR = "";
					if(!empty($message)){
						$i=1;
						foreach($message as $m){

							// $m = str_replace('"', "'", $m);
							$m = str_replace("'","\'", $m);
                            $m = str_replace("\\","\\\\", $m);

							// echo $m;

							// echo count($message);
							if(count($message) == 1){
								$whereOR .= "title = '".$m."' ";
							}else if(count($message) > 1){
								if($i == count($message)){
									$whereOR .= "title = '".$m."'  ";
								}else{
									$whereOR .= "title = '".$m."' OR ";
								}
							}
							$i++;
						}
					}

					// echo '<pre>'; echo $whereOR;

					// die;

					// $x = DB::raw("SELECT * FROM `keyword` WHERE ".$whereOR." ");

					// echo '<pre>'; print_r($x);


					$check_keyword = DB::select(DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND  ( ".$whereOR." ) "));

					// echo '<pre>'; print_r($check_keyword); die;

					$keywords_arr = array();

					$d->is_keyword_got = 0;

					if(!empty($check_keyword)){
						$d->is_keyword_got = 1;
						// $is_keyword_checked = true;

		                $to = $keyword_email;
						$subject = 'TSIC Keyword Notification';
						$content = "";
						$content .= "<html><body>";
		                $content .= "<div>Hi, ".$keyword_name.", a keyword specific chat has been identified.";
		                $content .= "Please review immediately.";
		                $content .= "<br/>Regards";
		                $content .= "<div>";
		                $content .= "<table>";
				        $content .= "<tr>
									<th>Sender</th>
									<th>Receiver</th>
									<th>Messsage</th>
									<th>Date&Time</th>
									</tr>
									<tr>
									<td>".$sender_name." (".$sender_user.")</td>
									<td>".$receiver_name." (".$receiver_user.")</td>
									<td>".$d->message."</td>
									<td>".date('m-d-Y H:i', strtotime($created_at))."</td>
									</tr>";
				        $content .= "</table>";
		                $content .= "</div>";
		                $content .= "</div></body></html>";

		                if(!empty($to)){
		                	email_send($to,$subject,$content);
		                }

		                if(!empty($keyword_super_staff_email)){
		                	email_send($keyword_super_staff_email,$subject,$content);
		                }

		                foreach($check_keyword as $ck){
							$keywords_arr[] = $ck->title;
						}

						$keywords = !empty($keywords_arr)?implode(",",$keywords_arr):'';

		                DB::table('keyword_chat_notification')->insert([
												'chat_type'=>'mentee-staff',
												'from_where'=>$d->from_where,
												'sender_id'=>$d->sender_id,
												'receiver_id'=>$d->receiver_id,
												'affiliate_id'=>$parent_id,
												'staff_id'=>$keyword_staff_id,
												'super_staff_id'=>$keyword_super_staff_id,
												'message'=>$d->message,
												'keywords'=>$keywords,
												'chat_code'=>$d->chat_code,
												'created_at'=> $created_at
											]);

					}

				}

				DB::table('mentee_staff_chat_threads')->where('id',$d->id)->update(['is_keyword_checked'=>1]);



			}
		}

		echo '<pre>'; print_r($data);
	}

	public function create_star_session($token='')
	{
		# code...

		// die;

		$getStarToken = getStarToken();

		$session = DB::table('session')
					->select('session.*','mentor.stardbID AS mentor_stardbID','mentee.stardbID AS mentee_stardbID')
					->leftJoin('mentor','mentor.id','session.mentor_id')
					->leftJoin('mentee','mentee.id','session.mentee_id')
					->where('session.id', 18824)
					->get()->toarray();

		echo '<pre>'; print_r($session);      die;

		// $session = DB::table('session')
		// 			->select('session.*','mentor.stardbID AS mentor_stardbID','mentee.stardbID AS mentee_stardbID')
		// 			->leftJoin('mentor','mentor.id','session.mentor_id')
		// 			->leftJoin('mentee','mentee.id','session.mentee_id')
		// 			->where('mentor.stardbID','!=',0)
		// 			->where('session.creationmethod', 'stardbapi')
		// 			->where('session.is_created_by_app', 0)
		// 			->whereRaw("session.created_date BETWEEN '2021-01-01 00:00:00' AND '2021-02-10 00:00:00' ")
		// 			// ->where('session.created_date','>=','2021-01-01 00:00:00')
		// 			->get()->toarray();

		if(!empty($session)){
			foreach($session as $d){

				$get_session_method_location = DB::table('session_method_location')->where('id',$d->session_method_location_id)->first();

        		$method_id = $get_session_method_location->method_id;
        		$d->method_id = $method_id;

				$data = array(
                            // "mentoringSessionID" => $d->stardbID,
                            "studentID" => $d->mentee_stardbID,
                            "mentorID" => $d->mentor_stardbID,
                            "sessionDate" => $d->schedule_date,
                            "sessionDuration" => (int) $d->time_duration,
                            "sessionNote" => $d->name,
                            "sessionTypeID" =>  (int) $d->type,
                            "sessionSourceID" => 1,
                            "sessionLocationID" => $d->method_id
                        );
	            $data_string = json_encode($data);

	            $ch = curl_init('https://unison.tsic.org/api/MentoringSessions/');
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	                'Content-Type: application/json',
	                'Authorization: Bearer '.$getStarToken,
	                'Content-Length: ' . strlen($data_string))
	            );

	            $result = curl_exec($ch);
	            curl_close($ch);

	            // echo $result; die;

	            DB::table('session')->where('id',$d->id)->update(['stardbID'=>$result,'creationmethod'=>'salesforceapi']);
			}
		}

		echo '<pre>'; print_r($session);


	}

	public function check_active_keyword_exists(Request $request)
	{
		$message = !empty($request->message)?$request->message:'';
		if(!empty($message)){
			$message = explode(" ",$message);
			// $is_keyword_checked = false;
			$whereOR = "";
			if(!empty($message)){
				$i=1;
				foreach($message as $m){

					// $m = str_replace('"', "'", $m);
					$m = str_replace("'","\'", $m);
                    $m = str_replace("\\","\\\\", $m);
					// $m = str_replace(":","", $m);
					// $m = str_replace("-","", $m);
					// $m = str_replace("\\","/", $m);
					// $m = str_replace("?","", $m);


					// echo $m;

					// echo count($message);
					if(count($message) == 1){
						$whereOR .= "title = '".$m."' ";
					}else if(count($message) > 1){
						if($i == count($message)){
							$whereOR .= "title = '".$m."'  ";
						}else{
							$whereOR .= "title = '".$m."' OR ";
						}
					}
					$i++;
				}
			}

			echo '<pre>'; echo $whereOR;

			// die;

			$x = DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND  ( ".$whereOR." ) ");

			echo '<pre>'; print_r($x);


			$check_keyword = DB::select(DB::raw("SELECT * FROM `keyword` WHERE status = 1 AND  ( ".$whereOR." ) "));

			if(!empty($check_keyword)){
				echo 'Yes';
			}else{
				echo 'No';
			}

		}
	}

	public function send_notification_unread_mentor()
	{

		date_default_timezone_set('America/New_York'); // EST

        echo date('H');

        if(date('H') == '23'){



        	$staff_ids = DB::table('mentor_staff_chat_threads')->select('receiver_id')->where('from_where','mentor')->where('receiver_is_read',0)->distinct()->get();

			$staffs = array();
			if(!empty($staff_ids)){
				foreach($staff_ids as $staff_id){
					$staffs[] = $staff_id->receiver_id;
				}

			}

			// $staffs = [83];



			if(!empty($staffs)){
				foreach($staffs as $value){
					$staff_data = get_single_data_id('admins',$value);
					// $email = 'aquatechdev2@gmail.com';
					$email = $staff_data->email;
					$name = $staff_data->name;
					$data = DB::table('mentor_staff_chat_threads AS msct')->select('msct.*','mentor.firstname','mentor.lastname')->leftJoin('mentor','mentor.id','msct.sender_id')->where('msct.from_where','mentor')->where('msct.receiver_is_read',0)->where('msct.receiver_id',$value)->orderBy('msct.created_date','desc')->get()->toarray();

					// echo '<pre>'; print_r($data);

					if(!empty($data)){

						$subject = 'Mentor Messages';
						$content = "";
						$content .= "<html><body>";
						$content .= "<div>Hi, ".$name." you have some unread messages from your mentors. ";
						$content .= "Please review immediately.";
						$content .= "<br/>Regards";
						$content .= "<div>";
						$content .= "<table>";
						$content .= "<tr>
									<th>Sender</th>
									<th>Messsage</th>
									<th>DateTime</th>
									</tr>";



						foreach($data as $d){

							$mentor_name = $d->firstname.' '.$d->lastname;

							$content .= 	"<tr>
											<td>".$mentor_name."</td>
											<td>".$d->message."</td>
											<td>".date('m-d-Y H:i', strtotime($d->created_date))."</td>
											</tr>";

						}


						// $content .= $table_data;
						$content .= "</table>";
						$content .= "</div>";
						$content .= "</div></body></html>";

						if(!empty($staff_data->is_active)){
							email_send($email,$subject,$content);
						}else{
							DB::table('mentor_staff_chat_threads')->where('receiver_id',$value)->where('from_where','mentor')->update(['receiver_is_read'=>1]);
						}


					}
				}
			}

			echo '<pre>'; print_r($staffs);

			// echo '<pre>'; print_r($staff_ids);

        }

	}

	public function send_notification_unread_mentee()
	{
		date_default_timezone_set('America/New_York'); // EST

        echo date('H');

        if(date('H') == '23'){



        	$staff_ids = DB::table('mentee_staff_chat_threads')->select('receiver_id')->where('from_where','mentee')->where('receiver_is_read',0)->distinct()->get();

			$staffs = array();
			if(!empty($staff_ids)){
				foreach($staff_ids as $staff_id){
					$staffs[] = $staff_id->receiver_id;
				}

			}

			// $staffs = [83];



			if(!empty($staffs)){
				foreach($staffs as $value){
					$staff_data = get_single_data_id('admins',$value);
					// $email = 'aquatechdev2@gmail.com';
					$email = $staff_data->email;
					$name = $staff_data->name;
					$data = DB::table('mentee_staff_chat_threads AS msct')->select('msct.*','mentee.firstname','mentee.lastname')->leftJoin('mentee','mentee.id','msct.sender_id')->where('msct.from_where','mentee')->where('msct.receiver_is_read',0)->where('msct.receiver_id',$value)->orderBy('msct.created_date','desc')->get()->toarray();

					// echo '<pre>'; print_r($data);

					if(!empty($data)){

						$subject = 'Mentee Messages';
						$content = "";
						$content .= "<html><body>";
						$content .= "<div>Hi, ".$name." you have some unread messages from your mentees. ";
						$content .= "Please review immediately.";
						$content .= "<br/>Regards";
						$content .= "<div>";
						$content .= "<table>";
						$content .= "<tr>
									<th>Sender</th>
									<th>Messsage</th>
									<th>DateTime</th>
									</tr>";



						foreach($data as $d){

							$mentee_name = $d->firstname.' '.$d->lastname;

							$content .= 	"<tr>
											<td>".$mentee_name."</td>
											<td>".$d->message."</td>
											<td>".date('m-d-Y H:i', strtotime($d->created_date))."</td>
											</tr>";

						}


						// $content .= $table_data;
						$content .= "</table>";
						$content .= "</div>";
						$content .= "</div></body></html>";

						if(!empty($staff_data->is_active)){
							email_send($email,$subject,$content);
						}else{
							DB::table('mentee_staff_chat_threads')->where('receiver_id',$value)->where('from_where','mentee')->update(['receiver_is_read'=>1]);
						}


					}
				}
			}

			echo '<pre>'; print_r($staffs);

			// echo '<pre>'; print_r($staff_ids);
        }



	}


	public function meeting_reminder_before()
	{
		/* Mail and push notification ... only for mentors ... before 30 minutes */

		$fcmApiKey = $this->fcmApiKey;

		$message = "Your scheduled session with your Mentee will begin in 30 minutes. You can view details about the session in the 'Schedule A Session' navigation on the Take Stock App profile screen.";
		$message_mentee = "You have a session in next 30 minutes.";

		$data = DB::table('meeting')->select('meeting.id','meeting.title','meeting.agency_id','meeting.schedule_time','meeting_status.status','admins.timezone','mentor_meeting.user_id AS mentor_id','mentor.device_type','mentor.firebase_id','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentor.email','mentee_meeting.user_id AS mentee_id','mentee.device_type AS mentee_device_type','mentee.firebase_id AS mentee_firebase_id','mentee.firstname AS mentee_firstname','mentee.lastname AS mentee_lastname','mentee.email AS mentee_email')
									->leftJoin('meeting_status','meeting_status.meeting_id','meeting.id')
									->leftJoin('admins','admins.id','meeting.agency_id')
									->leftJoin('meeting_users AS mentor_meeting',function ($jr) {
	                                    $jr->on('mentor_meeting.meeting_id', '=' , 'meeting.id') ;
	                                    $jr->where('mentor_meeting.type','=','mentor') ;
	                                })
	                                ->leftJoin('meeting_users AS mentee_meeting',function ($je) {
	                                    $je->on('mentee_meeting.meeting_id', '=' , 'meeting.id') ;
	                                    $je->where('mentee_meeting.type','=','mentee') ;
	                                })
	                                ->leftJoin('mentor','mentor.id','mentor_meeting.user_id')
	                                ->leftJoin('mentee','mentee.id','mentee_meeting.user_id')
									->where('meeting_status.status',1)
									->get()->toarray();

		$arr = array();

		if(!empty($data)){
			foreach($data as $d){
				$timezone = $d->timezone;
				date_default_timezone_set($timezone);
				$cur_date = date('Y-m-d H:i');
				$d->before_time = date('Y-m-d H:i', strtotime('-30 minutes'));


				$d->cur_date = $cur_date;
				$d->time = strtotime($d->schedule_time);
				$d->startTime = date("Y-m-d H:i", strtotime('-30 minutes', $d->time));



				if( $d->startTime == $d->cur_date ){
					$arr[] = $d;
				}

			}
		}

		if(!empty($arr)){
			foreach($arr as $d){

				if(!empty($d->device_type) && !empty($d->firebase_id)){

					$notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$d->id,'notification_for'=>'before_notification','user_type'=>'mentor','user_id'=>$d->mentor_id,'notification_response'=>'','created_at'=>date('Y-m-d H:i:s')]);


					$mentor_schedule_session_count = schedule_session_count('mentor',$d->mentor_id);
    				$mentor_user_total_chat_count = user_total_chat_count('mentor',$d->mentor_id);
    				$mentor_total_unread_goaltask_count = user_total_unread_goaltask_count('mentor',$d->mentor_id);
                    $meeting_id = $d->id;
                    $unread_task_count =($mentor_schedule_session_count+$mentor_total_unread_goaltask_count);

					$send_data = array('title' => $message,'type' => 'meeting_notification' , 'meeting_id' => "$meeting_id",'message'=>$message,'firebase_token' => $d->firebase_id , 'unread_chat'=> "$mentor_user_total_chat_count", 'unread_task' => "$unread_task_count");

					$data_arr = array('meeting_data' => json_encode($send_data));

					if($d->device_type == "iOS"){

					    $msg = array('message' => $message,'title' => $message, 'sound'=>"default" , 'badge'=> ($mentor_schedule_session_count+$mentor_user_total_chat_count+$mentor_total_unread_goaltask_count)  );
					    $fields = array('to' => $d->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

					}else if($d->device_type == "android"){

						$fields = array('to' => $d->firebase_id,'data' => $send_data ); // For Android
					}

                    $result = sendPushNotificationWithV1($fields);

					if(!empty($result['name'])){
						DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => json_encode($result) ]);
					}
				}

				/* ++++++++++New Addition ... Push notification for mentee++++++++++++ */

				if(!empty($d->mentee_device_type) && !empty($d->mentee_firebase_id)){

					$notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$d->id,'notification_for'=>'before_notification','user_type'=>'mentee','user_id'=>$d->mentee_id,'notification_response'=>'','created_at'=>date('Y-m-d H:i:s')]);



					$mentee_schedule_session_count = schedule_session_count('mentee',$d->mentee_id);
    				$mentee_user_total_chat_count = user_total_chat_count('mentee',$d->mentee_id);
    				$mentee_total_unread_goaltask_count = user_total_unread_goaltask_count('mentee',$d->mentee_id);

                    $meeting_id = $d->id;
                    $unread_task_count =($mentee_schedule_session_count+$mentee_total_unread_goaltask_count);
					$send_data_mentee = array('title' => $message_mentee,'type' => 'meeting_notification' , 'meeting_id' => "$meeting_id",'message'=>$message_mentee,'firebase_token' => $d->mentee_firebase_id , 'unread_chat'=> "$mentee_user_total_chat_count", 'unread_task' => "$unread_task_count" );
					$data_arr = array('meeting_data' => json_encode($send_data_mentee));

					if($d->mentee_device_type == "iOS"){

					    $msg = array('message' => $message_mentee,'title' => $message_mentee, 'sound'=>"default" , 'badge'=> ($mentee_schedule_session_count+$mentee_user_total_chat_count+$mentee_total_unread_goaltask_count) );
					    $fields1 = array('to' => $d->mentee_firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

					}else if($d->mentee_device_type == "android"){

						$fields1 = array('to' => $d->mentee_firebase_id,'data' => $send_data_mentee ); // For Android
					}

                    $result1 = sendPushNotificationWithV1($fields1);

                    if(!empty($result1['name'])){
                        DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => json_encode($result1) ]);
                    }

				}
			}
		}

		// echo '<pre>'; print_r($data);
		echo '<pre>'; print_r($arr);
	}

	public function meeting_reminder_after()
	{
		/* Mail and push notification ... only for mentors ... after 60 minutes */

		$message = "Your scheduled session with your Mentee was 60 minutes ago. Please do not forget to log your session using the 'Log a Session' feature on the Take Stock App profile screen.";

		$data = DB::table('meeting')->select('meeting.id','meeting.title','meeting.agency_id','meeting.schedule_time','meeting_status.status','admins.timezone','mentor_meeting.user_id AS mentor_id','mentor.device_type','mentor.firebase_id','mentor.firstname AS mentor_firstname','mentor.lastname AS mentor_lastname','mentor.email')
									->leftJoin('meeting_status','meeting_status.meeting_id','meeting.id')
									->leftJoin('admins','admins.id','meeting.agency_id')
									->leftJoin('meeting_users AS mentor_meeting',function ($jr) {
	                                    $jr->on('mentor_meeting.meeting_id', '=' , 'meeting.id') ;
	                                    $jr->where('mentor_meeting.type','=','mentor') ;
	                                })
	                                ->leftJoin('mentor','mentor.id','mentor_meeting.user_id')
									->where('meeting_status.status',1)
									->get()->toarray();


		$arr = array();

		if(!empty($data)){
			foreach($data as $d){
				$timezone = $d->timezone;
				date_default_timezone_set($timezone);
				$cur_date = date('Y-m-d H:i');
				$d->before_time = date('Y-m-d H:i', strtotime('+60 minutes'));


				$d->cur_date = $cur_date;
				$d->time = strtotime($d->schedule_time);
				$d->startTime = date("Y-m-d H:i", strtotime('+60 minutes', $d->time));



				if( $d->startTime == $d->cur_date ){
					$arr[] = $d;
				}

			}
		}

		if(!empty($arr)){
			foreach($arr as $d){

				if(!empty($d->device_type) && !empty($d->firebase_id)){

					$notification_id = DB::table(MEETING_NOTIFICATION)->insertGetId(['meeting_id'=>$d->id,'notification_for'=>'before_notification','user_type'=>'mentor','user_id'=>$d->mentor_id,'notification_response'=>'','created_at'=>date('Y-m-d H:i:s')]);


					$schedule_session_count = schedule_session_count('mentor',$d->mentor_id);
				    $user_total_chat_count = user_total_chat_count('mentor',$d->mentor_id);
				    $user_total_unread_goaltask_count = user_total_unread_goaltask_count('mentor',$d->mentor_id);

                    $meeting_id = $d->id;
                    $unread_task_count =($schedule_session_count+$user_total_unread_goaltask_count);

                    $send_data = array('title' => $message,'type' => 'meeting_notification' , 'meeting_id' => "$meeting_id",'message'=>$message,'firebase_token' => $d->firebase_id, 'unread_chat'=> "$user_total_chat_count", 'unread_task' => "$unread_task_count" );

					$data_arr = array('meeting_data' => json_encode($send_data));

					if($d->device_type == "iOS"){

					    $msg = array('message' => $message,'title' => $message, 'sound'=>"default" , 'badge'=> ($schedule_session_count+$user_total_chat_count+$user_total_unread_goaltask_count)  );
					    $fields = array('to' => $d->firebase_id,'notification' => $msg,'data' => $data_arr, 'priority'=>'high'); // For IOS

					}else if($d->device_type == "android"){

						$fields = array('to' => $d->firebase_id,'data' => $send_data); // For Android
					}

                    $result = sendPushNotificationWithV1($fields);

                    if(!empty($result1['name'])){
                        DB::table(MEETING_NOTIFICATION)->where('id',$notification_id)->update(['notification_response' => json_encode($result) ]);
                    }

				}

			}
		}

		echo '<pre>'; print_r($data);
		echo '<pre>'; print_r($arr);


	}

	public function reset_session_log_count()
	{
		$settings = get_single_data_id('settings',1);
		echo '<pre>'; print_r($settings);
		$session_start_day = $settings->session_start_day;
		$session_start_month = $settings->session_start_month;
		$session_end_day = $settings->session_end_day;
		$session_end_month = $settings->session_end_month;
		if(date('m') == $session_start_month){
			/*Reset count on August and inactive all previous session logs*/
			DB::table('mentor_session_log_count')->update(['count'=>0]);
			DB::table('session')->update(['status'=>0]);
		}
	}

	public function db_backup()
	{
		// echo 'Hi';
		// echo date('H'); die;
		date_default_timezone_set('America/New_York'); // EST

		if(date('H') == '00'){
			// $tablenames = DB::select(DB::raw("SELECT table_name FROM information_schema.tables WHERE table_schema = 'tsicdev_db' AND table_name = 'video_chat_rooms'"));
			$tablenames = DB::select(DB::raw("SELECT table_name FROM information_schema.tables WHERE table_schema = 'tsicdev_db'"));
			if(!empty($tablenames)){
				foreach($tablenames as $t){
					$table_arr[] = $t->table_name;
				}
			}
			// echo '<pre>'; print_r($table_arr); die;
			//ENTER THE RELEVANT INFO BELOW
		    $mysqlUserName      = "root";
		    $mysqlPassword      = "?8H/7Sr+tWdVr5=h";
		    $mysqlHostName      = "localhost";
		    $DbName             = "tsicdev_db";
		    $backup_name        = "tsiclive_db_".date("Y-m-d").".sql";
		    $tables             = $table_arr;

		   //or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

		    // $mysqli = new mysqli($host,$user,$pass,$name);
		    $mysqli = mysqli_connect($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName);
	        $mysqli->select_db($DbName);
	        $mysqli->query("SET NAMES 'utf8'");

	        $queryTables    = $mysqli->query('SHOW TABLES');
	        while($row = $queryTables->fetch_row())
	        {
	            $target_tables[] = $row[0];
	        }
	        if($tables !== false)
	        {
	            $target_tables = array_intersect( $target_tables, $tables);
	        }
	        foreach($target_tables as $table)
	        {
	            $result         =   $mysqli->query('SELECT * FROM '.$table);
	            $fields_amount  =   $result->field_count;
	            $rows_num=$mysqli->affected_rows;
	            $res            =   $mysqli->query('SHOW CREATE TABLE '.$table);
	            $TableMLine     =   $res->fetch_row();
	            $content        = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

	            for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0)
	            {
	                while($row = $result->fetch_row())
	                { //when started (and every after 100 command cycle):
	                    if ($st_counter%100 == 0 || $st_counter == 0 )
	                    {
	                            $content .= "\nINSERT INTO ".$table." VALUES";
	                    }
	                    $content .= "\n(";
	                    for($j=0; $j<$fields_amount; $j++)
	                    {
	                        $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
	                        if (isset($row[$j]))
	                        {
	                            $content .= '"'.$row[$j].'"' ;
	                        }
	                        else
	                        {
	                            $content .= '""';
	                        }
	                        if ($j<($fields_amount-1))
	                        {
	                                $content.= ',';
	                        }
	                    }
	                    $content .=")";
	                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
	                    if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num)
	                    {
	                        $content .= ";";
	                    }
	                    else
	                    {
	                        $content .= ",";
	                    }
	                    $st_counter=$st_counter+1;
	                }
	            } $content .="\n\n\n";
	        }

	        $backup_name = $backup_name ? $backup_name : $DbName.".sql";

	        $file_path = storage_path('/app'.'/');
	        // $file_path = public_path('/db_backups'.'/');
	        $handle = fopen($file_path.$backup_name, 'w+');
			fwrite($handle, $content);
			fclose($handle);

            $storage_path  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();
            $local_url = $storage_path . $backup_name;

            // $filePathBucket = 'db_backups/' ;
            $filePathBucket = 'tsiclive/' ;
            $savetoS3 = Storage::disk('s3DBBucket')->putFileAs($filePathBucket, new File($local_url),$backup_name);
            // $savetoS3 = Storage::disk('s3')->putFileAs($filePathBucket, new File($local_url),$backup_name, 'public');

            unlink(storage_path('app/'.$backup_name));

	    	exit;

		}

	}

	public function fetch_video_duration()
	{
		$data = DB::table(VIDEO_CHAT_ROOMS)->where('duration','')->get()->toarray();

		if(!empty($data)){
			foreach($data as $d){
				$fetch_room = fetch_room($d->room_sid);
				$room = $fetch_room['room'];
				// $linkarr = $fetch_room['linkarr'];
				$status = $room->status;

				if($status == 'completed'){
					$duration = $room->duration;
					DB::table(VIDEO_CHAT_ROOMS)->where('id',$d->id)->update(['duration'=>$duration]);

					// $sum_video_chat_duration = DB::select(DB::raw("SELECT SUM(duration) AS sum_value FROM video_chat_rooms WHERE chat_code = '".$d->chat_code."' "));

					// $sum_value = $sum_video_chat_duration[0]->sum_value;

					$existing_chat_user = DB::table(VIDEO_CHAT_USER)->where('chat_code',$d->chat_code)->first();

					$remaining_time = $existing_chat_user->remaining_time;


					// echo '<pre>'; echo $remaining_time;

					$remaining_time = ($remaining_time - $duration);

					DB::table(VIDEO_CHAT_USER)->where('chat_code',$d->chat_code)->update(['remaining_time'=>$remaining_time,'updated_at'=>date('Y-m-d H:i:s')]);


				}
			}
		}

		echo '<pre>'; print_r($data);
	}

	public function reset_videochat_weekly()
	{
		date_default_timezone_set('America/New_York');

		if(date('D') == 'Sun' && date('H') == '22'){

			$settings = DB::table('settings')->first();
			$video_chat_duration = $settings->video_chat_duration;

			$total_duration_seconds = (60*$video_chat_duration); #... in seconds
			echo $total_duration_seconds;

			$video_chat_user = DB::table(VIDEO_CHAT_USER)->get()->toarray();

			if(!empty($video_chat_user)){
				foreach($video_chat_user as $vcu){

					$incompleted_chats = DB::select(DB::raw("SELECT SUM(duration) AS sum_value FROM video_chat_rooms WHERE chat_code = '".$vcu->chat_code."' AND is_week_completed = 0 "));

					$used = 0;
					if(!empty($incompleted_chats)){
						$used = $incompleted_chats[0]->sum_value;
					}

					$vcu->used = $used;
					$remaining = ($total_duration_seconds - $used);
					$vcu->remaining = $remaining;

					DB::table(VIDEO_CHAT_ROOMS)->where('chat_code',$vcu->chat_code)->update(['is_week_completed'=>1]);

					// $remaining = ($remaining < 0 ? 0 : $remaining);

					// DB::table('video_chat_week')->insert(['chat_code'=>$vcu->chat_code,'start_date'=>$start_week,'end_date'=>$end_week,'used'=>$used,'remaining'=>$remaining,'created_at' => date('Y-m-d H:i:s') ]);
				}
			}
			echo '<pre>'; print_r($video_chat_user);

			DB::table(VIDEO_CHAT_USER)->update(['remaining_time'=>$total_duration_seconds,'updated_at'=>date('Y-m-d H:i:s') ]);
		}


	}

	public function remove_video_chats()
	{
		# Delete 25 before all video rooms and recordings of each room ... At every 10 pm in crontab
		$data = $room_sids = array();

		$previous_date = date('Y-m-d H:i:s', strtotime('-30 days'));
		$data = DB::table('video_chat_rooms')->where('created_at', '<=', $previous_date)->get()->toarray();

		if(!empty($data)){
			foreach($data as $d){
				$room_sids[] = $d->room_sid;
				twilio_delete_recordings($d->room_sid);
			}
		}

		if(!empty($room_sids)){
			DB::table('video_chat_rooms')->whereIn('room_sid',$room_sids)->delete();
			DB::table('video_chat_participants')->whereIn('room_sid',$room_sids)->delete();
		}

		echo '<pre>'; print_r($data); die;
	}


}
