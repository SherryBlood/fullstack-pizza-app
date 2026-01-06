<?php
session_start();

if (!isset($_SESSION['customer_id'])) {

    header("Location: main_client_page.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$first_name = trim($_POST['client-name-info']);
$last_name = trim($_POST['client-last-name-info']);
$phone_number = trim($_POST['client-tel-info']);
$address = trim($_POST['client-address-info']);
$email = trim($_POST['client-email-info']);



if ($first_name === '' || $last_name === '' || $email === '' || $phone_number === '' || $address === '') {
    echo "Please fill in all fields.";
    exit;
}


$sql = "UPDATE customers 
        SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ? 
        WHERE customer_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Request preparation error: " . $conn->error);
}

$stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone_number, $address, $customer_id);

if ($stmt->execute()) {

    header("Location: client_page.php?updated=1");
    exit;
} else {
    echo "Error updating: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
