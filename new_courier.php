<?php
$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

$first_name = trim($_POST['courier-name']);
$last_name = trim($_POST['courier-last-name']);
$phone = trim($_POST['courier-tel']);

$sql = "INSERT INTO couriers (first_name, last_name, phone_number) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $first_name, $last_name, $phone);

if ($stmt->execute()) {
    echo "<script>alert('Courier added!'); window.location.href='couriers_admin_page.php';</script>";
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
