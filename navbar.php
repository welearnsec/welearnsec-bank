<?php
// Start session only if none is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WeLearnSec-Bank</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f5f8fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.3rem;
    }
    .nav-link {
      font-size: 1rem;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
  <a class="navbar-brand" href="index.php"><i class="fas fa-university me-2"></i>WeLearnSec-Bank</a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto">
     

      <?php if (isset($_SESSION['user'])): ?>
    <li class="nav-item">
      <a class="nav-link" href="dashboard.php"><i class="fas fa-user-circle me-1"></i>Dashboard</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="profile.php"><i class="fas fa-user-circle me-1"></i>Profile</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
    </li>
<?php else: ?>
    <li class="nav-item">
      <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
    </li>
<?php endif; ?>

    </ul>
  </div>
</nav>
