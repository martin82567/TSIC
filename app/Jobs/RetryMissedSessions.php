<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetryMissedSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $sessions = DB::table('session')->where('creationmethod', '!=', 'salesforceapi')->where('created_date', '>=', '2024-08-01')->where('error_count', '<', 3)->get();
            foreach ($sessions as $session) {
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
                                $loginResponse = loginToSalesforce();
                                $token = $loginResponse->token;
                                $baseURL = $loginResponse->baseURL;
                                $response = createSession($data, $token, $baseURL);
                                DB::table('session')->where('id', $session->id)->update(['externalId' => $response->id, 'creationmethod' => 'salesforceapi', 'error_count' => 0]);
                            } else {
                                DB::table('session')->where('id', $session->id)->update([
                                    'error_count' => DB::raw('error_count + 1'),
                                ]);
                            }
                        } else {
                            DB::table('session')->where('id', $session->id)->update([
                                'error_count' => DB::raw('error_count + 1'),
                            ]);
                        }
                    } else {
                        DB::table('session')->where('id', $session->id)->update([
                            'error_count' => DB::raw('error_count + 1'),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to process session ID: {$session->id}. Error: " . $e->getMessage());
                    DB::table('session')
                        ->where('id', $session->id)
                        ->update([
                            'error_count' => DB::raw('error_count + 1'),
                        ]);

                }
            }

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }
}
