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
    <title>View Statements</title>
    <link rel="stylesheet" href="assets/index.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container p-4 max-w-5xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Your Statements</h2>

    <form id="filterForm" class="mb-4 flex gap-2">
        <input type="month" id="month" name="month" class="border rounded px-2 py-1" />
        <button type="submit" class="bg-black text-white px-4 py-1 rounded">Filter</button>
    </form>

    <div id="statement-table" class="bg-white rounded-lg shadow p-4 overflow-x-auto">
        <p>Loading...</p>
    </div>

    <div class="flex justify-center mt-4" id="pagination-controls"></div>
</div>

<script>
const perPage = 10;
let statements = [];

function renderTable(page = 1) {
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const paginated = statements.slice(start, end);
    const table = document.getElementById('statement-table');

    if (paginated.length === 0) {
        table.innerHTML = "<p>No statements found.</p>";
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
                <td class="p-3 border">${tx.date}</td>
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

function loadStatements(month = '') {
    let url = 'api/statements.php';
    if (month) {
        url += `?month=${month}`;
    }
    fetch(url)
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('statement-table');
            if (data.error) {
                container.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                return;
            }

            statements = data.transactions || [];
            const totalPages = Math.ceil(statements.length / perPage);
            renderTable(1);
            renderPagination(totalPages, 1);
        })
        .catch(() => {
            document.getElementById('statement-table').innerHTML = `<p class="text-red-500">Failed to load statements.</p>`;
        });
}

// initial load with no filter (vulnerable, triggers 500 if unfiltered)
loadStatements();

document.getElementById('filterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const month = document.getElementById('month').value;
    loadStatements(month);
});
</script>

</body>
</html>
