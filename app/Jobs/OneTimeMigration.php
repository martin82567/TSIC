<?php

namespace App\Jobs;

use App\Services\SalesforceDBService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OneTimeMigration implements ShouldQueue
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
    public function handle(SalesforceDBService $service)
    {
        ini_set("memory_limit",-1);
        ini_set('max_execution_time', 15000);

        try {
            $service->authenticate();

            $this->migrateOffices($service);

            $this->migrateSchools($service);

            $this->migrateMentors($service);

            $this->migrateMentees($service);

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    private function migrateOffices(SalesforceDBService $service)
    {
        try {
            $allOffices = $service->getLegacyOffices();
            foreach ($allOffices as $office) {
                $idParts = explode('-', $office->Legacy_ID__c);
                $salesForceId = $office->Id;
//                dd(DB::table('admins')->select('*')->where('stardbID', $idParts[1])->get());
                DB::table('admins')
                    ->where('stardbID', $idParts[1])
                    ->update(['externalId' => $salesForceId, 'externalName' => $office->Name, 'affiliateName' => $office->Affiliate__c, 'creationmethod'  =>  'salesforceapi']);
            }
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    private function migrateSchools(SalesforceDBService $service)
    {
        try {
            $allSchools = $service->getLegacySchools();

//            Log::info(count($allSchools) . " schools to process");
            foreach ($allSchools as $school) {
                $idParts = explode('-', $school->Legacy_ID__c);
                $salesForceId = $school->Id;
//                Log::info("updating school..." . $idParts[1] . "..." . $salesForceId);

                DB::table('school')
                    ->where('stardbID', $idParts[1])
                    ->update(['externalId' => $salesForceId, 'creationmethod'  =>  'salesforceapi']);

            }

//            Log::info("All Schools processed");

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    private function migrateMentors(SalesforceDBService $service)
    {
        try {
            $allMentors = $service->getLegacyMentors();

//            Log::info(count($allMentors) . " mentors to process");
            foreach ($allMentors as $mentor) {


                $idParts = explode('-', $mentor->Legacy_ID__c);
                $salesForceId = $mentor->Id;

                DB::table('mentor')
                    ->where('stardbID', $idParts[1])
                    ->update(['externalId' => $salesForceId, 'externalOfficeId' => $mentor->AccountId, 'externalOfficeName' => $mentor->Affiliate__c, 'creationmethod'  =>  'salesforceapi']);

            }

//            Log::info("All mentors processed");
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    private function migrateMentees(SalesforceDBService $service)
    {
        try {
            $allStudents = $service->getLegacyStudents();

            foreach ($allStudents as $student) {

                $idParts = explode('-', $student->Legacy_ID__c);
                $salesForceId = $student->Id;

                DB::table('mentee')
                    ->where('stardbID', $idParts[1])
                    ->update(['externalId' => $salesForceId, 'externalSchoolId' => $student->npsp__Primary_Affiliation__c, 'externalSchoolName' => "", 'creationmethod'  =>  'salesforceapi']);

            }

        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }
}
