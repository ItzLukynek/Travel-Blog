<?php
session_start();
$_SESSION = array();
session_destroy();

foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/');
    unset($_COOKIE[$key]);
}

echo "Data vymazána";
?>