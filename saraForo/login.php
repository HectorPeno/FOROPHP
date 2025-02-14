<?php
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $logFile = "logs/sesiones.log"; // Archivo de registro

    $stmt = $conn->prepare("SELECT id, password FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            // Inicio de sesión exitoso
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $username;

            // 📌 Guardar en logs
            $mensaje = "[" . date("Y-m-d H:i:s") . "] Usuario: $username ha iniciado sesión.\n";
            file_put_contents($logFile, $mensaje, FILE_APPEND);

            header("Location: perfil.php");
            exit();
        } else {
            echo "❌ Contraseña incorrecta.";
        }
    } else {
        echo "❌ Usuario no encontrado.";
    }
    $stmt->close();
}
$conn->close();
?>




<link rel="stylesheet" href="registro.css">
<h2>Iniciar sesión</h2>
<form method="POST" action="login.php">
    <label for="username">Nombre de usuario:</label>
    <input type="text" name="username" id="username" required>
    
    <label for="password">Contraseña:</label>
    <input type="password" name="password" id="password" required>
    
    <button type="submit">Iniciar sesión</button>
</form>
<br>
<p>¿No tienes una cuenta? <a href="crearusuario.php">Crear cuenta</a></p>
