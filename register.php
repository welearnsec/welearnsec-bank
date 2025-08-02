<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Generate account number
        $account_number = "AC" . rand(10000000, 99999999);

      $initial_balance = 500.00;
$stmt2 = $conn->prepare("INSERT INTO accounts (user_id, account_number, balance) VALUES (?, ?, ?)");
$stmt2->bind_param("isd", $user_id, $account_number, $initial_balance);

        $stmt2->execute();

        header("Location: login.php?registered=1");
    } else {
        echo "Registration failed: " . $stmt->error;
    }
}
?>

<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h2><i class="fas fa-user-plus"></i> Register</h2>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" required class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" required class="form-control">
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
