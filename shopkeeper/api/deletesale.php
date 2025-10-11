<?php
include('../../includes/config.php');
header('Content-Type: application/json');

// 🧩 Check if ID is sent
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing sale ID'
    ]);
    exit;
}

$sale_id = (int)$_POST['id'];

// 🗑️ Delete sale record
$delete_query = "DELETE FROM sales WHERE id = ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param('i', $sale_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Sale deleted successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No sale found with this ID'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to delete sale: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
