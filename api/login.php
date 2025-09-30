<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");

include "../includes/config.php";

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception($_SERVER["REQUEST_METHOD"] . ' Method Not Allowed', 405);
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    $bioid = isset($input['bioid']) ? addslashes(trim($input['bioid'])) : null;
    $password = isset($input['password']) ? addslashes(trim($input['password'])) : null;

    if (empty($bioid) || empty($password)) {
        throw new Exception('Missing Bio ID or Password', 400);
    }

    $CheckUserQuery = "SELECT * FROM login WHERE bio_id = '$bioid' AND password = '$password'";
    $CheckUserQueryResults = mysqli_query($conn, $CheckUserQuery);

    if (mysqli_num_rows($CheckUserQueryResults) > 0) {
        $record = mysqli_fetch_assoc($CheckUserQueryResults);

        $AccountType = !empty($record["usertype"]) ? $record["usertype"] : "shopkeeper";

        // Store user info in session
        $_SESSION['user_id']=$record['id'];
        $_SESSION['bio_id'] = $record['bio_id'];
        $_SESSION['usertype'] = $AccountType;
        $_SESSION['name'] = $record['name'] ?? ''; // optional
        $_SESSION['logged_in'] = true;

        // Return login info without redirection URLs
        echo json_encode([
            'status' => 200,
            'message' => 'Login Success',
            'bio_id' => $record["bio_id"],
            'usertype' => $AccountType
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