<?php
include('../../config/config.php');

// Set header to return JSON
header('Content-Type: application/json');

if (isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // Fetch all columns for the given item_id
    $query = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // If sub_category is empty, set default
        if (empty($row['sub_category'])) {
            $row['sub_category'] = 'No subcategory found';
        }

        // Rename item_quantity to available_quantity
        $row['available_quantity'] = (int) $row['item_quantity'];
        unset($row['item_quantity']); // optional: remove original key

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

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Item ID not provided'
    ]);
}

$conn->close();
?>
