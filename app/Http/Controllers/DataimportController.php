<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

use Importer;

class DataimportController extends Controller
{
    /*******************************************************/
    /******* This function is to fetch All schools ********/
    /*******************************************************/
    public function schoolentry($token)
    {
        $loginResponse = loginToSalesforce();

        $token = $loginResponse->token;
        $baseURL = $loginResponse->baseURL;

        ini_set("memory_limit", -1);
        $admins = DB::table('admins')->select('externalId', 'id', 'affiliateName')->where('type', 2)->where('creationmethod', 'salesforceapi')->get();
        $schoollistarr = array();
        $count = 0;
        $school_id_ar = array();
        if (!empty($admins)) {
            foreach ($admins as $agency) {
                $schools = collect(getSchoolsForOffice($agency->affiliateName, $token, $baseURL));

                if (!empty($schools)) {
                    $school_index = 0;
                    foreach ($schools as $school) {
                        $school_id_ar[] = $school->schoolID;

                        $schoollistarr[] = $school;

                        $school->agency_id = $agency->id;

                        $school_exist = DB::table('school')->where('externalId', $school->schoolID)->first();
                        if (!empty($school_exist)) {
                            DB::table('school')
                                ->where('id', $school_exist->id)
                                ->update([
                                    'name' => $school->schoolName,
                                    'agency_id' => $agency->id,
                                    'externalId' => $school->schoolID,
                                    'addressID' => 0,
                                    'city' => $school->city,
                                    'state' => $school->state,
                                    'zip' => $school->zip,
                                    'address' => $school->street,
                                    'latitude' => $school->latitude,
                                    'longitude' => $school->longitude,
                                    'creationmethod' => 'salesforceapi',

                                ]);

                            $inserted_data = array('name' => $school->schoolName, 'agency_id' => $agency->id, 'externalId' => $school->schoolID, 'addressID' => $school->addressID, 'status' => 1, 'creationmethod' => 'salesforceapi');

                            if ($school->schoolName != $school_exist->name) {
                                $data_insert = array('data' => json_encode($inserted_data), 'type' => 'update', 'table_name' => 'school');
                                DB::table('crontracker')->insert($data_insert);
                            }
                        } else {
                            $inserted_data = array(
                                'name' => $school->schoolName,
                                'agency_id' => $agency->id,
                                'externalId' => $school->schoolID,
                                'addressID' => 0,
                                'city' => $school->city,
                                'state' => $school->state,
                                'zip' => $school->zip,
                                'address' => $school->street,
                                'latitude' => $school->latitude,
                                'longitude' => $school->longitude,
                                'creationmethod' => 'salesforceapi',
                            );
                            DB::table('school')->insert($inserted_data);

                            $data_insert = array('data' => json_encode($inserted_data), 'type' => 'create', 'table_name' => 'school');
                            DB::table('crontracker')->insert($data_insert);
                        }

                        $school_id_ar[] = $inserted_data;
                    }
                }
            }
        }
        dd($school_id_ar);
    }

