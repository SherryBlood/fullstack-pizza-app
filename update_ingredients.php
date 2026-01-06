<?php
session_start();
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ingredient_ids'];
    $names = $_POST['names'];
    $prices = $_POST['prices'];

    for ($i = 0; $i < count($ids); $i++) {
        $id = (int) $ids[$i];
        $name = trim($names[$i]);
        $price = (float) $prices[$i];

        $stmt = $conn->prepare("UPDATE ingredients SET name = ?, price = ? WHERE ingredient_id = ?");
        $stmt->bind_param("sdi", $name, $price, $id);
        $stmt->execute();
        $stmt->close();
    }

    echo '<script>alert("Ingredients updated successfully!"); window.location.href = document.referrer;</script>';
}

$conn->close();
?>
