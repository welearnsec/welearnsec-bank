<?php
session_start();
include '../config.php';
header("Content-Type: application/json");

// ✅ Require login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

$from_acc_number = $input['from_account_id'] ?? null;
$to_acc_number = $input['to_account_id'] ?? null;
$amount = floatval($input['amount'] ?? 0);

if (!$from_acc_number || !$to_acc_number || $amount <= 0) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// ✅ Check if the source account belongs to the logged-in user (match by account_number)
$stmt = $conn->prepare("SELECT id, balance FROM accounts WHERE account_number = ? AND user_id = ?");
$stmt->bind_param("si", $from_acc_number, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$from_account = $result->fetch_assoc();

if (!$from_account) {
    echo json_encode(["error" => "Source account not found or not owned by user"]);
    exit;
}

// ✅ Get destination account info by account_number
$stmt = $conn->prepare("SELECT id FROM accounts WHERE account_number = ?");
$stmt->bind_param("s", $to_acc_number);
$stmt->execute();
$to_result = $stmt->get_result();
$to_account = $to_result->fetch_assoc();

if (!$to_account) {
    echo json_encode(["error" => "Destination account not found"]);
    exit;
}

$from_id = $from_account['id'];
$to_id = $to_account['id'];

// ✅ Check for sufficient funds
if ($from_account['balance'] < $amount) {
    echo json_encode(["error" => "Insufficient funds"]);
    exit;
}

// ✅ Begin transaction
$conn->begin_transaction();

try {
    // ✅ Deduct from source
    $stmt = $conn->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $from_id);
    $stmt->execute();

    // ✅ Credit to destination
    $stmt = $conn->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $to_id);
    $stmt->execute();

    // ✅ Log debit transaction
    $stmt = $conn->prepare("INSERT INTO transactions (account_id, type, amount, created_at) VALUES (?, 'transfer', ?, NOW())");
    $stmt->bind_param("id", $from_id, $amount);
    $stmt->execute();

    // ✅ Log credit transaction
    $stmt = $conn->prepare("INSERT INTO transactions (account_id, type, amount, created_at) VALUES (?, 'deposit', ?, NOW())");
    $stmt->bind_param("id", $to_id, $amount);
    $stmt->execute();

    $conn->commit();
    echo json_encode(["message" => "Transfer complete"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["error" => "Transaction failed: " . $e->getMessage()]);
}
