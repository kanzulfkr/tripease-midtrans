<?php
$server_key = "SB-Mid-server-Pfyvfrks-KFQAalvQjOt6BWS";
$is_production = false;
$api_url = $is_production ?
    'https://app.midtrans.com/snap/v1/transactions' :
    'https://app.sandbox.midtrans.com/snap/v1/transactions';

if (!strpos($_SERVER['REQUEST_URI'], '/charge')) {
    http_response_code(404);
    echo "Wrong path, make sure it's '/charge'";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(404);
    echo "Page not found or wrong HTTP Request method is used";
    exit();
}

$request_body = file_get_contents('php://input');
header('Content-Type: application/json');

$charge_result = chargerAPI($api_url, $server_key, $request_body);
http_response_code($charge_result['http_code']);
echo $charge_result['body'];

function chargerAPI($api_url, $server_key, $request_body)
{
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,

        CURLOPT_HTTPHEADER =>   array(
            'Content_Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($server_key)
        ),
        CURLOPT_POSTFIELDS => $request_body
    );
    curl_setopt_array($ch, $curl_options);
    $result = array(
        'body' => curl_exec($ch),
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
    );
    return $result;
}
