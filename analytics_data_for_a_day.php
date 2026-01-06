<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$days = [];
for ($i = 6; $i >= 0; $i--) {
    $days[] = date('Y-m-d', strtotime("-$i days"));
}

$result = $conn->query("
  SELECT DATE(order_datetime) AS day, COUNT(*) AS count
  FROM orders
  WHERE order_datetime >= CURDATE() - INTERVAL 6 DAY
  GROUP BY DATE(order_datetime)
  ORDER BY day
");

$orderCounts = [];
while ($row = $result->fetch_assoc()) {
    $orderCounts[$row['day']] = (int)$row['count'];
}

$data = [];
foreach ($days as $day) {
    $data[] = [
        'date' => $day,
        'count' => $orderCounts[$day] ?? 0
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
