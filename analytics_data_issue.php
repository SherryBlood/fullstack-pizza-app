<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$data = [
    'pickup' => [],
    'delivery' => []
];


$pickupRes = $conn->query("
    SELECT 
        TIME(o.order_datetime) AS time_ready,
        TIME(io.issued_at) AS time_issued
    FROM orders o
    JOIN issued_orders io ON o.order_id = io.order_id
    WHERE o.type_delivery = 'self_pickup'
      AND o.order_status = 'issued'
      AND DATE(o.order_datetime) = CURDATE()
");

while ($row = $pickupRes->fetch_assoc()) {
    $data['pickup'][] = [
        'ready' => $row['time_ready'],
        'issued' => $row['time_issued']
    ];
}

$deliveryRes = $conn->query("
    SELECT 
        TIME(d.delivery_datetime) AS time_planned,
        TIME(io.issued_at) AS time_issued
    FROM delivery d
    JOIN issued_orders io ON d.order_id = io.order_id
    JOIN orders o ON o.order_id = d.order_id
    WHERE o.type_delivery = 'address_pickup'
      AND o.order_status = 'issued'
      AND DATE(d.delivery_datetime) = CURDATE()
");

while ($row = $deliveryRes->fetch_assoc()) {
    $data['delivery'][] = [
        'planned' => $row['time_planned'],
        'actual' => $row['time_issued']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

?>
