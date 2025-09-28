<?php
class FCMService
{
    private $projectId;
    private $serviceAccount;

    public function __construct($projectId, $serviceAccountFile)
    {
        $this->projectId = $projectId;

        // Load service account JSON key file
        $this->serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);
    }

    // Generate JWT manually
    private function generateJWT()
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $now = time();
        $claimSet = [
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600, // Token valid for 1 hour
        ];

        // Encode Header and Claim Set to Base64
        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlClaimSet = $this->base64UrlEncode(json_encode($claimSet));

        // Create the unsigned JWT
        $unsignedToken = $base64UrlHeader . '.' . $base64UrlClaimSet;

        // Sign JWT with the private key
        $signature = '';
        $privateKey = openssl_pkey_get_private($this->serviceAccount['private_key']);
        openssl_sign($unsignedToken, $signature, $privateKey, 'sha256');
        openssl_free_key($privateKey);

        // Create the final JWT
        $jwt = $unsignedToken . '.' . $this->base64UrlEncode($signature);

        return $jwt;
    }

    // Helper function to base64url encode data
    private function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    // Get access token by exchanging JWT with Google OAuth 2.0 API
    private function getAccessToken()
    {
        $jwt = $this->generateJWT();

        // Google OAuth 2.0 token endpoint
        $url = 'https://oauth2.googleapis.com/token';

        // Prepare POST request
        $postFields = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        // Send HTTP request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Parse and return the access token
        $jsonResponse = json_decode($response, true);
        return $jsonResponse['access_token'];
    }

    // Send FCM notification using HTTP v1 API
    public function sendFCM($token, $title, $body, $isNotifable, $notificationDetail)
    {
        $accessToken = $this->getAccessToken();
        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

        // Create message payload
        $message = [
            'message' => [
                'token' => $token,
                'data' => [
                    'body' => $notificationDetail,
                ],
            ],
        ];

        if ($isNotifable == 1) {
            $message['message']['notification'] = [
                'title' => $title,
                'body' => $body,
            ];
        }

        // Setup headers including OAuth 2.0 token
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        // Send HTTP request to FCM API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
public function sendMultipleFCM($tokens, $title, $body, $isNotifable, $notificationDetail)
{
    $accessToken = $this->getAccessToken();
    $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

    // Setup headers including OAuth 2.0 token
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json',
    ];

    // Loop through tokens and send notification to each user
    foreach ($tokens as $token) {
        // Create message payload
        $message = [
            'message' => [
                'token' => $token, // Send to each token individually
                'data' => [
                    'body' => $notificationDetail,
                ],
            ],
        ];

        if ($isNotifable == 1) {
            $message['message']['notification'] = [
                'title' => $title,
                'body' => $body,
            ];
        }

        // Send HTTP request to FCM API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        $result = curl_exec($ch);
        curl_close($ch);
    }

    return "All notifications sent.";
}

}
// Example usage:

/*$fcm = new FCMService('from-merkato', 'serviceAccount.json');
$result = $fcm->sendFCM('dUFRRNm0Tayy9rE09HpFXg:APA91bGS7wrAJFk6lWYppyXvFiKworGwsCmaupBUwrO9esK5_MjVH3me3WlCzP5iyv2pw_UFB3kJew2r_QAcqsacTA5rfx2uN7u8k66qf-Pz9_Vh6Fyzd5O7ctzMK5_nKBP4PU6MaCaq', 'Test Merkato Pro', 'body', 0, '1$20&merkato pro&fast delivery service...wow it works&no|21');
echo $result;*/
/*
$tokens = array("f59hMGFERAeNoM5r7mu9-L:APA91bEW2eRSrJPG-XIsWFTk0keu3xAFao8IBDaw3NM2V_2gBrTWofVuK46vXvjwOXv0K8_iAJRL0VydzUUcOlzSkUl0u-_mPEN0LEQYs9R2vDAvhHwqF9RTQfjBDYt3EGdeW20Hk0MW","dUFRRNm0Tayy9rE09HpFXg:APA91bGS7wrAJFk6lWYppyXvFiKworGwsCmaupBUwrO9esK5_MjVH3me3WlCzP5iyv2pw_UFB3kJew2r_QAcqsacTA5rfx2uN7u8k66qf-Pz9_Vh6Fyzd5O7ctzMK5_nKBP4PU6MaCaq");

$result = $fcm->sendMultipleFCM($tokens, 'Merkato is Just a Click Away!', 'Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly!', '1$20&Merkato is Just a Click Away!&Why make the trip? Get all the latest mobile accessories delivered directly to your shop from Merkato. Browse now and restock effortlessly! &no|0');

echo $result;*/
?>
