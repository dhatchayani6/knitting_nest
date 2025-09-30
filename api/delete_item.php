<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST"); // Using POST for form-data
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

    // Delete the item
    $DeleteQuery = "DELETE FROM items WHERE item_code = '$item_code'";
    if (mysqli_query($conn, $DeleteQuery)) {
        echo json_encode([
            'status' => 200,
            'message' => 'Item deleted successfully',
            'item_code' => $item_code
        ]);
    } else {
        throw new Exception('Delete failed: ' . mysqli_error($conn), 500);
    }

} catch (Exception $e) {
    $status = $e->getCode() ? $e->getCode() : 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>