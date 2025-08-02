<?php
// api/external-fetch.php
header('Content-Type: application/json');

// Example: /api/external-fetch.php?url=https://api.exchangerate-api.com/v4/latest/USD

if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing 'url' parameter."]);
    exit;
}

$url = $_GET['url'];

// 🛡️ Basic SSRF protection — block localhost/internal
$blocked_hosts = ['localhost', '127.0.0.1', '::1'];
$parsed = parse_url($url);

if (!$parsed || !isset($parsed['host']) || in_array($parsed['host'], $blocked_hosts)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid or disallowed host."]);
    exit;
}

// ⚠️ No validation of source trust, structure, or schema (Unsafe Consumption)
$response = @file_get_contents($url);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["error" => "Failed to fetch data from the external API."]);
    exit;
}

// 👇 Return unvalidated response
echo json_encode([
    "status" => "success",
    "source" => $url,
    "fetched_data" => $response // raw and unchecked
]);
