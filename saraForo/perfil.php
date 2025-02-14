<?php
session_start();
include 'config.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar si las contraseñas coinciden
    if ($new_password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        echo "La nueva contraseña debe contener al menos una letra mayúscula.";
    } elseif (!preg_match('/[\W_]/', $new_password)) {
        echo "La nueva contraseña debe contener al menos un carácter especial.";
    } else {
        // Verificar la contraseña anterior
        $sql = "SELECT * FROM usuarios WHERE id = " . $_SESSION['user_id'];
        $result = $conn->query($sql);
        $user = $result->fetch_assoc();
        
        if (password_verify($old_password, $user['password'])) {
            // Encriptar la nueva contraseña
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $update_sql = "UPDATE usuarios SET password = '$new_password_hash' WHERE id = " . $_SESSION['user_id'];
            if ($conn->query($update_sql) === TRUE) {
                echo "Contraseña actualizada con éxito.";
            } else {
                echo "Error al actualizar la contraseña.";
            }
        } else {
            echo "La contraseña anterior es incorrecta.";
        }
    }
}
$user_id = $_SESSION['user_id'];

// Obtener datos del usuario
$sql_usuario = "SELECT username, email, fecha_registro FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $user_id);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

// Obtener temas del usuario
$sql_temas = "SELECT id, titulo, fecha_creacion FROM temas WHERE autor_id = ? ORDER BY fecha_creacion DESC";
$stmt_temas = $conn->prepare($sql_temas);
$stmt_temas->bind_param("i", $user_id);
$stmt_temas->execute();
$result_temas = $stmt_temas->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['username']); ?></title>
    <link rel="stylesheet" href="perfil.css">
</head>
<body>

<header>
    <h1>Perfil de <?php echo htmlspecialchars($usuario['username']); ?></h1>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="insertartema.php">Nuevo Tema</a></li>
            <li><a href="cierre.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>

<section>
    <h2>Información del Usuario</h2>
    <p><strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
    <p><strong>Fecha de Registro:</strong> <?php echo htmlspecialchars($usuario['fecha_registro']); ?></p>
</section>

<section>
    <h2>Mis Temas</h2>
    <?php if ($result_temas->num_rows > 0): ?>
        <ul>
            <?php while ($tema = $result_temas->fetch_assoc()): ?>
                <li>
                    <a href="temas.php?id=<?php echo $tema['id']; ?>">
                        <?php echo htmlspecialchars($tema['titulo']); ?>
                    </a> - <?php echo $tema['fecha_creacion']; ?>
                    <a href="editartema.php?id=<?php echo $tema['id']; ?>">✏️ Editar</a>
                    <a href="eliminartema.php?id=<?php echo $tema['id']; ?>" onclick="return confirm('¿Seguro que quieres eliminar este tema?');">🗑️ Eliminar</a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No has publicado ningún tema aún.</p>
    <?php endif; ?>
</section>

</body>
</html>
