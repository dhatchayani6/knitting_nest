<?php
header('Content-Type: application/json');
include __DIR__ . '/../../includes/config.php'; // adjust path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $item_code = $_POST['item_code'] ?? '';
    $from_store_id = $_POST['from_store_id'] ?? '';
    $to_store_id = $_POST['to_store_id'] ?? 0;
    $available_quantity = $_POST['available_quantity'] ?? 0;
    $shared_quantity = $_POST['shared_quantity'] ?? 0;

    if (empty($item_name) || empty($item_code) || !$from_store_id || !$to_store_id) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO item_transfers (item_id, item_code, from_store_id, to_store_id, available_quantity, shared_quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiii", $item_name, $item_code, $from_store_id, $to_store_id, $available_quantity, $shared_quantity);

    if ($stmt->execute()) {
        // Get the inserted ID
        $inserted_id = $stmt->insert_id;

        // Return JSON response with the inserted data
        echo json_encode([
            'success' => true,
            'message' => 'Item transferred successfully!',
            'data' => [
                'id' => $inserted_id,
                'item_name' => $item_name,
                'item_code' => $item_code,
                'from_store_id' => $from_store_id,
                'to_store_id' => $to_store_id,
                'available_quantity' => $available_quantity,
                'shared_quantity' => $shared_quantity,
                'created_at' => date('Y-m-d H:i:s') // optional, timestamp of insertion
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>