<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include __DIR__ . '/../../config/config.php';

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
        $stmt = $conn->prepare("SELECT item_id, shared_quantity, available_quantity, to_store_id, from_store_id FROM item_transfers WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transfer = $result->fetch_assoc();
        $stmt->close();

        if ($transfer) {
            $item_id = $transfer['item_id'];
            $shared_qty = (int) $transfer['shared_quantity'];
            $available_qty = (int) $transfer['available_quantity'];
            $to_store_id = $transfer['to_store_id'];
            $from_store_id = $transfer['from_store_id'];

            // Reduce available_quantity in item_transfers
            $new_available = $available_qty - $shared_qty;
            $stmt = $conn->prepare("UPDATE item_transfers SET available_quantity=? WHERE id=?");
            $stmt->bind_param("ii", $new_available, $id);
            $stmt->execute();
            $stmt->close();

            // Reduce quantity in source item (based on from_store_id)
            $stmt = $conn->prepare("UPDATE items SET item_quantity = item_quantity - ? WHERE id=? AND store_id=?");
            $stmt->bind_param("iii", $shared_qty, $item_id, $from_store_id);
            $stmt->execute();
            $stmt->close();

            // Get source item details
            $stmt = $conn->prepare("SELECT item_name, item_code, items_image, item_price, stock_level FROM items WHERE id=?");
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $source_item = $result->fetch_assoc();
            $stmt->close();

            if ($source_item) {
                $item_name = $source_item['item_name'];
                $item_code = $source_item['item_code'];
                $items_image = $source_item['items_image'];
                $item_price = $source_item['item_price'];
                $stock_level = $source_item['stock_level'];

                // Get store name based on to_store_id
                $stmt = $conn->prepare("SELECT stores_name FROM shops WHERE id=?");
                $stmt->bind_param("i", $to_store_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $store_row = $result->fetch_assoc();
                $stmt->close();
                $store_name = $store_row ? $store_row['stores_name'] : '';

                // Check if item exists in destination store using item_code
                $stmt = $conn->prepare("SELECT id, item_quantity FROM items WHERE item_code=? AND store_id=?");
                $stmt->bind_param("si", $item_code, $to_store_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $dest_item = $result->fetch_assoc();
                $stmt->close();

                if ($dest_item) {
                    // Item exists → update quantity
                    $new_qty = $dest_item['item_quantity'] + $shared_qty;
                    $stmt = $conn->prepare("UPDATE items SET item_quantity=? WHERE id=?");
                    $stmt->bind_param("ii", $new_qty, $dest_item['id']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Item does not exist → insert new record
                    $stmt = $conn->prepare("INSERT INTO items (store_id, store_name, item_name, item_code, item_quantity, items_image, item_price, stock_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssisii", $to_store_id, $store_name, $item_name, $item_code, $shared_qty, $items_image, $item_price, $stock_level);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => "Transfer has been $status."]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Failed to update transfer: ' . $e->getMessage()]);
}
?>