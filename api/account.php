<?php
include '../config.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if logged in
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get the account ID from the GET parameter (vulnerable! no ownership check)
$accountId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($accountId === 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid account ID']);
    exit();
}

// ⚠️ Vulnerable: No check if the account belongs to logged-in user
$query = "SELECT a.id, a.account_number, a.balance, u.id as user_id, u.name, u.email 
          FROM accounts a 
          JOIN users u ON a.user_id = u.id
          WHERE a.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'Account not found']);
    exit();
}

$account = $result->fetch_assoc();

echo json_encode($account);
?>
