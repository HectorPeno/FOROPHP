<?php
session_start();
include 'config.php';

$mensaje = ""; // Variable para mostrar mensajes dentro del formulario

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'], $_POST['contenido'])) {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $usuario_id = $_SESSION['user_id'];

    // Validaciones
    if (empty($titulo) || empty($contenido)) {
        $mensaje = "<p class='error'>❌ Por favor, completa todos los campos.</p>";
    } else {
        // Insertar el nuevo tema en la base de datos
        $sql = "INSERT INTO temas (titulo, contenido, autor_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $titulo, $contenido, $usuario_id);
        
        if ($stmt->execute()) {
            $mensaje = "<p class='success'>✅ Tema creado exitosamente. <a href='index.php'>Volver a la página principal</a></p>";
        } else {
            $mensaje = "<p class='error'>❌ Error al crear el tema: " . $stmt->error . "</p>";
        }
    }
}
?>

<link rel="stylesheet" href="insertar.css">

<form method="POST" action="insertartema.php">
    <h2>Añadir nuevo tema</h2>

    <!-- Aquí se muestra el mensaje de error o éxito dentro del formulario -->
    <?php if (!empty($mensaje)) echo $mensaje; ?>

    <label for="titulo">Título:</label>
    <input type="text" name="titulo" id="titulo" required>
    
    <label for="contenido">Contenido:</label>
    <textarea name="contenido" id="contenido" required></textarea>
    
    <button type="submit">Crear tema</button>
</form>
