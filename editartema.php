<?php
session_start();
include 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha pasado un ID de tema válido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID de tema no proporcionado.");
}

$tema_id = intval($_GET['id']); // Convertir a entero para mayor seguridad

// Obtener el tema de la base de datos
$sql = "SELECT * FROM temas WHERE id = ? AND autor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tema_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el tema existe y pertenece al usuario
if ($result->num_rows === 0) {
    die("Error: No tienes permiso para editar este tema o el tema no existe.");
}

$tema = $result->fetch_assoc();

// Si se envió el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);

    // Validar que no estén vacíos
    if (empty($titulo) || empty($contenido)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Actualizar el tema en la base de datos
        $sql = "UPDATE temas SET titulo = ?, contenido = ? WHERE id = ? AND autor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $titulo, $contenido, $tema_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header("Location: index.php?mensaje=Tema actualizado correctamente");
            exit();
        } else {
            $error = "Error al actualizar el tema.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tema</title>
    <link rel="stylesheet" href="editartema.css">

</head>
<body>

<header>
    <h1>Editar Tema</h1>
    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="perfil.php">Mi perfil</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</header>

<section>
    <h2>Editar Tema</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?php echo htmlspecialchars($tema['titulo']); ?>" required>

        <label for="contenido">Contenido:</label>
        <textarea name="contenido" id="contenido" required><?php echo htmlspecialchars($tema['contenido']); ?></textarea>

        <button type="submit">Actualizar tema</button>
    </form>

    <p><a href="index.php">Volver al inicio</a></p>
</section>

</body>
</html>
