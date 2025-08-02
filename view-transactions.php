<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Transactions</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container p-4 max-w-5xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Your Transactions</h2>
     <div class="flex justify-center mt-6">
    <a href="statements.php"
       style="display:inline-block; background:linear-gradient(to right, #2563eb, #1e3a8a); color:white; font-weight:bold; font-size:14px; padding:10px 20px; border-radius:9999px; text-decoration:none; box-shadow:0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;"
       onmouseover="this.style.background='linear-gradient(to right, #1d4ed8, #1e3a8a)'"
       onmouseout="this.style.background='linear-gradient(to right, #2563eb, #1e3a8a)'">
       📄 View Account Statement
    </a>
</div><br>
    <div id="transaction-table" class="bg-white rounded-lg shadow p-4 overflow-x-auto">
        <p>Loading...</p>
    </div>

    <div class="flex justify-center mt-4" id="pagination-controls"></div>
   


</div>

</div>

<script>
const perPage = 10;
let transactions = [];

function renderTable(page = 1) {
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = transactions.slice(start, end);
    const table = document.getElementById('transaction-table');

    if (paginated.length === 0) {
        table.innerHTML = "<p>No transactions found.</p>";
        return;
    }

    let html = `
        <table class="w-full text-sm text-left border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">ID</th>
                    <th class="p-3 border">Type</th>
                    <th class="p-3 border">Amount</th>
                    <th class="p-3 border">Date</th>
                </tr>
            </thead>
            <tbody>
    `;

    paginated.forEach(tx => {
        html += `
            <tr class="hover:bg-gray-50">
                <td class="p-3 border">${tx.id}</td>
                <td class="p-3 border capitalize">${tx.type}</td>
                <td class="p-3 border text-green-600 font-medium">₱${tx.amount}</td>
                <td class="p-3 border">${tx.created_at}</td>
            </tr>
        `;
    });

    html += '</tbody></table>';
    table.innerHTML = html;
}

function renderPagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination-controls');
    pagination.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = `mx-1 px-3 py-1 border rounded ${i === currentPage ? 'bg-black text-white' : 'bg-white text-black border-gray-300'}`;
        btn.onclick = () => {
            renderTable(i);
            renderPagination(totalPages, i);
        };
        pagination.appendChild(btn);
    }
}

// 🔥 Single vulnerable parameter
const targetId = <?= json_encode($_GET['account_id'] ?? $_SESSION['user_id']) ?>;

fetch('api/transactions.php?account_id=' + targetId)
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('transaction-table');
        if (data.error) {
            container.innerHTML = `<p class="text-red-500">${data.error}</p>`;
            return;
        }

        transactions = data.transactions || [];
        const totalPages = Math.ceil(transactions.length / perPage);
        renderTable(1);
        renderPagination(totalPages, 1);
    })
    .catch(() => {
        document.getElementById('transaction-table').innerHTML = `<p class="text-red-500">Failed to load data.</p>`;
    });
</script>

</body>
</html>
