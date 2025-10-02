<?php
header('Content-Type: application/json');
include __DIR__ . '/../../includes/config.php'; // Adjust path

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['id'] ?? ''; // coming from select value

    if (!$item_name) {
        echo json_encode(['success' => false, 'message' => 'No item selected.']);
        exit;
    }

    // Use item_name instead of id
    $stmt = $conn->prepare("SELECT item_name, item_code, item_quantity FROM items WHERE item_name = ?");
    $stmt->bind_param("s", $item_name); // bind as string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'data' => [
                'item_name' => $data['item_name'],
                'item_code' => $data['item_code'],
                'available_quantity' => (int) $data['item_quantity']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>