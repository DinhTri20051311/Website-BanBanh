<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = __DIR__ . '/debug_gemini.txt';
// Use same API key as chatbot_api.php
$api_key = 'AIzaSyAImg0ifj6pKF4jPdSu3Tx5zY1rxHl8GpM';
file_put_contents($logFile, "===== LIST MODELS RUN =====\n", FILE_APPEND);

$url = "https://generativelanguage.googleapis.com/v1/models?key=" . $api_key;
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents($logFile, "HTTP CODE: $httpCode\n", FILE_APPEND);
file_put_contents($logFile, "CURL ERROR: $curlError\n", FILE_APPEND);
file_put_contents($logFile, "RAW RESPONSE:\n$response\n\n", FILE_APPEND);

// Also print response for immediate feedback when run via CLI
if (php_sapi_name() === 'cli') {
    echo "HTTP CODE: $httpCode\n";
    if ($curlError) echo "CURL ERROR: $curlError\n";
    echo $response . "\n";
}

?>