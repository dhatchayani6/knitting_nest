<?php
header("Content-Type: application/json"); //we recieve data from json formate
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include __DIR__ .'/../../includes/config.php'; // Include database connection

// Fetch all stores
$sql = "SELECT id, stores_name, stores_location, usertype FROM shops ORDER BY id ASC";
$result = $conn->query($sql);


$stores = [];
if ($result && $result->num_rows > 0) {
    
    $sno = 1;
    while ($row = $result->fetch_assoc()) {
        $stores[] = [
            "sno" => $sno++,//add serial number
            "id" => $row['id'],
            "stores_name" => $row['stores_name'],
            "stores_location" => $row['stores_location'],
            "usertype" => $row['usertype']
        ];
    }

    // ✅ Success response with HTTP 200
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "stores fetched successfully",
        "data" => $stores
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
