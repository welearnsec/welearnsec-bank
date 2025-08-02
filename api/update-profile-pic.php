<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../config.php';

// SSRF via GET parameter
$image_url = $_GET['image_url'] ?? null;

if (!$image_url || !filter_var($image_url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or missing image_url']);
    exit;
}

// Simulate fetching the image (SSRF vulnerable)
$contents = @file_get_contents($image_url);

if ($contents === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch image']);
    exit;
}

// Assume user image is updated successfully
echo json_encode(['message' => 'Profile image updated from: ' . $image_url]);
