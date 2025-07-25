<?php

namespace App\Traits;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

trait ZoomMeetingTrait
{
    public $client;
    public $jwt;
    public $headers;

    public function __construct()
    {
        $this->client = new Client();
        $unique_name = null;
        $this->jwt = $this->generateMentorZoomToken($unique_name);
        $this->jwt = $this->generateMenteeZoomToken($unique_name);
//        Log::info('TOKEN:' . $this->jwt);
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->jwt,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function generateMentorZoomToken($unique_name)
    {
        $key = env('ZOOM_API_KEY', '');
        $secret = env('ZOOM_API_SECRET', '');
        $tpc = $unique_name;
        $payload = [
            'app_key' => $key,
            'role_type' => 1,
            'tpc' => $tpc,
            'version' => 1,
            'cloud_recording_option' => 0,
            'cloud_recording_election' => 1,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDay(2)->timestamp,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function generateDownloadRecordingToken()
    {
        $key = env('ZOOM_KEY', '');
        $secret = env('ZOOM_SECRET', '');
        $payload = [
            'aud' => null,
            'iss' => $key,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDay(2)->timestamp,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    public function generateMenteeZoomToken($unique_name)
    {
        $key = env('ZOOM_API_KEY', '');
        $secret = env('ZOOM_API_SECRET', '');
        $tpc = $unique_name;
        $payload = [
            'app_key' => $key,
            'role_type' => 0,
            'tpc' => $tpc,
            'version' => 1,
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addDay(2)->timestamp,
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    private function retrieveZoomUrl()
    {
        return env('ZOOM_API_URL', '');
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new \DateTime($dateTime);

            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());

            return '';
        }
    }

    public function create($data)
    {
        $path = 'users/me/meetings';
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->headers,
            'body' => json_encode([
                'topic' => $data['topic'] ?: 'Test Meeting',
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration' => $data['duration'] ?: 120,
                'agenda' => (!empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone' => 'Asia/Kolkata',
                'settings' => [
                    'host_video' => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room' => true,
                ],
            ]),
        ];

        $response = $this->client->post($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 201,
            'data' => json_decode($response->getBody(), true),
        ];
    }

    public function update($id, $data)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->headers,
            'body' => json_encode([
                'topic' => $data['topic'],
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration' => $data['duration'],
                'agenda' => (!empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone' => 'Asia/Kolkata',
                'settings' => [
                    'host_video' => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room' => true,
                ],
            ]),
        ];
        $response = $this->client->patch($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data' => json_decode($response->getBody(), true),
        ];
    }

    public function get($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();
        $this->jwt = $this->generateZoomToken();
        $body = [
            'headers' => $this->headers,
            'body' => json_encode([]),
        ];

        $response = $this->client->get($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data' => json_decode($response->getBody(), true),
        ];
    }

    /**
     * @param string $id
     *
     * @return bool[]
     */
    public function delete($id)
    {
        $path = 'meetings/' . $id;
        $url = $this->retrieveZoomUrl();
        $body = [
            'headers' => $this->headers,
            'body' => json_encode([]),
        ];

        $response = $this->client->delete($url . $path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
        ];
    }

}
