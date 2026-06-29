<?php
// Simple test script to reproduce the application's cURL call and produce logs.
require __DIR__ . '/init_autoloader.php';

use Application\Model\common;

// Usage:
// php test_curl.php [controller] [method] '[json_params]'
// Example:
// php test_curl.php companycontroller getDetails '{"id":123}'

$controller = $argv[1] ?? 'companycontroller';
$method = $argv[2] ?? 'methodName';
$params = [];
if (!empty($argv[3])) {
    $decoded = json_decode($argv[3], true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $params = $decoded;
    } else {
        // try parse as query string
        parse_str($argv[3], $params);
    }
}

$c = new common();
$response = $c->curlhit($params, $method, $controller);

$logDir = __DIR__ . '/public/log';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0775, true);
}
$respLog = $logDir . '/test_curl_response.txt';
file_put_contents($respLog, date('c') . " | Controller:$controller | Method:$method | Params:" . json_encode($params) . "\nResponse:\n" . print_r($response, true) . "\n\n", FILE_APPEND);

echo $response;
