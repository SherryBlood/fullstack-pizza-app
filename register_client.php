<?php

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$first_name = $_POST['client-name'];
$last_name = $_POST['client-last-name'];
$phone = $_POST['client-tel'];
$address = $_POST['client-address'];
$email = $_POST['client-new-email'];
$password = $_POST['client-new-password'];


$check_sql = "SELECT customer_id FROM customers WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {

    echo '<script>
            alert("User with this email is already registered.");
            window.history.back();
          </script>';
    $check_stmt->close();
    $conn->close();
    exit;
}
$check_stmt->close();


$password_hash = password_hash($password, PASSWORD_DEFAULT);


$sql = "INSERT INTO customers (first_name, last_name, email, password_hash, phone_number, address) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Помилка підготовки запиту: " . $conn->error);
}

$stmt->bind_param("ssssss", $first_name, $last_name, $email, $password_hash, $phone, $address);

if ($stmt->execute()) {
    echo '<script>
            alert("Registration successful!");
            window.history.back();
        </script>';
} else {
    echo '<script>
            alert("Error during registration: ' . $stmt->error . '");
            window.history.back();
        </script>';
}

$stmt->close();
$conn->close();
?>
