<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

include "includes/config.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception($_SERVER["REQUEST_METHOD"] . ' Method Not Allowed', 405);
    }

    // Get POST values
    $item_name = isset($_POST['item_name']) ? addslashes(trim($_POST['item_name'])) : null;
    $item_code = isset($_POST['item_code']) ? addslashes(trim($_POST['item_code'])) : null;
    $category = isset($_POST['category']) ? addslashes(trim($_POST['category'])) : null;
    $unit_price = isset($_POST['unit_price']) ? floatval($_POST['unit_price']) : null;
    $initial_quantity = isset($_POST['initial_quantity']) ? intval($_POST['initial_quantity']) : null;
    $minimum_stock_level = isset($_POST['minimum_stock_level']) ? intval($_POST['minimum_stock_level']) : null;

    // Validate required fields
    $missingFields = [];
    if (empty($item_name))
        $missingFields[] = 'Item Name';
    if (empty($item_code))
        $missingFields[] = 'Item Code';
    if (empty($category))
        $missingFields[] = 'Category';
    if ($unit_price === null)
        $missingFields[] = 'Unit Price';
    if ($initial_quantity === null)
        $missingFields[] = 'Initial Quantity';
    if ($minimum_stock_level === null)
        $missingFields[] = 'Minimum Stock Level';

    if (!empty($missingFields)) {
        throw new Exception('Missing Field(s): ' . implode(', ', $missingFields), 400);
    }

    // Check if item_code already exists
    $CheckItemQuery = "SELECT * FROM items WHERE item_code = '$item_code' LIMIT 1";
    $CheckItemResult = mysqli_query($conn, $CheckItemQuery);

    if (mysqli_num_rows($CheckItemResult) > 0) {
        echo json_encode(['status' => 409, 'message' => 'Item Code already exists']);
        exit;
    }

    // Insert new item
    $InsertQuery = "INSERT INTO items (item_name, item_code, category, unit_price, quantity, minimum_stock_level)
                    VALUES ('$item_name', '$item_code', '$category', $unit_price, $initial_quantity, $minimum_stock_level)";

    if (mysqli_query($conn, $InsertQuery)) {
        echo json_encode([
            'status' => 201,
            'message' => 'Item added successfully',
            'item' => [
                'item_name' => $item_name,
                'item_code' => $item_code,
                'category' => $category,
                'unit_price' => $unit_price,
                'quantity' => $initial_quantity,
                'minimum_stock_level' => $minimum_stock_level
            ]
        ]);
    } else {
        throw new Exception('Insert failed: ' . mysqli_error($conn), 500);
    }

} catch (Exception $e) {
    $status = $e->getCode() ? $e->getCode() : 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>