<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;

class FCMServiceProvider
{
    private $serviceAccountFile;
    private $clientEmail;
    private $privateKey;
    private $tokenUri = 'https://oauth2.googleapis.com/token';
    private $fcmApiUri = 'https://fcm.googleapis.com/v1/projects/takestockinchildren-427bb/messages:send';

    public function __construct($serviceAccountFilePath)
    {
        // Load the service account JSON file
        $this->serviceAccountFile = json_decode(file_get_contents($serviceAccountFilePath), true);
        $this->clientEmail = $this->serviceAccountFile['client_email'];
        $this->privateKey = $this->serviceAccountFile['private_key'];
    }

    /**
     * Generate JWT token for OAuth 2.0
     */
    public function generateJWT()
    {
        $nowSeconds = time();
        $payload = [
            'iss' => $this->clientEmail, // issuer
            'sub' => $this->clientEmail, // subject
            'aud' => $this->tokenUri, // audience
            'iat' => $nowSeconds, // issued at
            'exp' => $nowSeconds + 3600, // expiration time
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging' // scope for FCM
        ];

        // Create the JWT and sign it with the private key using RS256 algorithm
        return JWT::encode($payload, $this->privateKey, 'RS256');
    }

    /**
     * Get OAuth 2.0 token from Google
     */
    public function getOAuthToken()
    {
        $jwt = $this->generateJWT();

        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $ch = curl_init($this->tokenUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseDecoded = json_decode($response, true);

        if (isset($responseDecoded['access_token'])) {
            return $responseDecoded['access_token'];
        } else {
            throw new Exception('Failed to get OAuth 2.0 token: ' . $response);
        }
    }

    /**
     * Send a notification to FCM
     */
    public function sendNotification($payload)
    {
        $accessToken = $this->getOAuthToken();
	Log::info(json_encode($payload));

        $notificationPayload = [
            'message' => [
                'token' => $payload['to'],
                'android'   =>  [
                    'notification'  =>  null
                ],
                'apns'  => [
                    'payload'  =>  [
                        'aps' =>  [
                            'alert' =>  [
                                'title' => isset($payload['notification']) ? $payload['notification']['title'] : $payload['data']['title'],
                                'body' => isset($payload['notification']) ? $payload['notification']['message'] : $payload['data']['message']
                            ],
                            'sound' => isset($payload['notification']) ? $payload['notification']['sound'] : 'default'
                        ]
                    ]
                ]
            ]
        ];

        if (isset($payload['notification'])) {
            $notificationPayload['message']['notification'] = [
                'title' => $payload['notification']['title'],
                'body' => $payload['notification']['message']
            ];
        }

        if (isset($payload['notification']['badge'])) {
            $notificationPayload['message']['apns']['payload']['aps']['badge'] = $payload['notification']['badge'];
        }

        if(isset($payload['data'])) {
            $notificationPayload['message']['data'] = $payload['data'];
        }

        $ch = curl_init($this->fcmApiUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notificationPayload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseDecoded = json_decode($response, true);

        if (isset($responseDecoded['name'])) {
            return $responseDecoded;
        } else {
            throw new Exception('Failed to send notification: ' . $response);
        }
    }
}
