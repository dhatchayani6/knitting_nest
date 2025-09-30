<?php

// Allow CORS & set JSON response
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include __DIR__ .'/../../includes/config.php';

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Only POST method allowed"]);
    exit();
}

// Get POST data The ternary operator is shorthand for if-else.
// condition ? value_if_true : value_if_false;
$stores_name = isset($_POST['stores_name']) ? $_POST['stores_name'] : null;
$stores_location = isset($_POST['stores_location']) ? $_POST['stores_location'] : null;
$usertype = isset($_POST['usertype']) && !empty($_POST['usertype']) ? $_POST['usertype'] : "Admin";

// Validate input
if (!$stores_name || !$stores_location || !$usertype) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit();
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO shops (stores_name, stores_location, usertype) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $stores_name, $stores_location, $usertype);

// Execute
if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Shop inserted successfully", "id" => $stmt->insert_id]);
} else {
    http_response_code(500); // Internal Server Error
    echo json_encode(["status" => "error", "message" => "Insertion failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
