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

    // Get values from form-data
    $bioid = isset($_POST['bioid']) ? addslashes(trim($_POST['bioid'])) : null;
    $password = isset($_POST['password']) ? addslashes(trim($_POST['password'])) : null;

    if (empty($bioid) || empty($password)) {
        throw new Exception('Missing Bio ID or Password', 400);
    }

    $CheckUserQuery = "SELECT * FROM login WHERE bioid = '$bioid' AND password = '$password'";
    $CheckUserQueryResults = mysqli_query($conn, $CheckUserQuery);

    if (mysqli_num_rows($CheckUserQueryResults) > 0) {
        $record = mysqli_fetch_assoc($CheckUserQueryResults);

        $AccountType = !empty($record["usertype"]) ? $record["usertype"] : "shopkeeper";

        // Map user types to pages
        $redirect_pages = [
            "admin" => "admin/admin_dashboard.php",
            "shopkeeper" => "shokeeper/shopkeeper_dashboard.php"
        ];

        $redirect_url = isset($redirect_pages[$AccountType]) ? $redirect_pages[$AccountType] : "default_dashboard.php";

        echo json_encode([
            'status' => 200,
            'message' => 'Login Success',
            'user_id' => $record["u_id"],
            'user_name' => $record["name"],
            'bioid' => $record["bioid"],
            'user_type' => $AccountType,
            'redirect_url' => $redirect_url
        ]);
    } else {
        echo json_encode(['status' => 401, 'message' => 'Invalid Bio ID or Password']);
    }

} catch (Exception $e) {
    $status = $e->getCode() ? $e->getCode() : 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>