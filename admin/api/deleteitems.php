<?php
include('../../includes/config.php'); 

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$id = intval($_POST['id']);
if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM items WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Item deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Delete failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
