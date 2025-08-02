<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transfer Funds</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    .container {
      max-width: 500px;
      margin: 80px auto;
      background: #fff;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 28px;
      color: #222;
    }
    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: 500;
    }
    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #111;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #444;
    }
    #response {
      background-color: #f4f4f4;
      padding: 15px;
      margin-top: 20px;
      border-radius: 6px;
      font-size: 14px;
      overflow: auto;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Transfer Funds</h2>
    <form id="transferForm">
      <label for="from_account_id">From Account ID:</label>
      <input type="text" id="from_account_id" name="from_account_id" required>

      <label for="to_account_id">To Account ID:</label>
      <input type="text" id="to_account_id" name="to_account_id" required>

      <label for="amount">Amount:</label>
      <input type="number" id="amount" name="amount" required>

      <button type="submit">Transfer</button>
    </form>

    <pre id="response"></pre>
  </div>

  <script>
    document.getElementById("transferForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      const from_account_id = document.getElementById("from_account_id").value;
      const to_account_id = document.getElementById("to_account_id").value;
      const amount = document.getElementById("amount").value;

      const response = await fetch("api/transfer.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          from_account_id,
          to_account_id,
          amount
        })
      });

      const data = await response.json();
      document.getElementById("response").textContent = JSON.stringify(data, null, 2);
    });
  </script>


</body>
</html>
