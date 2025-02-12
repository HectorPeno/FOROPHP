<?php
session_start();
include 'config.php';

$logFile = "logs/sesiones.log"; // Archivo de registro

if (isset($_SESSION['username'])) {
    $usuario = $_SESSION['username'];

    // Guardar log antes de destruir la sesión
    $mensaje = "[" . date("Y-m-d H:i:s") . "] Usuario: $usuario cerró sesión.\n";
    file_put_contents($logFile, $mensaje, FILE_APPEND);
}

// Eliminar sesión
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Eliminar cookie de sesión

// Redirigir a login
header("Location: login.php");
exit();
