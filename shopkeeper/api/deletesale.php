<?php
include('../../includes/config.php');

header("Content-Type: application/json");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Get the sale ID from POST
$saleId = intval($_POST['id'] ?? 0);

if ($saleId <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid sale ID"]);
    exit;
}

// Check if the sale exists
$stmt = $conn->prepare("SELECT id FROM sales WHERE id = ?");
$stmt->bind_param("i", $saleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Sale not found"]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Delete the sale
$deleteStmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
$deleteStmt->bind_param("i", $saleId);

if ($deleteStmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Sale deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete sale"]);
}

$deleteStmt->close();
$conn->close();
?>