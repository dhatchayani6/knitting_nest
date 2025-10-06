<?php
header('Content-Type: application/json');
include __DIR__ . '/../../includes/config.php'; // adjust path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $item_code = $_POST['item_code'] ?? '';
    $from_store_id = $_POST['from_store_id'] ?? '';
    $to_store_id = $_POST['to_store_id'] ?? 0;
    $available_quantity = $_POST['available_quantity'] ?? 0;
    $shared_quantity = $_POST['shared_quantity'] ?? 0;

    if (empty($item_name) || empty($item_code) || !$from_store_id || !$to_store_id) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Handle file upload
    $imagePath = null; // Will hold the stored image path

    if (isset($_FILES['items_image']) && $_FILES['items_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['items_image']['tmp_name'];
    $fileName = $_FILES['items_image']['name'];
    $fileSize = $_FILES['items_image']['size'];
    $fileType = $_FILES['items_image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Allowed file extensions
    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExtension, $allowedfileExtensions)) {
        // Sanitize file name and create a unique name to avoid conflicts
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Directory where the file will be stored
        $uploadFileDir = __DIR__ . '/../../uploads/'; // adjust path

        // Check if directory exists
        if (!is_dir($uploadFileDir)) {
            echo json_encode([
                'success' => false,
                'message' => 'Upload directory does not exist.'
            ]);
            exit;
        }

        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $imagePath = 'uploads/' . $newFileName; // relative path to store in DB
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'There was an error moving the uploaded file.'
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Upload failed. Allowed file types: ' . implode(', ', $allowedfileExtensions)
        ]);
        exit;
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No file uploaded or upload error.'
    ]);
    exit;
}



    // Insert into database
    $stmt = $conn->prepare("INSERT INTO item_transfers (item_id, item_code, from_store_id, to_store_id, available_quantity, shared_quantity,imagePath) VALUES (?, ?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssiiiis", $item_name, $item_code, $from_store_id, $to_store_id, $available_quantity, $shared_quantity, $imagePath);

    if ($stmt->execute()) {
        // Get the inserted ID
        $inserted_id = $stmt->insert_id;

        // Return JSON response with the inserted data
        echo json_encode([
            'success' => true,
            'message' => 'Item transferred successfully!',
            'data' => [
                'id' => $inserted_id,
                'item_name' => $item_name,
                'item_code' => $item_code,
                'from_store_id' => $from_store_id,
                'to_store_id' => $to_store_id,
                'available_quantity' => $available_quantity,
                'shared_quantity' => $shared_quantity,
                'imagePath' => $imagePath,
                'created_at' => date('Y-m-d H:i:s') // optional, timestamp of insertion
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $conn->error
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>