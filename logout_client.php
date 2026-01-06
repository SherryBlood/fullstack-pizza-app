<?php
session_start();
session_unset();
session_destroy();
header("Location: main_client_page.php");
exit;
?>
