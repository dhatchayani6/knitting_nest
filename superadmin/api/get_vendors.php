<?php
include('../../config/config.php');

$result = $conn->query("SELECT * FROM vendors ORDER BY id DESC");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $data]);
$conn->close();
?>