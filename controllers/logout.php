<?php
session_start();
//delete user data from session
unset($_SESSION['auth']);
unset($_SESSION['user_name']);
unset($_SESSION['user_id']);

$_SESSION['login_error'] = "Uživatel úspěšně odhlášen";
header("Location: ../index.php");

exit();


?>