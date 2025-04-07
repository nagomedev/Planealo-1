<?php
session_start();
$_SESSION = []; // Limpia variables de sesión
session_destroy(); // Destruimos la sesión abierta
setcookie(session_name(), '', time() - 3600, '/'); // Borra la cookie de sesión

header('Location: login.php'); // redirreción al inicio de sesión
exit();
?>