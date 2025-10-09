<?php
include('../../includes/config.php');

// Set header to return JSON
header('Content-Type: application/json');

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    $query = "SELECT item_code, item_quantity AS available_quantity, sub_category AS subcategory ,item_price
              FROM items 
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Check if subcategory is empty or null
        if (empty($row['subcategory'])) {
            $row['subcategory'] = 'No subcategory found';
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Item details fetched successfully',
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No item found with this ID'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Item ID not provided'
    ]);
}
?>
