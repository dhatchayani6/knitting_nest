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

    // Get form-data values
    $name = isset($_POST['name']) ? addslashes(trim($_POST['name'])) : null;
    $bioid = isset($_POST['bioid']) ? addslashes(trim($_POST['bioid'])) : null;
    $password = isset($_POST['password']) ? addslashes(trim($_POST['password'])) : null;
    $usertype = isset($_POST['usertype']) ? addslashes(trim($_POST['usertype'])) : "shopkeeper";

    $missingFields = [];
    if (empty($name))
        $missingFields[] = 'Name';
    if (empty($bioid))
        $missingFields[] = 'Bio ID';
    if (empty($password))
        $missingFields[] = 'Password';

    if (!empty($missingFields)) {
        throw new Exception('Missing Field(s): ' . implode(', ', $missingFields), 400);
    }

    // ✅ Check if bioid already exists
    $CheckUserQuery = "SELECT * FROM login WHERE bioid = '$bioid' LIMIT 1";
    $CheckUserResult = mysqli_query($conn, $CheckUserQuery);

    if (mysqli_num_rows($CheckUserResult) > 0) {
        echo json_encode(['status' => 409, 'message' => 'Bio ID already exists']);
        exit;
    }

    // ✅ Insert new user
    $InsertQuery = "INSERT INTO login (name, bioid, password, usertype) 
                    VALUES ('$name', '$bioid', '$password', '$usertype')";

    if (mysqli_query($conn, $InsertQuery)) {
        echo json_encode([
            'status' => 201,
            'message' => 'Signup successful',
            'user' => [
                'name' => $name,
                'bioid' => $bioid,
                'usertype' => $usertype
            ]
        ]);
    } else {
        throw new Exception('Signup failed: ' . mysqli_error($conn), 500);
    }

} catch (Exception $e) {
    $status = $e->getCode() ? $e->getCode() : 500;
    $message = $e->getMessage();
    echo json_encode(['status' => $status, 'message' => $message]);
}
?>