<?php
include('../../includes/config.php');

// Set header to JSON
header('Content-Type: application/json');

// Check required fields
if (isset($_POST['item_name'], $_POST['total_items'], $_POST['item_price'], $_POST['store_id'])) {
    $item_id = $_POST['item_name'];
    $total_items = intval($_POST['total_items']);
    $item_price = floatval($_POST['item_price']);
    $store_id = intval($_POST['store_id']); // Get store_id from POST

    // Fetch item details to get current quantity
    $stmt = $conn->prepare("SELECT item_name, item_code, item_quantity FROM items WHERE id = ? AND store_id = ?");
    $stmt->bind_param("ii", $item_id, $store_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $item_name = $row['item_name'];
        $item_code = $row['item_code'];
        $initial_quantity = $row['item_quantity']; // Save initial stock

        // Check if enough stock
        if ($total_items > $initial_quantity) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Not enough stock available.'
            ]);
            exit;
        }

        // Calculate remaining quantity
        $remaining_quantity = $initial_quantity - $total_items;

        // Insert into sales table including store_id, initial and remaining quantities
        $stmt2 = $conn->prepare("INSERT INTO sales (item_id, store_id, item_name, item_code, total_items, item_price, item_quantity, remaining_quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iissidii", $item_id, $store_id, $item_name, $item_code, $total_items, $item_price, $initial_quantity, $remaining_quantity);

        if ($stmt2->execute()) {
            // Update item quantity in items table
            $stmt3 = $conn->prepare("UPDATE items SET item_quantity = ? WHERE id = ? AND store_id = ?");
            $stmt3->bind_param("iii", $remaining_quantity, $item_id, $store_id);
            $stmt3->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Sale added successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add sale.'
            ]);
        }

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Item not found in this store.'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Required fields missing.'
    ]);
}
?>