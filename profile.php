<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'];
$encodedId = base64_encode($loggedInUserId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background-color: #f0f2f5;
    }
    .profile-wrapper {
      max-width: 600px;
      margin: 60px auto;
    }
    .card-profile {
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
    }
    .profile-title {
      background-color: #343a40;
      color: #fff;
      padding: 20px;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
      text-align: center;
    }
    .profile-item {
      margin-bottom: 10px;
      font-size: 1rem;
    }
  </style>
</head>
<body>

<div class="container profile-wrapper">
  <div class="card card-profile">
    <div class="profile-title">
      <h3>User Profile</h3>
    </div>
    <div class="card-body" id="profileContent">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function () {
    const encodedId = '<?= $encodedId ?>';

    $.ajax({
      url: 'api/view-user.php?id=' + encodeURIComponent(encodedId),
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        if (data.error) {
          $('#profileContent').html('<div class="alert alert-danger">' + data.error + '</div>');
        } else {
          $('#profileContent').html(`
            <div class="profile-item"><strong>Name:</strong> ${data.name}</div>
            <div class="profile-item"><strong>Email:</strong> ${data.email}</div>
            <div class="profile-item"><strong>Role:</strong> ${data.role}</div>
            <div class="profile-item"><strong>Created At:</strong> ${data.created_at}</div>
       
          `);
        }
      },
      error: function () {
        $('#profileContent').html('<div class="alert alert-danger">Failed to fetch profile data.</div>');
      }
    });
  });
</script>

</body>
</html>
