<?php
include('../config/config.php');

$type = $_GET['type'] ?? 'sales';

function renderTable($data, $headers, $rows)
{
    if ($data->num_rows === 0) {
        echo "<p class='text-center text-muted my-4'>🚫 No data found.</p>";
        return;
    }

    echo "<table class='table table-bordered table-striped'>
            <thead class='table-light'>
                <tr>";
    foreach ($headers as $head) {
        echo "<th>$head</th>";
    }
    echo "  </tr>
            </thead>
            <tbody>";

    $i = 1;
    while ($row = $data->fetch_assoc()) {
        echo "<tr><td>{$i}</td>";
        foreach ($rows as $col) {
            echo "<td>" . htmlspecialchars($row[$col]) . "</td>";
        }
        echo "</tr>";
        $i++;
    }
    echo "</tbody></table>";
}

if ($type === 'sales') {
    echo "<h5 class='mb-3 text-success'>📦 Sales Data</h5>";
    $data = $conn->query("SELECT item_name, item_quantity, DATE(created_at) AS created_date FROM sales ORDER BY created_at DESC LIMIT 10");
    renderTable($data, ['S No', 'Item Name', 'Quantity', 'Date'], ['item_name', 'item_quantity', 'created_date']);

} elseif ($type === 'stock') {
    echo "<h5 class='mb-3 text-primary'>📦 Total Stock</h5>";
    $data = $conn->query("SELECT purchase_name, purchase_quantity, DATE(purchase_date) AS purchase_date FROM purchase_order ORDER BY purchase_date DESC LIMIT 10");
    renderTable($data, ['S No', 'Purchase Name', 'Quantity', 'Purchase Date'], ['purchase_name', 'purchase_quantity', 'purchase_date']);

} elseif ($type === 'bought') {
    echo "<h5 class='mb-3 text-warning'>🕒 Stock Bought (Last 30 Days)</h5>";
    $data = $conn->query("SELECT purchase_name, purchase_quantity, DATE(purchase_date) AS purchase_date 
                          FROM purchase_order 
                          WHERE purchase_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                          ORDER BY purchase_date DESC");
    renderTable($data, ['S No', 'Purchase Name', 'Quantity', 'Purchase Date'], ['purchase_name', 'purchase_quantity', 'purchase_date']);
}
?>