<?php
include('../../includes/config.php');

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid item ID"]);
    exit;
}

// Fetch item along with purchase_date and sales_count
$sql = "
    SELECT 
        i.*,
        p.purchase_date,
        COALESCE(s.sales_count, 0) AS sales_count
    FROM items i
    LEFT JOIN purchase_order p ON i.item_code = p.purchase_code
    LEFT JOIN (
        SELECT item_id, COUNT(*) AS sales_count
        FROM sales
        GROUP BY item_id
    ) s ON i.id = s.item_id
    WHERE i.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    
    // Format purchase_date if available
    $item['purchase_date'] = $item['purchase_date'] ? date("d-m-Y", strtotime($item['purchase_date'])) : null;
    $item['sales_count'] = (int)$item['sales_count']; // ensure it's integer

    echo json_encode(["status" => "success", "data" => $item]);
} else {
    echo json_encode(["status" => "error", "message" => "Item not found"]);
}

$stmt->close();
$conn->close();
?>
