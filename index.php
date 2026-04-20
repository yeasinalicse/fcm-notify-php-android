<?php

class FcmSender {

    private static $PROJECT_ID = "947355066889";
    private static $SERVICE_ACCOUNT_FILE = "service-account.json";

    public static function getAccessToken() {

        $jsonKey = json_decode(file_get_contents(self::$SERVICE_ACCOUNT_FILE), true);

        if (!$jsonKey) {
            die("❌ service-account.json not found!");
        }

        $header = self::base64UrlEncode(json_encode([
            "alg" => "RS256",
            "typ" => "JWT"
        ]));

        $now = time();

        $payload = self::base64UrlEncode(json_encode([
            "iss" => $jsonKey['client_email'],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => "https://oauth2.googleapis.com/token",
            "iat" => $now,
            "exp" => $now + 3600
        ]));

        $signatureInput = $header . "." . $payload;

        openssl_sign($signatureInput, $signature, $jsonKey['private_key'], 'SHA256');

        $jwt = $signatureInput . "." . self::base64UrlEncode($signature);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://oauth2.googleapis.com/token");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion" => $jwt
        ]));

        $response = curl_exec($ch);

        if ($response === false) {
            die("❌ Curl error: " . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if (!isset($result['access_token'])) {
            die("❌ Token error: " . $response);
        }

        return $result['access_token'];
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function sendPush($title, $body) {

        $accessToken = self::getAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/" . self::$PROJECT_ID . "/messages:send";

        $data = [
            "message" => [
                "topic" => "All_FCM",
                "notification" => [
                    "title" => $title,
                    "body" => $body
                ],
                "data" => [
                    "type" => "All_FCM",
                    "id" => "12345"
                ],
                "android" => [
                    "priority" => "high",
                    "notification" => [
                        "sound" => "default"
                    ]
                ]
            ]
        ];

        $headers = [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return "HTTP Code: $httpCode <br> Response: $response";
    }
}


// 🟢 Handle Form Submit
$result = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $body = $_POST['body'] ?? '';

    if (!empty($title) && !empty($body)) {
        $result = FcmSender::sendPush($title, $body);
    } else {
        $result = "❌ Title and Body required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FCM Push Sender</title>
</head>
<body style="font-family: Arial; padding: 40px;">

<h2>📲 Send FCM Notification</h2>

<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" style="width:300px;" required><br><br>

    <label>Body:</label><br>
    <textarea name="body" rows="4" style="width:300px;" required></textarea><br><br>

    <button type="submit">🚀 Send</button>
</form>

<br>

<div>
    <?php echo $result; ?>
</div>

</body>
</html>