<?php


// Allow CORS & set JSON header
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept");

// Include DB config
include "includes/config.php";

try {
    // Only allow POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception($_SERVER["REQUEST_METHOD"] . ' Method Not Allowed', 405);
    }

    // Get POST values (form-data)
    $bioid = isset($_POST['bio_id']) ? trim($_POST['bio_id']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;

    if (empty($bioid) || empty($password)) {
        throw new Exception('Missing Bio ID or Password', 400);
    }

    // Prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM login WHERE bio_id = ? AND password = ?");
    $stmt->bind_param("ss", $bioid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();

        // Determine user type
        $AccountType = strtolower($record["usertype"]); // ensure lowercase for comparison

        // Map user types to redirect pages
        if ($AccountType === "admin") {
            $redirect_url = "admin/admin_index.php";
        } elseif ($AccountType === "shopkeeper") {
            $redirect_url = "shopkeeper/shopkeeper_dashboard.php";
        } else {
            $redirect_url = "index.php"; // fallback
        }

        // Store session info
        $_SESSION['user_id'] = $record["id"];
        $_SESSION['bioid'] = $record["bio_id"];
        $_SESSION['user_type'] = $AccountType;

        // Return JSON
        echo json_encode([
            'status' => 200,
            'message' => 'Login Success',
            'user_id' => $record["id"],
            'bioid' => $record["bio_id"],
            'user_type' => $AccountType,
            'redirect_url' => $redirect_url
        ]);

    } else {
        // Invalid credentials
        echo json_encode([
            'status' => 401,
            'message' => 'Invalid Bio ID or Password'
        ]);
    }

} catch (Exception $e) {
    $status = $e->getCode() ?: 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>
