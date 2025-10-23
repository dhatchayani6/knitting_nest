<?php
include('../../config/config.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Only POST method allowed']);
    exit;
}

// Get POST data
$purchase_item = $_POST['purchase_item'] ?? null;
$date_of_purchase = $_POST['date_of_purchase'] ?? null;
$purchase_code = $_POST['purchase_code'] ?? null;
$distributor_name = $_POST['distributor_name'] ?? null;
$purchase_quantity = $_POST['purchase_quantity'] ?? null;
$purchase_price = $_POST['purchase_price'] ?? null; // integer value

// Validate required fields
if (!$purchase_item || !$date_of_purchase || !$purchase_code || !$distributor_name || !$purchase_quantity || !$purchase_price) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Handle file upload
$items_image = null;
if (isset($_FILES['items_image']) && $_FILES['items_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['items_image']['tmp_name'];
    $fileName = $_FILES['items_image']['name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        $newFileName = uniqid('item_', true) . '.' . $fileExt;
        $uploadPath = __DIR__ . '/../../uploads/' . $newFileName;
        if (move_uploaded_file($fileTmpPath, $uploadPath)) {
            $items_image = 'uploads/' . $newFileName;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid image format']);
        exit;
    }
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO purchase_order (purchase_name, purchase_code, purchase_date, distributor_name, purchase_quantity, purchase_price, items_image) VALUES (?,?,?,?,?,?,?)");
$stmt->bind_param("ssssiis", $purchase_item, $purchase_code, $date_of_purchase, $distributor_name, $purchase_quantity, $purchase_price, $items_image);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Purchase added successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Insertion failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>