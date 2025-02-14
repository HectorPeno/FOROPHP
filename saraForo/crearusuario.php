<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verificar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password)) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Comprobar si el email ya está registrado
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Error: Este correo electrónico ya está registrado.");
    }
    $stmt->close();

    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el usuario en la base de datos
    $sql = "INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        echo "Registro exitoso. <a href='login.php'>Inicia sesión aquí</a>";
    } else {
        echo "Error al registrar el usuario.";
    }

    $stmt->close();
    $conn->close();
}
?>
<link rel="stylesheet" href="registro.css">
<h2>Registro de Usuario</h2>
<form action="crearusuario.php" method="POST">
    Nombre de usuario: <input type="text" name="username" required><br>
    Correo electrónico: <input type="email" name="email" required><br>
    Contraseña: <input type="password" name="password" required><br>
    <button type="submit">Registrarse</button>
</form>
