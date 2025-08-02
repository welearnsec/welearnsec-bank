<?php
// olderversion-api/deprecated.php
header('Content-Type: application/json');

// Include weak DB connection
include '../config.php';

// Vulnerable: no authentication or authorization check

$sql = "SELECT name, email, created_at, role FROM users";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Query failed: " . $conn->error
    ]);
    exit;
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    "status" => "success",
    "message" => "Legacy user info",
    "data" => $users
]);

$conn->close();
