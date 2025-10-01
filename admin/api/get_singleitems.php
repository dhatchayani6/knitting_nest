<?php
include('../../includes/config.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid item ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $item]);
} else {
    echo json_encode(["status" => "error", "message" => "Item not found"]);
}

$stmt->close();
$conn->close();
