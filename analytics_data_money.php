<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to connect to the database"]);
    exit;
}

$orders = [];

$stmt = $conn->prepare("
  SELECT o.total_price, io.issued_at
  FROM orders o
  JOIN issued_orders io ON o.order_id = io.order_id
  WHERE io.issued_at >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-01')
    AND io.issued_at < DATE_ADD(DATE_FORMAT(CURRENT_DATE, '%Y-%m-01'), INTERVAL 1 MONTH)
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Request error"]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'date' => $row['issued_at'],
        'total' => (float)$row['total_price']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($orders);
?>
