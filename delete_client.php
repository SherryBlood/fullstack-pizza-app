<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    http_response_code(500);
    echo "Connection Error.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['client_id'])) {
    $client_id = (int) $_POST['client_id'];


    if ($client_id === 1) {
        http_response_code(403);
        echo "This client cannot be deleted.";
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->bind_param("i", $client_id);

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
