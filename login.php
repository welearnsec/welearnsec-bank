<?php
session_start();
include 'config.php';

if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Secure query to get user by email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

   if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // ✅ Compare password securely using password_verify
    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id']; // required by transfer.php
        $_SESSION['account_id'] = $user['id']; // add this

        // ⚠️ Broken Auth: Store email and password in localStorage (for demo purposes only)
        echo "<script>
            localStorage.setItem('email', '".htmlspecialchars($email, ENT_QUOTES)."');
            localStorage.setItem('password', '".htmlspecialchars($password, ENT_QUOTES)."');
            window.location.href = 'dashboard.php';
        </script>";
        exit();

    } else {
        $error = "Invalid email or password.";
    }
} else {
    $error = "Invalid email or password.";
}
}
?>


<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow rounded">
                <div class="card-body">
                    <h4 class="text-center mb-4">🔐 Login to WeLearnSec-Bank</h4>
                    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required />
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php">New user? Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
