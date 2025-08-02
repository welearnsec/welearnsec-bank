<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION['user'];

// If you have account ID in session, use it; otherwise fetch here (example):
$accountId = 0;

// Option 1: If stored in session
if (isset($_SESSION['account_id'])) {
    $accountId = $_SESSION['account_id'];
} else {
    // Option 2: Fetch the account id from DB based on user id (simple example)
    include 'config.php';
    $stmt = $conn->prepare("SELECT id FROM accounts WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $accountId = $row['id'];
        $_SESSION['account_id'] = $accountId; // store for next time
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="text-center">
        <h2>🏦 Welcome, <?= htmlspecialchars($user['email']) ?>!</h2>
        <p class="lead">You are now logged in to WeLearnSec-Bank.</p>

        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow p-3" id="balance-card">
                    <h5>💰 Balance</h5>
                    <p id="balance-amount">Loading...</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow p-3">
                    <h5>📜 Transaction History</h5>
                    <p><a href="view-transactions.php">View Transactions</a></p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow p-3">
                    <h5>🔄 Transfer Funds</h5>
                    <p><a href="transfer-form.php">Start a Transfer</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const accountId = <?= json_encode($accountId) ?>;

if (accountId === 0) {
    document.getElementById('balance-amount').textContent = 'Account not found';
} else {
    fetch(`api/account.php?id=${accountId}`)
    .then(res => res.json())
    .then(data => {
        if(data.error) {
            document.getElementById('balance-amount').textContent = `Error: ${data.error}`;
        } else {
            document.getElementById('balance-amount').textContent = `$${data.balance}`;
        }
    })
    .catch(() => {
        document.getElementById('balance-amount').textContent = 'Failed to fetch balance';
    });
}
</script>
