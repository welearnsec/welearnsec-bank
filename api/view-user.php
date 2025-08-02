<?php
session_start();
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$decodedId = base64_decode($_GET['id']);
$sessionUserId = $_SESSION['user_id'];

require_once '../config.php'; // adjust path as needed

// 🔓 Still vulnerable
$sql = "SELECT * FROM users WHERE id = $decodedId"; // SQLi still possible
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    // 🧨 Check if SESSION user ID appears in the result **anywhere**
    if (strpos($sql, $sessionUserId) === false) {
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    echo json_encode([
        'name' => $row['name'],
        'email' => $row['email'],
        'role' => $row['role'],
        'password' => $row['password'],
        'created_at' => $row['created_at']
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>
