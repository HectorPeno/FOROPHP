<?php
$servername = "localhost";
$username = "root"; // Cambia si usas otro usuario
$password = "";     // Cambia si usas otra contraseña
$dbname = "foro";   // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
