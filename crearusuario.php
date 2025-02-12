<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    $logFile = "logs/sesiones.log"; // Archivo de registro

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "❌ El nombre de usuario ya está en uso.";
    } else {
        // Registrar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            // 📌 Guardar en logs
            $mensaje = "[" . date("Y-m-d H:i:s") . "] Nuevo usuario registrado: $username.\n";
            file_put_contents($logFile, $mensaje, FILE_APPEND);

            echo "✅ Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
        } else {
            echo "❌ Error al registrar usuario.";
        }
    }
    $stmt->close();
}
$conn->close();
?>



<link rel="stylesheet" href="registro.css">
<h1>Crear cuenta</h1>
<form method="POST" action="crearusuario.php">
    <label for="username">Nombre de usuario:</label>
    <input type="text" name="username" id="username" required>
    
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>
    
    <label for="email">Correo electrónico:</label>
    <input type="email" name="email" id="email" required>
    
    <button type="submit">Registrar</button>
</form>
