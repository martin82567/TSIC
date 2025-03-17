<?php
/***********Mentor***********/
namespace App\Http\Controllers\Api\V1\mentor;

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

class UserController extends Controller
{
    private $assigned_by;

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

            $chk_user = DB::table('mentor')->where('id',$this->user_id)->first();



            if(empty($chk_user)){
                response()->json(array('status'=>false,'message'=>"User not found"))->send();
                exit();
            }

            $this->assigned_by = $chk_user->assigned_by;
            mentor_last_activity($this->user_id);

        }catch (\Exception $e) {
            response()->json(array('status'=>false,'message'=>"Token is invalid"))->send();
            exit();
        }
    }

    public function logout(Request $request)
    {
        $empty_firebase = DB::table('mentor')->where('id',$this->user_id)->update(['firebase_id' => '','voip_device_token'=>'']);

        DB::table('waiver_acceptance')->where('user_type','mentor')->where('login_from', 'app')->where('user_id',$this->user_id)->where('status', 1)->update(['status'=>0]); /* Previous made not signed*/

        return response()->json(['status'=>true, 'message' => "You have successfully logged out", 'data' => array()]);

    }

    public function get_timezone(Request $request)
    {
        $affiliate_id = $this->assigned_by;

        if(!empty($affiliate_id)){
            $affiliate_data = DB::table('admins')->select('admins.id','admins.timezone','timezones.timezone_offset')->leftjoin('timezones', 'timezones.value','admins.timezone')->where('admins.id',$affiliate_id)->first();

            $timezone = !empty($affiliate_data->timezone)?$affiliate_data->timezone:'America/New_York';
            $timezone_offset = !empty($affiliate_data->timezone_offset)?$affiliate_data->timezone_offset:'-4';

            return response()->json(['status'=>true,'message'=>"This is timezone",'data'=>array('timezone' => $timezone, 'timezone_offset'=> $timezone_offset)]);
        }
    }

    public function update_tokens(Request $request) {

        $input = $request->all();

        $firebase_id = !empty($input['firebase_id'])?$input['firebase_id']:'';
        $voip_device_token = !empty($input['voip_device_token'])?$input['voip_device_token']:'';

        $data = [];
        if(!empty($firebase_id)) {
            $data['firebase_id'] = $firebase_id;
        }

        if(!empty($voip_device_token)) {
            $data['voip_device_token'] = $voip_device_token;
        }
        if ($firebase_id || $voip_device_token) {

            DB::table('mentee')->where('id', $this->user_id)->update(
                $data
            );

            return response()->json(['status'=>true, 'message' => 'Successfully updated firebase id and voip token.']);
        }else{
            return response()->json(['status'=>false, 'message' => "Error while updating."]);
        }

    }

}
