<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST"); // using POST for form-data
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

include "includes/config.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception($_SERVER["REQUEST_METHOD"] . ' Method Not Allowed', 405);
    }

    // Get form-data values
    $item_code = isset($_POST['item_code']) ? addslashes(trim($_POST['item_code'])) : null;

    if (empty($item_code)) {
        throw new Exception('Missing Field: Item Code', 400);
    }

    // Check if item exists
    $CheckItemQuery = "SELECT * FROM items WHERE item_code = '$item_code' LIMIT 1";
    $CheckItemResult = mysqli_query($conn, $CheckItemQuery);

    if (mysqli_num_rows($CheckItemResult) == 0) {
        throw new Exception('Item not found', 404);
    }

    // Prepare update fields
    $updateFields = [];
    if (isset($_POST['item_name']) && !empty($_POST['item_name'])) {
        $updateFields[] = "item_name = '" . addslashes(trim($_POST['item_name'])) . "'";
    }
    if (isset($_POST['category']) && !empty($_POST['category'])) {
        $updateFields[] = "category = '" . addslashes(trim($_POST['category'])) . "'";
    }
    if (isset($_POST['unit_price']) && is_numeric($_POST['unit_price'])) {
        $updateFields[] = "unit_price = " . floatval($_POST['unit_price']);
    }
    if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
        $updateFields[] = "quantity = " . intval($_POST['quantity']);
    }
    if (isset($_POST['minimum_stock_level']) && is_numeric($_POST['minimum_stock_level'])) {
        $updateFields[] = "minimum_stock_level = " . intval($_POST['minimum_stock_level']);
    }

    if (empty($updateFields)) {
        throw new Exception('No fields to update', 400);
    }

    // Update query
    $UpdateQuery = "UPDATE items SET " . implode(", ", $updateFields) . " WHERE item_code = '$item_code'";

    if (mysqli_query($conn, $UpdateQuery)) {
        echo json_encode([
            'status' => 200,
            'message' => 'Item updated successfully',
            'item_code' => $item_code
        ]);
    } else {
        throw new Exception('Update failed: ' . mysqli_error($conn), 500);
    }

} catch (Exception $e) {
    $status = $e->getCode() ? $e->getCode() : 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>