<?php
include('../../includes/config.php');
session_start();

header('Content-Type: application/json');

// ✅ Step 1: Get logged-in user's bio_id from session
$bio_id = $_SESSION['bio_id'] ?? 0;
$shop_id = 0;

// ✅ Step 2: Find the shop_id assigned to this bio_id
if ($bio_id) {
    $stmt = $conn->prepare("SELECT shop_id FROM shopkeeper WHERE shopkeeper_bioid = ?");
    $stmt->bind_param("i", $bio_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $shop_id = $row['shop_id'];
    }
    $stmt->close();
}

// ✅ Step 3: If no shop_id found, return an error
if (!$shop_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No shop found for this user',
        'data' => []
    ], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Step 4: Fetch all sales related to this shop_id
// 🗓️ Get today's date (Y-m-d format to match MySQL DATE)
// 🗓️ Get selected date, default to today
$date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : date('Y-m-d');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// ✅ Total rows for pagination
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM sales WHERE DATE(created_at) = '$date'");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query = "
    SELECT 
        id,
        store_id,
        item_code,
        item_name,
        total_items,
        item_price,
        item_quantity,
        remaining_quantity,
        DATE_FORMAT(created_at, '%d-%m-%Y') AS created_date
    FROM sales
    WHERE store_id = ?
     WHERE DATE(created_at) = '$date'   -- ✅ Only today's records
    ORDER BY created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt2 = $conn->prepare($query);
$stmt2->bind_param("i", $shop_id);
$stmt2->execute();
$result = $stmt2->get_result();

// ✅ Step 5: Format result
if ($result && $result->num_rows > 0) {
    $sales = [];
    while ($row = $result->fetch_assoc()) {
        $sales[] = [
            'id' => (int) $row['id'],
            'store_id' => (int) $row['store_id'],
            'item_code' => $row['item_code'],
            'item_name' => $row['item_name'],
            'total_items' => (int) $row['total_items'],
            'item_price' => (float) $row['item_price'],
            'item_quantity' => (int) $row['item_quantity'],
            'remaining_quantity' => (int) $row['remaining_quantity'],
            'created_at' => $row['created_date']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'message' => ' Today Sales fetched successfully',
          'current_page' => $page,
        'total_pages' => $totalPages,
        'data' => $sales
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No sales found for this shop',
        'message' => 'No sales found',
         'current_page' => $page,
        'total_pages' => 0,
        'data' => []
    ], JSON_PRETTY_PRINT);
}

$stmt2->close();
$conn->close();
?>