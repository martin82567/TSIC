<?php


namespace App\Services;


use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesforceDBService implements ExternalDBService
{
    public $token;
    public $baseURL;

    const SESSION_TYPE_MAPPING = [
        '1' => 'Group',
        '2' => 'Individual',
    ];
    const SESSION_LOCATION_MAPPING = [
        '0' => 'In Person',
        '1' => 'Virtual',
        '2' => 'App-Chat',
        '3' => 'App-Video',
    ];
    const SESSION_SOURCE_MAPPING = [
        '1' => 'TSIC Mobile App',
        '2' => 'Mentor Portal',
        '3' => 'Staff Entry',
        '4' => 'Staff Import',
    ];

    const MENTOR_STATUS_MAPPING = [
        'Mentor - Active' => 1,
        'Applicant - Applied' => 2,
        'Mentor - Dormant' => 3,
        'Closed - Application - Ineligible (Background Check Fail)' => 4,
        'Mentor - On Hold' => 5,
        'Applicant - Ready to Match' => 6,
        'Applicant - Dormant' => 7,
        'Closed - Application - Ineligible (Behavior)' => 10,
        'Applicant - Trained' => 11,
        'Closed - Deceased' => 12,
        'Mentor Lead - New' => 13,
        'Applicant - Completed Background' => 14,
        'Closed - Mentor Dismissed (Behavior)' => 15,
        'Closed - Mentor Dismissed (Crime)' => 16,
        'Closed - Mentor Dismissed (Other)' => 17,
        'Closed - Withdrawn Mentor' => 18,
        'Other - Other' => 99,
    ];
//    const MENTOR_STATUS_MAPPING = [
//        'Mentor - Active - Active'   =>   1,    // Good
//        'Applicant - Applied - Active'   =>   2, // Good
//        'Mentor - Dormant - Inactive'   =>   3, // Good
//        'Closed - Application - Ineligible (Background Check Fail) - Active'   =>   4,  //Good
//        'Mentor - On Hold - Active'   =>   5, //Good
//        'Applicant - Ready to Match - Active'   =>   6, //Good
//        'Applicant - Dormant - Active'   =>   7,    // Good
//        'Closed - Application - Ineligible (Behavior) - Active'   =>   10, //Good
//        'Applicant - Trained - Active'   =>   11,
//        'Closed - Deceased - Inactive'   =>   12,
//        'Mentor Lead - New - Active'   =>   13,
//        'Applicant - Completed Background - Active'   =>   14,
//        'Closed - Mentor Dismissed (Behavior) - Inactive'   =>   15,
//        'Closed - Mentor Dismissed (Crime) - Inactive'   =>   16,
//        'Closed - Mentor Dismissed (Other) - Inactive'   =>   17,
//        'Closed - Withdrawn Mentor - Inactive'   =>   18,
//        'Other - Other - Inactive'   =>   99,
//    ];
//    const MENTEE_STATUS_MAPPING = [
//        'Children Program - Active'   =>   1,
//        'Children Program - Dormant'   =>   2,
//        'Children Program - Probation'   =>   3,
//        'Children Program - Transfer'   =>   4,
//        'Children Program - Probation - Warning'   =>  5,
//        'Children Program - Applied'   =>   6,
//        'Closed - Applicant - Ineligible'   =>   7,
//        'Closed - Applicant - Not Selected'   =>   8,
//        'Children Program - Processed - Ready'   =>  9,
//        'Children Program - Processed - Waitlist'   =>  10,
//        'Children Program - College Transition - HS Grad'   =>  11,
//        'Children Program - Alumni - Grad Finished'   =>  12,
//        'Children Program - College Enrolled'   =>  13,
//        'Children Program - Inactive'   =>  14,
//        'Children Program - On Hold'   =>  15,
//        'Closed - Mentee - Terminated - Deceased'   =>  19,
//        'Closed - Mentee - Terminated - Attendance'   =>  20,
//        'Closed - Mentee - Terminated - Behavior'   =>  21,
//        'Closed - Mentee - Terminated - GPA'   =>  22,
//        'Closed - Mentee - Terminated - Resigned'   =>  23,
//        'Closed - Mentee - Terminated - Withdrawn'   =>  24,
//        'Closed - Mentee - Terminated'   =>  25,
//        'College Program - Alumni - Grad School'   =>  28,
//        'Unknown'   =>   99,
//    ];

    const MENTEE_STATUS_MAPPING = [
        'TSIChildren Student - Active' => 1,        //...
        'TSIChildren Student - Dormant' => 2,       //.
        'TSIChildren Student - Probation' => 3,     //...
        'TSIChildren Student - Transfer' => 4,      //....
//        'TSIChildren Student - Warning'   =>  5,        // This is not applicable anymore
        'Applicant - Applied' => 6,                 //...
        'Applicant - Other' => 7,     //...
//        'Closed - Applicant - Not Selected'   =>   8,     //NA
        'Applicant - Processed - Ready' => 9,        //..
        'Applicant - Processed - Waitlist' => 10,    //..
        'Children Program - College Transition - HS Grad' => 11,
        'Children Program - Alumni - Grad Finished' => 12,
        'Children Program - College Enrolled' => 13,
        'Children Program - Inactive' => 14,
        'Children Program - On Hold' => 15,
//        'Applicant - Other'   =>   16,     //...
//        'Applicant - Other'   =>   17,     //...
//        'Applicant - Other'   =>   18,     //...
        'TSIChildren Student - Terminated - Deceased' => 19,
        'TSIChildren Student - Terminated - Attendance' => 20,
        'TSIChildren Student - Terminated - Behavior' => 21,
        'TSIChildren Student - Terminated - Grades' => 22,
        'TSIChildren Student - Terminated - Resigned' => 23,
        'TSIChildren Student - Terminated - Withdrawn' => 24,
        'Closed - Mentee - Terminated' => 25,
        'College Program - Alumni - Grad School' => 28,
        'Applicant - Processing' => 29,     //..
        'Applicant - Processed' => 30,   //..
        'Unknown' => 99,
        'TSICollege Scholar' => 12,
    ];

    public function authenticate()
    {
        $httpClient = new GuzzleClient();
        $clientID = env('SF_CLIENT_ID');
        $clientSecret = env('SF_CLIENT_SECRET');
        $loginURL = env('SF_LOGIN_URL');
        $username = env('SF_USERNAME');
        $password = env('SF_PASSWORD');

        try {
            $settings = DB::table('settings')->where('id', 1)->first();

            if (!empty($settings->salesforce_token) && !empty($settings->base_url)) {
                if (isset($settings->updated_at) && Carbon::parse($settings->updated_at)->diffInMinutes(Carbon::now()) < 20) {
                    return [
                        'success' => true,
                        'token' => $settings->salesforce_token,
                        'baseURL' => $settings->base_url,
                        'code' => 200
                    ];
                }
            }


            $response = $httpClient->post("$loginURL/oauth2/token", [
                'form_params' => [
                    'grant_type' => 'password',
                    'username' => $username,
                    'password' => $password,
                    'client_id' => $clientID,
                    'client_secret' => $clientSecret,
                ]
            ]);

            if ($response->getStatusCode() === 200) {
                $responseObj = json_decode($response->getBody());

                $this->token = $responseObj->access_token;
                $this->baseURL = $responseObj->instance_url;


                DB::table('settings')->where('id', 1)->update(
                    [
                        'salesforce_token' => $this->token,
                        'base_url' => $this->baseURL,
                        'updated_at' => Carbon::now()
                    ]);

                return [
                    'success' => true,
                    'token' => $this->token,
                    'baseURL' => $this->baseURL,
                    'code' => 200
                ];
            }

        } catch (GuzzleException $e) {

            Log::info('Salesforce Login API error response');
            Log::info($e);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }

        return [
            'success' => false,
            'message' => "Something happened",
            'code' => 501
        ];

    }

    public function createSession($sessionObject)
    {

//        Log::info(json_encode($sessionObject));

        $session = [
            "serviceID" => $sessionObject['serviceID'],
            "studentID" => $sessionObject['studentID'],
            "mentorID" => $sessionObject['mentorID'],
            "sessionDate" => $sessionObject['sessionDate'],
            "sessionDuration" => $sessionObject['sessionDuration'],
            "sessionNote" => $sessionObject['sessionNote'],
            "sessionTypeID" => self::SESSION_TYPE_MAPPING[$sessionObject['sessionTypeID']],
            "sessionSourceID" => self::SESSION_SOURCE_MAPPING[$sessionObject['sessionSourceID']],
            "sessionLocationID" => self::SESSION_LOCATION_MAPPING[$sessionObject['sessionLocationID']]
        ];

        $sessionId = $this->createSessionInSalesforce($session);

        if ($sessionId) {
            return [
                'success' => true,
                'data' => [
                    'id' => $sessionId
                ],
                'code' => 200
            ];
        } else {
            return [
                'success' => false,
                'data' => [
                    'id' => null
                ],
                'code' => 400
            ];
        }
    }

    public function getMentorsByOffice($office = "")
    {
        $mentorsConverted = [];

        $recordType = env('SF_MENTOR_RECORD_TYPE_ID');
        $mentors = $this->queryData('Contact', 'Id,AccountId,npe01__HomeEmail__c,MobilePhone,FirstName,MiddleName,LastName,Mentor__c,Take_Stock_in_Children_Student__c,Mentor_Status_1__c,Mentor_Status_2__c,Affiliate__c,Legacy_ID__c,Mentor_Status_Chevron__c,Inactive__c', "RecordTypeId='$recordType'  AND Affiliate__c='$office'");

        if ($mentors) {
            $mentorsConverted = collect($mentors)->map(function ($mentor) {
//                Log::info($mentor->Mentor_Status_1__c . "-" . $mentor->Mentor_Status_2__c);
//                    $statusId = self::MENTOR_STATUS_MAPPING[$mentor->Mentor_Status_1__c . " - " . $mentor->Mentor_Status_2__c . " - " . ($mentor->Inactive__c ? 'Active' : 'Inactive')] ?? 99;
                $statusId = self::MENTOR_STATUS_MAPPING[$mentor->Mentor_Status_1__c . " - " . $mentor->Mentor_Status_2__c] ?? 99;

                $idParts = explode('-', $mentor->Legacy_ID__c);

                return [
                    'firstName' => $mentor->FirstName,
                    'middleName' => $mentor->MiddleName,
                    'lastName' => $mentor->LastName,
                    'emailAddress' => $mentor->npe01__HomeEmail__c,
                    'mentorStatusID' => $statusId,
                    'stardbID' => count($idParts) === 2 ? $idParts[1] : 0,      // If legacy mentor, send that info, otherwise 0
                    'officeID' => 0,
                    'mentorID' => $mentor->Id,
                    'externalId' => $mentor->Id,
                    'externalOfficeId' => $mentor->AccountId,
                    'externalOfficeName' => $mentor->Affiliate__c,
                    'mobilePhoneNumber' => $mentor->MobilePhone
                ];
            });

        }

        return [
            'success' => true,
            'data' => $mentorsConverted,
            'code' => 200
        ];
    }

    public function getStudentsByOffice($office = "")
    {
        $converted = [];

        $recordType = env('SF_MENTEE_RECORD_TYPE_ID');
        $students = $this->queryData('Contact', 'Id,AccountId,npe01__HomeEmail__c,MobilePhone,FirstName,MiddleName,LastName,Mentor__c,Take_Stock_in_Children_Student__c,Mentor_Status_1__c,Mentor_Status_2__c,Affiliate__c,Legacy_ID__c,Student_Status_1__c,Student_Status_2__c,Student_Status_3__c,Student_Status_4__c,npsp__Primary_Affiliation__c,Student_Status_Chevron__c,Children_Status__c,Closed_Reason__c,County__c', "RecordTypeId='$recordType' AND Affiliate__c='$office'");

        if ($students) {
            $converted = collect($students)->map(function ($student) {
//                    $statusId = self::MENTEE_STATUS_MAPPING[
//                                $student->Student_Status_1__c
//                            . ($student->Student_Status_2__c ?  " - "  . $student->Student_Status_2__c: "")
//                            . ($student->Student_Status_3__c ?  " - " . $student->Student_Status_3__c : "")
//                            . ($student->Student_Status_4__c ?  " - " . $student->Student_Status_4__c : "")
//                            . " - " . $student->Student_Status_Chevron__c
//                        ] ?? 99;
                $statusId = self::MENTEE_STATUS_MAPPING[$student->Student_Status_Chevron__c
                . ' - ' . $student->Children_Status__c
                . ($student->Closed_Reason__c ? " - " . $student->Closed_Reason__c : "")] ?? 99;


                $idParts = explode('-', $student->Legacy_ID__c);
//                Log::info('Student ID::' . $student->Id . "....Status..." . $student->npsp__Primary_Affiliation__c);
                return [
                    'firstName' => $student->FirstName,
                    'middleName' => $student->MiddleName,
                    'lastName' => $student->LastName,
                    'emailAddress' => $student->npe01__HomeEmail__c,
                    'studentStatusID' => $statusId,
                    'schoolID' => $student->npsp__Primary_Affiliation__c,
                    'stardbID' => count($idParts) === 2 ? $idParts[1] : 0,      // If legacy mentor, send that info, otherwise 0
                    'studentID' => $student->Id,
                    'externalId' => $student->Id,
                    'externalSchoolId' => $student->npsp__Primary_Affiliation__c,
                    'mobilePhoneNumber' => $student->MobilePhone,
                ];
            });

        }

        return [
            'success' => true,
            'data' => $converted,
            'code' => 200
        ];
    }

    public function getScholarsByOffice($office = "")
    {
        $converted = [];

        $recordType = env('SF_SCHOLAR_RECORD_TYPE_ID');
        $students = $this->queryData('Contact', 'Id,AccountId,npe01__HomeEmail__c,MobilePhone,FirstName,MiddleName,LastName,Mentor__c,Take_Stock_in_Children_Student__c,Mentor_Status_1__c,Mentor_Status_2__c,Affiliate__c,Legacy_ID__c,Student_Status_1__c,Student_Status_2__c,Student_Status_3__c,Student_Status_4__c,npsp__Primary_Affiliation__c,Student_Status_Chevron__c,Children_Status__c,Closed_Reason__c,County__c', "RecordTypeId='$recordType' AND Affiliate__c='$office'");

        if ($students) {
            $converted = collect($students)->map(function ($student) {

                $statusId = self::MENTEE_STATUS_MAPPING[$student->Student_Status_Chevron__c] ?? 99;


                $idParts = explode('-', $student->Legacy_ID__c);
//                Log::info('Student ID::' . $student->Id . "....Status..." . $student->npsp__Primary_Affiliation__c);
                return [
                    'firstName' => $student->FirstName,
                    'middleName' => $student->MiddleName,
                    'lastName' => $student->LastName,
                    'emailAddress' => $student->npe01__HomeEmail__c,
                    'studentStatusID' => $statusId,
                    'schoolID' => $student->npsp__Primary_Affiliation__c,
                    'stardbID' => count($idParts) === 2 ? $idParts[1] : 0,      // If legacy mentor, send that info, otherwise 0
                    'studentID' => $student->Id,
                    'externalId' => $student->Id,
                    'externalSchoolId' => $student->npsp__Primary_Affiliation__c,
                    'mobilePhoneNumber' => $student->MobilePhone,
                ];
            });

        }

        return [
            'success' => true,
            'data' => $converted,
            'code' => 200
        ];
    }

    public function getStudentMentorMatch($studentId)
    {
        $converted = [];

        $matches = $this->queryData('Match__c', 'Active__c,CreatedDate,Primary_Mentor__c,Mentor_Level__c,Student__c'
            , "Student__c='$studentId' and Active__c=true");

        if ($matches) {
            $converted = collect($matches)->map(function ($match) {
                return [
                    'mentorAssignmentTypeID' => $match->Active__c ? 1 : 0,
                    'assignedDate' => $match->CreatedDate,
                    'mentorID' => $match->Primary_Mentor__c,
                    'isPrimary' => $match->Mentor_Level__c === "PRIMARY" ? "" : "1",
                ];
            });

            return [
                'success' => true,
                'data' => $converted,
                'code' => 200
            ];
        } else {
            return [
                'success' => true,
                'data' => [],
                'code' => 200
            ];
        }
    }

    public function getAllSchoolsForOffice($affiliateId = "")
    {

        $converted = [];

        $recordType = env('SF_SCHOOL_RECORD_TYPE_ID');
        $matches = $this->queryData('Account', 'Id,Name,ParentId,ShippingCity,ShippingState,ShippingPostalCode,ShippingStreet,ShippingCountry,ShippingAddress,Affiliate__c'
            , "RecordTypeId = '$recordType' and Affiliate__c='$affiliateId'");

        if ($matches) {
            $converted = collect($matches)->map(function ($match) {
                return [
                    'affiliateId' => $match->Affiliate__c,
                    'schoolID' => $match->Id,
                    'schoolName' => $match->Name,
                    'addressID' => 0,
                    'city' => $match->ShippingCity ?? "",
                    'state' => $match->ShippingState ?? "",
                    'zip' => $match->ShippingPostalCode ?? 0,
                    'street' => $match->ShippingStreet ?? "",
                    'latitude' => $match->ShippingLatitude ?? 0,
                    'longitude' => $match->ShippingLongitude ?? 0,
                ];
            });

            return [
                'success' => true,
                'data' => $converted,
                'code' => 200
            ];
        } else {
            return [
                'success' => true,
                'data' => [],
                'code' => 200
            ];
        }

    }

    public function getStudentSessionCount($studentId, $startDate, $endDate)
    {
        $count = $this->getCount('pmdm__ServiceDelivery__c', 'Id'
            , "pmdm__Contact__c = '$studentId' AND pmdm__DeliveryDate__c>=$startDate AND pmdm__DeliveryDate__c <=$endDate");

        return [
            'success' => true,
            'data' => $count,
            'code' => 200
        ];

    }

    public function getMentorSessionCount($mentorId, $startDate, $endDate)
    {
        $count = $this->getCount('pmdm__ServiceDelivery__c', 'Id'
            , "pmdm__Service_Provider__c = '$mentorId' AND pmdm__DeliveryDate__c>=$startDate AND pmdm__DeliveryDate__c <=$endDate");

        return [
            'success' => true,
            'data' => $count,
            'code' => 200
        ];

    }

    public function getProgramAndServicesForAgency($agencyId)
    {
//        Log::info("Processing $agencyId in Salesforce Service");
        $servicesConverted = [];
        $programs = $this->queryData('pmdm__Program__c', 'Id,Affiliate__c,Fiscal_Year__c', "Fiscal_Year__c!=null AND Affiliate__c='$agencyId'");

        if ($programs) {
            foreach ($programs as $program) {
                // Fetch service for each program
                $services = $this->queryData('pmdm__Service__c', 'Id,pmdm__Status__c,Name', "pmdm__Program__c='$program->Id'");

                foreach ($services as $service) {
                    if (strpos($service->Name, 'Mentoring') !== false) {
                        $servicesConverted[] = [
                            'program_id' => $program->Id,
                            'fiscal_year' => $program->Fiscal_Year__c,
                            'service_id' => $service->Id,
                            'active' => $service->pmdm__Status__c === "Active",
                        ];
                    }
                }
            }

            $servicesConverted = collect($servicesConverted);
        }

//        Log::info("Received in $agencyId::" . json_encode($servicesConverted));

        return [
            'success' => true,
            'data' => $servicesConverted,
            'code' => 200
        ];

    }

    private function queryData($object, $fields, $whereClause = null)
    {

        try {
            $httpClient = new GuzzleClient();

            $query = "SELECT $fields FROM $object";

            if ($whereClause) {
                $query .= " WHERE $whereClause";
            }
            $foundMore = false;

            $responseObject = [];
            $url = "/services/data/v56.0/query?q=$query";
            do {
                $response = $httpClient->get($this->baseURL . $url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token
                    ]
                ]);

                $responseBody = json_decode($response->getBody());

                $responseObject = array_merge($responseObject, $responseBody->records);

                if (isset($responseBody->nextRecordsUrl)) {
                    $foundMore = true;
                    $url = $responseBody->nextRecordsUrl;
                } else {
                    $foundMore = false;
                }
            } while ($foundMore);

            return $responseObject;
        } catch (GuzzleException $e) {
            Log::info($e);
            return null;
        }
    }

    private function getCount($object, $fields, $whereClause = null)
    {

        try {
            $httpClient = new GuzzleClient();

            $query = "SELECT $fields FROM $object";

            if ($whereClause) {
                $query .= " WHERE $whereClause";
            }

            $responseObject = [];
            $url = "/services/data/v56.0/query?q=$query";
            $response = $httpClient->get($this->baseURL . $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token
                ]
            ]);

            $responseBody = json_decode($response->getBody());

            return $responseBody->totalSize;
        } catch (GuzzleException $e) {
            Log::info($e);
            return 0;
        }
    }

    public function getLegacyOffices()
    {
        return $this->queryData('Account', 'Id,Legacy_ID__c,Affiliate__c,Name', "Legacy_ID__c LIKE 'OFFICE-_' OR Legacy_ID__c LIKE 'OFFICE-__'");
    }

    public function getLegacyMentors()
    {
        return $this->queryData('Contact', 'Id,AccountId,Affiliate__c,Legacy_ID__c', "Legacy_ID__c LIKE 'MENTOR-_' OR Legacy_ID__c LIKE 'MENTOR-__' OR Legacy_ID__c LIKE 'MENTOR-___' OR Legacy_ID__c LIKE 'MENTOR-____' OR Legacy_ID__c LIKE 'MENTOR-_____' OR Legacy_ID__c LIKE 'MENTOR-______'");
    }

    public function getLegacySchools()
    {
        return $this->queryData('Account', 'Id,Legacy_ID__c,Name', "Legacy_ID__c LIKE 'SCHOOL-_' OR Legacy_ID__c LIKE 'SCHOOL-__'  OR Legacy_ID__c LIKE 'SCHOOL-___'  OR Legacy_ID__c LIKE 'SCHOOL-____' OR Legacy_ID__c LIKE 'SCHOOL-_____'");
    }

    public function getLegacyStudents()
    {
        return $this->queryData('Contact', 'Id,AccountId,Affiliate__c,Legacy_ID__c,npsp__Primary_Affiliation__c', "Legacy_ID__c LIKE 'STUDENT-_' OR Legacy_ID__c LIKE 'STUDENT-__' OR Legacy_ID__c LIKE 'STUDENT-___' OR Legacy_ID__c LIKE 'STUDENT-____' OR Legacy_ID__c LIKE 'STUDENT-_____' OR Legacy_ID__c LIKE 'STUDENT-______'");
    }

    private function createSessionInSalesforce($session)
    {
        try {
            $httpClient = new GuzzleClient();

            $url = "/services/data/v56.0/sobjects/pmdm__ServiceDelivery__c";

//            Log::info('Sending request to Salesforce', ['url' => $this->baseURL . $url, 'session' => $session]);

            $response = $httpClient->post($this->baseURL . $url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token
                ],
                'json' => [
                    'pmdm__Service__c' => $session['serviceID'],
                    "pmdm__Service_Provider__c" => $session['mentorID'],
                    "pmdm__Contact__c" => $session['studentID'],
                    "pmdm__DeliveryDate__c" => $session['sessionDate'],
                    "Duration__c" => $session['sessionDuration'],
                    "Session_Location__c" => "",
                    "Session_Method__c" => $session['sessionLocationID'],
                    "Session_Notes1__c" => $session['sessionNote'],
                    "Session_Source__c" => $session['sessionSourceID'],
                    "Session_Type__c" => $session['sessionTypeID'],
                    "RecordTypeId" => env('SF_SERVICE_DELIVERY_RECORD_TYPE_ID')
                ]
            ]);

//            Log::info($response->getStatusCode());
            $responseBody = json_decode($response->getBody());

            return $responseBody->id;
        } catch (GuzzleException $e) {
            Log::info($e);
            return null;
        }
    }
}
