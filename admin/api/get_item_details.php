<?php
header('Content-Type: application/json');
include __DIR__ . '/../../config/config.php'; // Adjust path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['id'] ?? ''; // coming from select value

    if (!$item_id) {
        echo json_encode(['success' => false, 'message' => 'No item selected.']);
        exit;
    }

    // Fetch all items with this ID
    $stmt = $conn->prepare("
        SELECT 
            id,
            store_id,
            store_name,
            item_name,
            item_code,
            item_quantity,
            sub_category
            
        FROM items 
        WHERE id = ?
    ");
    $stmt->bind_param("s", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id' => $row['id'],
                'store_id' => $row['store_id'],
                'store_name' => $row['store_name'],
                'item_name' => $row['item_name'],
                'item_code' => $row['item_code'],
                'available_quantity' => (int) $row['item_quantity'],
                'sub_category' => $row['sub_category'],
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => $items
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
