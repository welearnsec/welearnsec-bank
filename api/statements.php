<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../config.php';

$account_id = $_SESSION['user_id'];
$month = $_GET['month'] ?? null;

// Track all loads per session
$_SESSION['statement_request_count'] = ($_SESSION['statement_request_count'] ?? 0) + 1;

if ($_SESSION['statement_request_count'] > 10) {
    http_response_code(503);
    echo json_encode(['error' => '⚠️ System temporarily overloaded due to repeated requests. Please reload after a short break.']);
    exit;
}

$transactions = [];

try {
    if ($month) {
        $stmt = $conn->prepare("
            SELECT id, amount, type, created_at 
            FROM transactions 
            WHERE account_id = ? AND DATE_FORMAT(created_at, '%Y-%m') = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("is", $account_id, $month);
    } else {
        $stmt = $conn->prepare("
            SELECT id, amount, type, created_at 
            FROM transactions 
            WHERE account_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("i", $account_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $transactions[] = [
            'id' => $row['id'],
            'amount' => $row['amount'],
            'type' => $row['type'],
            'date' => $row['created_at']
        ];
    }

    echo json_encode([
        'account_id' => $account_id,
        'month' => $month ?? 'all',
        'transactions' => $transactions
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch statements']);
}