    /*******************************************************************/
    /******* This function is to fetch All Mentors and Students ********/
    /*******************************************************************/
    public function gettoken()
    {

        $loginResponse = loginToSalesforce();

        $token = $loginResponse->token;
        $baseURL = $loginResponse->baseURL;

        ini_set("memory_limit", -1);
        ini_set('max_execution_time', 300);
        $admins = DB::table('admins')->select('externalId', 'id', 'externalName', 'affiliateName')->where('creationmethod', 'salesforceapi')->where('is_inserted', 0)->limit(1)->get();

        $schoollistarr = array();
        $inserted_data = array();
        if (!empty($admins->toarray())) {
            foreach ($admins as $agency) {
                // Mentor List

                $mentorlist = collect(getMentorsForOffice($agency->affiliateName, $token, $baseURL));

                if (!empty($mentorlist)) {
                    foreach ($mentorlist as $mentor) {

                        try {

                            $school_exist = DB::table('mentor')->select('*')->where('externalId', $mentor->mentorID)->first();
                            if (empty($school_exist)) {

                                $inserted_data = array('created_by' => $agency->id, 'assigned_by' => $agency->id, 'password' => bcrypt("Welcome123"), 'firstname' => !empty($mentor->firstName) ? $mentor->firstName : '', 'middlename' => !empty($mentor->middleName) ? $mentor->middleName : '', 'lastname' => !empty($mentor->lastName) ? $mentor->lastName : '', 'email' => !empty($mentor->emailAddress) ? $mentor->emailAddress : '', 'is_active' => $mentor->mentorStatusID, 'stardbofficeID' => $mentor->officeID, 'externalId' => $mentor->mentorID, 'externalOfficeId' => $mentor->externalOfficeId, 'creationmethod' => 'salesforceapi', 'phone' => !empty($mentor->mobilePhoneNumber) ? $mentor->mobilePhoneNumber : '');

                                DB::table('mentor')->insert($inserted_data);

                                $data_insert = array('data' => json_encode($inserted_data), 'type' => 'create', 'table_name' => 'mentor');
                                DB::table('crontracker')->insert($data_insert);
                            } else {
                                $inserted_data = array('created_by' => $agency->id, 'assigned_by' => $agency->id, 'stardbofficeID' => $mentor->officeID, 'externalOfficeId' => $mentor->externalOfficeId, 'firstname' => !empty($mentor->firstName) ? $mentor->firstName : '', 'middlename' => !empty($mentor->middleName) ? $mentor->middleName : '', 'lastname' => !empty($mentor->lastName) ? $mentor->lastName : '', 'email' => !empty($mentor->emailAddress) ? $mentor->emailAddress : '', 'phone' => !empty($mentor->mobilePhoneNumber) ? $mentor->mobilePhoneNumber : '', 'is_active' => $mentor->mentorStatusID);

                                $affected = DB::table('mentor')
                                    ->where('externalId', $mentor->mentorID)
                                    ->update($inserted_data);

                                if (($mentor->firstName != $school_exist->firstname) || ($mentor->middleName != $school_exist->middlename) || ($mentor->lastName != $school_exist->lastname) || ($mentor->emailAddress != $school_exist->email) || ($mentor->mobilePhoneNumber != $school_exist->phone) || ($agency->id != $school_exist->assigned_by) || ($mentor->officeID != $school_exist->stardbofficeID) || ($mentor->externalOfficeId != $school_exist->externalOfficeId) || ($mentor->mentorStatusID != $school_exist->is_active)) {
                                    $data_insert = array('data' => json_encode($inserted_data), 'type' => 'update', 'table_name' => 'mentor');
                                    DB::table('crontracker')->insert($data_insert);
                                }
                            }
                        } catch (GuzzleException $e) {
                            Log::info($e->getMessage());
                        }
                    }
                }

                // Student List

                $studentList = collect(getStudentsForOffice($agency->affiliateName, $token, $baseURL));

                if (!empty($studentList)) {
                    foreach ($studentList as $student) {

                        try {
                            $mentee_data[] = $student->emailAddress;
                            $school_exist = DB::table('school')->select('id')->where('externalId', $student->schoolID)->first();
                            if (!empty($student->schoolID)) {

                                $mentee_exist = DB::table('mentee')->select('*')->where('externalId', $student->studentID)->first();

                                if (empty($mentee_exist)) {
                                    $inserted_data = array('assigned_by' => $agency->id, 'password' => bcrypt("Welcome123"), 'school_id' => !empty($school_exist->id) ? $school_exist->id : 0, 'firstname' => !empty($student->firstName) ? $student->firstName : '', 'middlename' => !empty($student->middleName) ? $student->middleName : '', 'lastname' => !empty($student->lastName) ? $student->lastName : '', 'email' => !empty($student->emailAddress) ? $student->emailAddress : '', 'created_by' => 1, 'status' => $student->studentStatusID, 'externalId' => $student->studentID, 'schoolID' => $student->schoolID, 'creationmethod' => 'salesforceapi');
                                    $mentee_id = DB::table('mentee')->insertGetId($inserted_data);

                                    $data_insert = array('data' => json_encode($inserted_data), 'type' => 'create', 'table_name' => 'mentee');
                                    DB::table('crontracker')->insert($data_insert);

                                } else {
                                    $mentee_id = $mentee_exist->id;

                                    $inserted_data = array('firstname' => !empty($student->firstName) ? $student->firstName : '', 'assigned_by' => $agency->id, 'school_id' => !empty($school_exist->id) ? $school_exist->id : 0, 'schoolID' => $student->schoolID, 'middlename' => !empty($student->middleName) ? $student->middleName : '', 'lastname' => !empty($student->lastName) ? $student->lastName : '', 'email' => !empty($student->emailAddress) ? $student->emailAddress : '', 'status' => $student->studentStatusID);

                                    DB::table('mentee')
                                        ->where('externalId', $student->studentID)
                                        ->update($inserted_data);

                                    if (($student->firstName != $mentee_exist->firstname) || ($student->middleName != $mentee_exist->middlename) || ($student->lastName != $mentee_exist->lastname) || ($student->emailAddress != $mentee_exist->email)) {
                                        $data_insert = array('data' => json_encode($inserted_data), 'type' => 'update', 'table_name' => 'mentee');
                                        DB::table('crontracker')->insert($data_insert);
                                    }
                                }
                            }
                        } catch (GuzzleException $e) {
                            Log::info($e->getMessage());
                        }
                    }
                }

                // Scholar List

                $scholarList = collect(getScholarsForOffice($agency->affiliateName, $token, $baseURL));

                if (!empty($scholarList)) {
                    foreach ($scholarList as $scholar) {

                        try {
                            $mentee_data[] = $scholar->emailAddress;
                            $school_exist = DB::table('school')->select('id')->where('externalId', $scholar->schoolID)->first();
                            if (!empty($scholar->schoolID)) {

                                $mentee_exist = DB::table('mentee')->select('*')->where('externalId', $scholar->studentID)->first();

                                if (empty($mentee_exist)) {
                                    $inserted_data = array('assigned_by' => $agency->id, 'password' => bcrypt("Welcome123"), 'school_id' => !empty($school_exist->id) ? $school_exist->id : 0, 'firstname' => !empty($scholar->firstName) ? $scholar->firstName : '', 'middlename' => !empty($scholar->middleName) ? $scholar->middleName : '', 'lastname' => !empty($scholar->lastName) ? $scholar->lastName : '', 'email' => !empty($scholar->emailAddress) ? $scholar->emailAddress : '', 'created_by' => 1, 'status' => $scholar->studentStatusID, 'externalId' => $scholar->studentID, 'schoolID' => $scholar->schoolID, 'creationmethod' => 'salesforceapi');
                                    $mentee_id = DB::table('mentee')->insertGetId($inserted_data);

                                    $data_insert = array('data' => json_encode($inserted_data), 'type' => 'create', 'table_name' => 'mentee');
                                    DB::table('crontracker')->insert($data_insert);

                                } else {
                                    $mentee_id = $mentee_exist->id;

                                    $inserted_data = array('firstname' => !empty($scholar->firstName) ? $scholar->firstName : '', 'assigned_by' => $agency->id, 'school_id' => !empty($school_exist->id) ? $school_exist->id : 0, 'schoolID' => $scholar->schoolID, 'middlename' => !empty($scholar->middleName) ? $scholar->middleName : '', 'lastname' => !empty($scholar->lastName) ? $scholar->lastName : '', 'email' => !empty($scholar->emailAddress) ? $scholar->emailAddress : '', 'status' => $scholar->studentStatusID);

                                    DB::table('mentee')
                                        ->where('externalId', $scholar->studentID)
                                        ->update($inserted_data);

                                    if (($scholar->firstName != $mentee_exist->firstname) || ($scholar->middleName != $mentee_exist->middlename) || ($scholar->lastName != $mentee_exist->lastname) || ($scholar->emailAddress != $mentee_exist->email)) {
                                        $data_insert = array('data' => json_encode($inserted_data), 'type' => 'update', 'table_name' => 'mentee');
                                        DB::table('crontracker')->insert($data_insert);
                                    }
                                }
                            }
                        } catch (GuzzleException $e) {
                            Log::info($e->getMessage());
                        }
                    }
                }

                DB::table('admins')->where('id', $agency->id)->update(['is_inserted' => 1]);

                try {
                    $this->mentee_mentor_data_import();
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            }

        } else {
            DB::table('admins')->update(['is_inserted' => 0]);
        }
    }

    /*******************************************************************/
    /******* This function is to get matches for Mentor-Student ********/
    /*******************************************************************/
    public function mentee_mentor_data_import()
    {
        $loginResponse = loginToSalesforce();

        $token = $loginResponse->token;
        $baseURL = $loginResponse->baseURL;

        ini_set("memory_limit", -1);
        ini_set('max_execution_time', 300);
//        $mentee = DB::table('mentee')->select('externalId', 'id')->where('creationmethod', 'salesforceapi')->where('externalId', '!=', '')->where('is_inserted', 0)->limit(1000)->get();
        $studentList = collect(getStudentMatchForMentor($token, $baseURL));
//        $schoollistarr = array();
//        $inserted_data = array();
//        if (!empty($mentee->toarray())) {
//            foreach ($mentee as $student) {
//                $studentList = collect(getStudentMatchForMentor($token, $baseURL));
//
//                $mentee_id = $student->id;
//
//                $currentAssignedMentors = DB::table('assign_mentee')
//                    ->where('mentee_id', $mentee_id)
//                    ->pluck('assigned_by')
//                    ->toArray();
//
//                $activeMentorIDs = [];
//
        if (!empty($studentList)) {
            foreach ($studentList as $assign_mentee) {
                $mentee_external_id = $assign_mentee->studentID;
                if (!empty($mentee_external_id)) {
                    $mentee_id = DB::table('mentee')->where('externalId', $mentee_external_id)->first();
                    if (!empty($mentee_id)) {
                        $mentor_exist = DB::table('mentor')->select('id')->where('externalId', $assign_mentee->mentorID)->first();
                        $created_date = date('Y-m-d h:i:s', strtotime($assign_mentee->assignedDate));

                        if (!empty($mentor_exist)) {
                            if ($assign_mentee->mentorAssignmentTypeID == 1) {

                                $assign_mentee_exist = DB::table('assign_mentee')->select('*')->where('mentee_id', $mentee_id->id)->where('assigned_by', $mentor_exist->id)->first();

                                if (empty($assign_mentee_exist)) {
                                    $inserted_data_assign = array('mentee_id' => $mentee_id->id, 'assigned_by' => $mentor_exist->id, 'created_date' => $created_date, 'is_primary' => !empty($assign_mentee->isPrimary) ? 1 : 0);
                                    DB::table('assign_mentee')->insert($inserted_data_assign);

                                    $data_insert = array('data' => json_encode($inserted_data_assign), 'type' => 'create', 'table_name' => 'assign_mentee');
                                    DB::table('crontracker')->insert($data_insert);

                                } else {

                                    $inserted_data = array('is_primary' => !empty($assign_mentee->isPrimary) ? 1 : 0);

                                    DB::table('assign_mentee')
                                        ->where('id', $assign_mentee_exist->id)
                                        ->update($inserted_data);
                                }
                            } else {
                                $assign_mentee_exist = DB::table('assign_mentee')
                                    ->where('mentee_id', $mentee_id->id)
                                    ->where('assigned_by', $mentor_exist->id)
                                    ->first();

                                if (!empty($assign_mentee_exist)) {
                                    // Delete the inactive assignment
                                    DB::table('assign_mentee')
                                        ->where('id', $assign_mentee_exist->id)
                                        ->delete();

                                    // Track changes
                                    $data_delete = [
                                        'data' => json_encode($assign_mentee_exist),
                                        'type' => 'delete',
                                        'table_name' => 'assign_mentee',
                                    ];
                                    DB::table('crontracker')->insert($data_delete);
                                }
                            }
                        }
//                        else {
//                            $mentor_exist = DB::table('mentor')->select('id')->where('externalId', $assign_mentee->mentorID)->first();
//                            if (!empty($mentor_exist)) {
//                                $assign_mentee_exist = DB::table('assign_mentee')->select('*')->where('mentee_id', $mentee_id)->where('assigned_by', $mentor_exist->id)->first();
//                                if (!empty($assign_mentee_exist)) {
//                                    DB::table('assign_mentee')
//                                        ->where('id', $assign_mentee_exist->id)
//                                        ->delete();
//                                    $data_insert = array('data' => json_encode($assign_mentee_exist), 'type' => 'delete', 'table_name' => 'assign_mentee');
//                                    DB::table('crontracker')->insert($data_insert);
//                                }
//                            }
//                        }

                        DB::table('mentee')->where('id', $mentee_id->id)->update(['is_inserted' => 1]);
                    }
                }
            }
            $successful_updated_at = Carbon::now()->toIso8601ZuluString();
            DB::table('settings')->where('id', 1)->update(['successful_updated_at' => $successful_updated_at]);
            //DB::table('mentee')->update(['is_inserted' => 1]);
        } else {
            DB::table('mentee')->update(['is_inserted' => 0]);
        }
    }

    public
    function mentoringsessions()
    {
        /* $url = 'https://unison.tsic.org/api/Authentication';
         $fields = array(
             'username' => urlencode('webservices'),
             'password' => urlencode('8tQPNoO5tA')
         );
         $fields_string = '';
         //url-ify the data for the POST
         foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
         rtrim($fields_string, '&');

         //open connection
         $ch = curl_init();

         //set the url, number of POST vars, POST data
         curl_setopt($ch,CURLOPT_URL, $url);
         curl_setopt($ch,CURLOPT_POST, count($fields));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
         curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));



         //execute post
         $token_res = curl_exec($ch);

         $token_arr = json_decode($token_res);

         //close connection
         curl_close($ch);

         $token = $token_arr->token;



         ini_set("memory_limit",-1);
         ini_set('max_execution_time', 300);
         $admins = DB::table('mentor')->select('stardbID','id')->where('creationmethod' , 'stardbapi')->where('stardbID','!=', 0)->where('is_inserted', 0)->limit(1000)->get();
         $schoollistarr = array();
         $inserted_data = array();
         if(!empty($admins->toarray())){
             foreach($admins as $agency){
                 $cURLConnection = curl_init();
                 $url = 'https://unison.tsic.org/api/MentoringSessions/GetByMentorId/'.$agency->stardbID;

                 curl_setopt($cURLConnection, CURLOPT_URL,$url);
                 curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                 curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
                         'Authorization: Bearer '.$token
                     ));

                 $dataresponce = curl_exec($cURLConnection);
                 $http_status = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
                 curl_close($cURLConnection);

                 $mentorlist = json_decode($dataresponce);

                 if(!empty($mentorlist)){
                     foreach($mentorlist as $student){
                         $school_exist = DB::table('mentee')->select('id')->where('stardbID', $student->studentID)->first();
                         if(!empty($school_exist)){

                             $session_exist = DB::table('session')->select('*')->where('stardbID', $student->mentoringSessionID)->first();
                             if(!empty($session_exist)){
                                 $inserted_data = array('name' => !empty($student->sessionNote)?$student->sessionNote:'', 'schedule_date' => date('Y-m-d', strtotime($student->sessionDate)), 'time_duration' => $student->sessionDuration, 'stardbID' => $student->mentoringSessionID, 'type' => $student->sessionTypeID, 'session_method_location_id' => $student->sessionLocationID + 1);

                                 if(empty($session_exist->is_created_by_app)){
                                     DB::table('session')
                                           ->where('stardbID', $student->mentoringSessionID)
                                           ->update($inserted_data);

                                     if($session_exist->name != $student->sessionNote || $session_exist->schedule_date != date('Y-m-d', strtotime($student->sessionDate)) || $session_exist->time_duration != $student->sessionDuration || $session_exist->type != $student->sessionTypeID || $session_exist->session_method_location_id != $student->sessionLocationID +1){
                                         $data_insert = array('data' => json_encode($inserted_data), 'type' => 'update', 'table_name' => 'session');
                                         DB::table('crontracker')->insert($data_insert);
                                     }
                                 }


                             }else{
                                 $inserted_data = array('name' => !empty($student->sessionNote)?$student->sessionNote:'', 'schedule_date' => date('Y-m-d', strtotime($student->sessionDate)), 'time_duration' => $student->sessionDuration, 'mentee_id' => $school_exist->id, 'mentor_id' => $agency->id, 'status' => 1, 'stardbID' => $student->mentoringSessionID, 'type' => $student->sessionTypeID, 'session_method_location_id' => $student->sessionLocationID + 1, 'creationmethod' => 'stardbapi');
                                 DB::table('session')->insert($inserted_data);

                                 $data_insert = array('data' => json_encode($inserted_data), 'type' => 'create', 'table_name' => 'session');
                                 DB::table('crontracker')->insert($data_insert);
                             }
                         }

                     }
                 }

                 DB::table('mentor')->where('id' ,  $agency->id)->update(['is_inserted' => 1]);

             }
         }else{
             DB::table('mentor')->update(['is_inserted' => 0]);
         } */
    }

    public
    function testroute()
    {
        echo "testroute1";
    }

    public
    function syncAgencyPrograms()
    {

        $loginResponse = loginToSalesforce();

        $token = $loginResponse->token;
        $baseURL = $loginResponse->baseURL;

        $allOffices = DB::table('admins')->select('externalId', 'id')->where('type', 2)->where('creationmethod', 'salesforceapi')->get();
//        return $allOffices;

        try {
            foreach ($allOffices as $office) {

//                Log::info("Processing $office->externalId");
                $services = collect(getProgramsAndServicesForAgency($office->externalId, $token, $baseURL));

                foreach ($services as $service) {
                    // Check if this service exists in DB
                    $serviceRecord = DB::table('agency_programs')->select('id')->where('service_id', $service->service_id)->first();

                    if ($serviceRecord) {
                        // Service updated
                        DB::table('agency_programs')
                            ->where('id', $serviceRecord->id)
                            ->update(['agency_id' => $office->id, 'year' => $service->fiscal_year, 'program_id' => $service->program_id, 'service_id' => $service->service_id, 'active' => $service->active]);

                    } else {
                        DB::table('agency_programs')
                            ->insert(['agency_id' => $office->id, 'year' => $service->fiscal_year, 'program_id' => $service->program_id, 'service_id' => $service->service_id, 'active' => $service->active]);
                    }

                }
            }
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

}
