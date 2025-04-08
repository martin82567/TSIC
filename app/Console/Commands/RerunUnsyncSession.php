<?php

namespace App\Console\Commands;

use App\SessionRerunLog;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;

class RerunUnsyncSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rerun:session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rerun unsync session to the Hopeforce';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $sessions = DB::table('session')->where('creationmethod', 'adminpanel')->where('created_date', '>=', '2024-08-01')->get();
            
            foreach ($sessions as $session) {
                Log::debug("Session ID: ".print_r($session->id, true));

                try {
                    $mentee_id = !empty($session->mentee_id) ? $session->mentee_id : '';
                    $mentor_id = !empty($session->mentor_id) ? $session->mentor_id : '';
                    $mentee_data = get_single_data_id('mentee', $mentee_id);
                    $mentee_externalId = $mentee_data->externalId;
                    $mentor_data = get_single_data_id('mentor', $mentor_id);
                    $externalId = $mentor_data->externalId;
                    $creationMethod = $mentor_data->creationmethod;
                    $affiliate_data = get_single_data_id('admins', $mentor_data->assigned_by);
                    $schedule_date = !empty($session->schedule_date) ? $session->schedule_date : '';
                    $time_duration = !empty($session->time_duration) ? $session->time_duration : '';
                    $name = !empty($session->name) ? $session->name : '';
                    $type = !empty($session->type) ? $session->type : '';
                    $session_method_location_id = !empty($session->session_method_location_id) ? $session->session_method_location_id : '';
                    $session_method_location = DB::table('session_method_location')->where('id', $session_method_location_id)->first();
                    $method_id = $session_method_location->method_id;


                    if ($creationMethod == 'salesforceapi' && !empty($externalId) && !empty($mentee_externalId)) {
                        if ($affiliate_data->externalId) {
                            $fiscalYear = getFiscalYear($schedule_date);
                            $serviceRecord = DB::table('agency_programs')->select('service_id')->where('agency_id', $affiliate_data->id)->where('year', $fiscalYear)->where('active', 1)->first();
                            
                            if ($serviceRecord && $serviceRecord->service_id) {
                                $data = array(
                                    "serviceID" => $serviceRecord->service_id,
                                    "studentID" => $mentee_data->externalId,
                                    "mentorID" => $mentor_data->externalId,
                                    "sessionDate" => $schedule_date,
                                    "sessionDuration" => (int)$time_duration,
                                    "sessionNote" => $name,
                                    "sessionTypeID" => (int)$type,
                                    "sessionSourceID" => 2,
                                    "sessionLocationID" => $method_id
                                );
                                // Log::debug("Data: ".print_r($data, true));

                                $loginResponse = loginToSalesforce();
                                // Log::debug("loginResponse: ".print_r($loginResponse, true));

                                $token = $loginResponse->token;
                                $baseURL = $loginResponse->baseURL;

                                $response = createSession($data, $token, $baseURL);
                                // Log::debug("API response 1: ".print_r($response, true));

                                DB::table('session')->where('id', $session->id)
                                    ->update([
                                        'externalId' => $response->id, 
                                        'creationmethod' => 'salesforceapi', 
                                        'error_count' => 0
                                    ]);

                                SessionRerunLog::insert([
                                    'session_id' => $session->id,
                                    'externalId' => $response->id, 
                                    'status' => 1,
                                    'created_at' => Carbon::now()
                                ]);
                            
                            } else {
                                if (SessionRerunLog::where('status', 0)->where('session_id', $session->id)->exists()) {
                                    continue;
                                }

                                SessionRerunLog::insert([
                                    'session_id' => $session->id,
                                    'error_reason' => 'Custom Error: serviceRecord is empty.',
                                    'created_at' => Carbon::now()
                                ]);
                            }
                        } else {
                            if (SessionRerunLog::where('status', 0)->where('session_id', $session->id)->exists()) {
                                continue;
                            }

                            SessionRerunLog::insert([
                                'session_id' => $session->id,
                                'error_reason' => 'Custom Error: Admin externalId is empty.',
                                'created_at' => Carbon::now()
                            ]);
                        }
                    } else {
                        if (SessionRerunLog::where('status', 0)->where('session_id', $session->id)->exists()) {
                            continue;
                        }
                        
                        SessionRerunLog::insert([
                            'session_id' => $session->id,
                            'error_reason' => 'Custom Error: Mentor data not present in Hopeforce.',
                            'created_at' => Carbon::now()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to process session ID: {$session->id}. Error: " . $e->getMessage());
                    
                    if (SessionRerunLog::where('status', 0)->where('session_id', $session->id)->exists()) {
                        continue;
                    }
                    SessionRerunLog::insert([
                        'session_id' => $session->id,
                        'error_reason' => $e->getMessage(),
                        'created_at' => Carbon::now()
                    ]);
                }
            }

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }
}
