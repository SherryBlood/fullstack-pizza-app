<?php

$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$email = $_POST['client-email'];
$password = $_POST['client-password'];


$sql = "SELECT * FROM customers WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


if (!$user) {
    echo '<script>
        alert("Invalid email or password.");
        window.history.back();
    </script>';
    exit;
}


if (password_verify($password, $user['password_hash'])) {
    session_start();
    $_SESSION['customer_id'] = $user['customer_id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['customer_logged_in'] = true;


    if ($stmt->execute()) {
    echo '<script>alert("Login successful!"); window.location.href = "main_client_page.php";</script>';
    }
    exit;
} else {
    echo '<script>
        alert("Invalid email or password.");
        window.history.back();
    </script>';
    exit;
}

$stmt->close();
$conn->close();
?>