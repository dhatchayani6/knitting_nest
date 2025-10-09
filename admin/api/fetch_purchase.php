<?php
header("Content-Type: application/json"); //we recieve data from json formate
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ .'/../../includes/config.php'; // Include database connection

// Fetch all stores
$sql = "SELECT id, purchase_name, purchase_code, purchase_date,distributor_name,purchase_quantity,items_image FROM purchase_order ORDER BY id ASC";
$result = $conn->query($sql);


$purchase = [];
if ($result && $result->num_rows > 0) {
    
    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $purchase[] = [
            "sno" => $sno++,//add serial number
            "id" => $row['id'],
             "purchase_name" => $row['purchase_name'],
            "purchase_code" => $row['purchase_code'],
            "purchase_date" => $row['purchase_date'],
            "distributor_name" => $row['distributor_name'],
            "purchase_quantity" => $row['purchase_quantity'],
            "items_image" => $row['items_image'],
        ];
    }

    // ✅ Success response with HTTP 200
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Purchase fetched successfully",
        "data" => $purchase
    ]);
} else {
    // ⚠️ No data found — HTTP 404
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "message" => "No records found"
    ]);
}

$conn->close();
?>
