<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include __DIR__ . '/../../includes/config.php';

// Check login
if (!isset($_SESSION['bio_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in']);
    exit;
}

// Get POST data
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$id || !in_array($status, ['accepted', 'rejected'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Update transfer status
    $stmt = $conn->prepare("UPDATE item_transfers SET transfer_status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    if ($status === 'accepted') {
        // Get transfer details
        $stmt = $conn->prepare("SELECT item_name, shared_quantity, available_quantity FROM item_transfers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transfer = $result->fetch_assoc();
        $stmt->close();

        if ($transfer) {
            $item_name = $transfer['item_name'];
            $shared_qty = (int) $transfer['shared_quantity'];
            $available_qty = (int) $transfer['available_quantity'];

            // Reduce available_quantity in item_transfers
            $new_available = $available_qty - $shared_qty;
            $stmt = $conn->prepare("UPDATE item_transfers SET available_quantity=? WHERE id=?");
            $stmt->bind_param("ii", $new_available, $id);
            $stmt->execute();
            $stmt->close();

            // Reduce item quantity in items table
            $stmt = $conn->prepare("UPDATE items SET item_quantity = item_quantity - ? WHERE item_name=?");
            $stmt->bind_param("is", $shared_qty, $item_name);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => "Transfer has been $status."]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to update transfer: ' . $e->getMessage()]);
}
?>