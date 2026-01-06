<?php
session_start();


$conn = new mysqli("localhost", "root", "1234", "pizza_db");
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}


$id = $_POST['admin-id'];
$tel = $_POST['admin-tel'];


if ($id != 1) {
    echo '<script>
        alert("Invalid data!");
        window.history.back();
    </script>';
    exit;
}


$sql = "SELECT * FROM customers WHERE customer_id = 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();


    if ($user['phone_number'] === $tel) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['customer_id'];
        $_SESSION['admin_email'] = $user['email'];

        echo '<script>
            alert("Logged in.");
            window.location.href = "orders_admin_page.php";
        </script>';
        exit;
    }
}


echo '<script>
    alert("Invalid data!");
    window.history.back();
</script>';
exit;

$conn->close();
?>