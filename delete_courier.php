<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection Error.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['courier_id'])) {
    $courier_id = (int) $_POST['courier_id'];

    $stmt = $conn->prepare("DELETE FROM couriers WHERE courier_id = ?");
    $stmt->bind_param("i", $courier_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Error deleting: " . $stmt->error;
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Bad request";
}

$conn->close();
?>