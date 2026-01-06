<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['crust-name']);
    $description = trim($_POST['crust-description']);
    $price = (float)$_POST['crust-price'];

    $stmt = $conn->prepare("INSERT INTO crusts (name, description, additional_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $description, $price);

    if ($stmt->execute()) {
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'menu_admin_page.php';
        echo '<script>alert("Crust added successfully!"); window.location.href = "' . $referrer . '";</script>';
    } else {
        echo '<script>alert("Error adding the crust: ' . $stmt->error . '"); window.history.back();</script>';
    }

    $stmt->close();
}
$conn->close();
?>
