<?php

namespace App\Http\Controllers\Salesforce;

use App\Services\SalesforceDBService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class MiddlewareController extends Controller
{
    private $service = null;

    public function __construct(SalesforceDBService $service)
    {
        $this->service = $service;
    }

    public function login()
    {
        $serviceResponse = $this->service->authenticate();
        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getToken(SalesforceDBService $service)
    {
        $serviceResponse = $service->authenticate();
        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getMentorsByOfficeFunc($token, $baseURL, $office)
    {
        // Get the Token and base URL
        $this->service->token = $token;
        $this->service->baseURL = $baseURL;

        $serviceResponse = $this->service->getMentorsByOffice($office);
        return $serviceResponse;
    }

//    public function getMentorsByOffice(Request $request, SalesforceDBService $service)
//    {
//        // Get the Token and base URL
//        $token = $request->header('token');
//        $baseURL = $request->header('baseURL');
//
//        $office = $request->query('office');
//
//        $service->token = $token;
//        $service->baseURL = $baseURL;
//
//        $serviceResponse = $service->getMentorsByOffice($office);
//        return response()->json($serviceResponse, $serviceResponse['code']);
//    }

    public function getStudentsByOfficeFunc($token, $baseURL, $office)
    {
        // Get the Token and base URL

        $this->service->token = $token;
        $this->service->baseURL = $baseURL;

        $serviceResponse = $this->service->getStudentsByOffice($office);
        return $serviceResponse;
    }

    public function getScholarsByOfficeFunc($token, $baseURL, $office)
    {
        // Get the Token and base URL

        $this->service->token = $token;
        $this->service->baseURL = $baseURL;

        $serviceResponse = $this->service->getScholarsByOffice($office);
        return $serviceResponse;
    }

//    public function getStudentsByOffice(Request $request, SalesforceDBService $service)
//    {
//        // Get the Token and base URL
//        $token = $request->header('token');
//        $baseURL = $request->header('baseURL');
//
//        $office = $request->query('office');
//
//        $service->token = $token;
//        $service->baseURL = $baseURL;
//
//        $serviceResponse = $service->getStudentsByOffice($office);
//        return response()->json($serviceResponse, $serviceResponse['code']);
//    }

    public function getStudentMentorMatchFunc($token, $baseURL)
    {

        $this->service->token = $token;
        $this->service->baseURL = $baseURL;

        $serviceResponse = $this->service->getStudentMentorMatch();
        return $serviceResponse;
    }

//    public function getStudentMentorMatch(Request $request, SalesforceDBService $service)
//    {
//        // Get the Token and base URL
//        $token = $request->header('token');
//        $baseURL = $request->header('baseURL');
//
//        $studentId = $request->query('studentId');
//
//        $service->token = $token;
//        $service->baseURL = $baseURL;
//
//        $serviceResponse = $service->getStudentMentorMatch($studentId);
//        return response()->json($serviceResponse, $serviceResponse['code']);
//    }

    public function getSchoolsForOfficeFunc($token, $baseURL, $affiliateId)
    {
        $this->service->token = $token;
        $this->service->baseURL = $baseURL;

        $serviceResponse = $this->service->getAllSchoolsForOffice($affiliateId);
        return $serviceResponse;
    }

//    public function getAllSchoolsForOffice(Request $request, SalesforceDBService $service)
//    {
//        // Get the Token and base URL
//        $token = $request->header('token');
//        $baseURL = $request->header('baseURL');
//
//        $affiliateId = $request->query('affiliateId');
//
//        $service->token = $token;
//        $service->baseURL = $baseURL;
//        $serviceResponse = $service->getAllSchoolsForOffice($affiliateId);
//        return response()->json($serviceResponse, $serviceResponse['code']);
//    }

    public function createSession(Request $request, SalesforceDBService $service)
    {
        // Get the Token and base URL
        $token = $request->header('token');
        $baseURL = $request->header('baseURL');

        $sessionObject = $request->only('serviceID', 'studentID', 'mentorID', 'sessionDate', 'sessionDuration', 'sessionNote', 'sessionTypeID', 'sessionSourceID', 'sessionLocationID');

        $service->token = $token;
        $service->baseURL = $baseURL;

        $serviceResponse = $service->createSession($sessionObject);

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getStudentSessionCount(Request $request, SalesforceDBService $service)
    {
        // Get the Token and base URL
        $token = $request->header('token');
        $baseURL = $request->header('baseURL');

        $studentId = $request->query('studentId');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $service->token = $token;
        $service->baseURL = $baseURL;

        $serviceResponse = $service->getStudentSessionCount($studentId, $startDate, $endDate);

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getMentorSessionCount(Request $request, SalesforceDBService $service)
    {
        // Get the Token and base URL
        $token = $request->header('token');
        $baseURL = $request->header('baseURL');

        $mentorId = $request->query('mentorId');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $service->token = $token;
        $service->baseURL = $baseURL;
//        Log::info($mentorId);
        $serviceResponse = $service->getMentorSessionCount($mentorId, $startDate, $endDate);

        return response()->json($serviceResponse, $serviceResponse['code']);
    }

    public function getProgramAndServicesForAgency(Request $request, SalesforceDBService $service)
    {
        // Get the Token and base URL
        $token = $request->header('token');
        $baseURL = $request->header('baseURL');

        $agencyId = $request->query('agencyId');

        $service->token = $token;
        $service->baseURL = $baseURL;

        $serviceResponse = $service->getProgramAndServicesForAgency($agencyId);

        return response()->json($serviceResponse, $serviceResponse['code']);
    }
}
