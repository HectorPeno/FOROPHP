<?php
session_start();
include("config.php");

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    die("Error: Debes iniciar sesión para responder.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tema_id = $_POST['tema_id'];
    $contenido = trim($_POST['contenido']);
    $autor_id = $_SESSION['user_id']; // ID del usuario autenticado

    if (empty($contenido)) {
        die("Error: La respuesta no puede estar vacía.");
    }

    // Insertar la respuesta en la base de datos
    $stmt = $conn->prepare("INSERT INTO respuestas (tema_id, autor_id, contenido) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $tema_id, $autor_id, $contenido);

    if ($stmt->execute()) {
        // Redirigir a tema.php en lugar de respuesta.php
        header("Location: tema.php?id=" . $tema_id);
        exit();
    }
    else {
        echo "❌ Error al publicar la respuesta: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
