<?php
include('../../config/config.php'); // your DB connection

$id = $_POST['vendor_id'] ?? '';
$vendor_name = $_POST['vendor_name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile_number'] ?? '';
$pan = $_POST['pan_number'] ?? '';
$address = $_POST['address'] ?? '';

if (!$vendor_name || !$email || !$mobile || !$address) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields']);
    exit;
}

if ($id) {
    // Update existing vendor
    $stmt = $conn->prepare("UPDATE vendors SET vendor_name=?, email=?, mobile_number=?, pan_number=?, address=? WHERE id=?");
    $stmt->bind_param("sssssi", $vendor_name, $email, $mobile, $pan, $address, $id);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Vendor updated successfully']);
} else {
    // Insert new vendor
    $stmt = $conn->prepare("INSERT INTO vendors (vendor_name,email,mobile_number,pan_number,address) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $vendor_name, $email, $mobile, $pan, $address);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Vendor added successfully']);
}
$stmt->close();
$conn->close();
?>