<?php
function getAccessToken() {
    $consumerKey = 'EAR8WZS2z8vGqxwlAClxB4SFsJskSATzhtI9D0M2PKDnLrsF';
    $consumerSecret = 'FJAMTrYC9sw1N6G0LRp3UPsEh9VR86joJGGdkG9p1xalI7JSKGIAQU2DuTeSZKXk';

    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $credentials
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
        return null;
    }

    curl_close($curl);

    $result = json_decode($response);

    return $result->access_token;
}
?>
