<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['ingredient-name']);
    $price = (float) $_POST['ingredient-price'];

    $stmt = $conn->prepare("INSERT INTO ingredients (name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $price);

    if ($stmt->execute()) {
        echo '<script>alert("Ingredient added successfully!"); window.location.href = document.referrer;</script>';
    } else {
        echo '<script>alert("Error adding the ingredient: ' . $stmt->error . '"); window.history.back();</script>';
    }

    $stmt->close();
}
$conn->close();
?>
