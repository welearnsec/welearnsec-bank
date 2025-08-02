<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../config.php';

// Manually parse all account_id occurrences
$raw_query = $_SERVER['QUERY_STRING'];
preg_match_all('/account_id=([^&]+)/', $raw_query, $matches);

$account_ids = $matches[1] ?? [];
$transactions = [];

if (count($account_ids) === 1) {
    // ✅ Normal validation if only 1 param
    $account_id = $account_ids[0];

    if ($account_id != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    // Proceed with authorized access
} elseif (count($account_ids) >= 2) {
    // 🔥 Bypass: attacker sends two account_id values
    // e.g., ?account_id=2&account_id=1 (you are ID 2, targeting ID 1)

    $attacker_id = $account_ids[0];  // 2 → used to pass validation
    $target_id   = $account_ids[1];  // 1 → used in SQL

    if ($attacker_id != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized access']);
        exit;
    }

    $account_id = $target_id; // SQL uses victim’s ID
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing account_id']);
    exit;
}

// 🔥 SQL runs after either path
try {
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE account_id = ?");
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    echo json_encode(['transactions' => $transactions]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch transactions']);
}
?>
