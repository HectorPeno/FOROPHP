<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "No has iniciado sesión. Por favor, inicia sesión para continuar.";
    exit();
}

if (!isset($_GET['id'])) {
    echo "Tema no encontrado.";
    exit();
}

$tema_id = $_GET['id'];

// Verificar si el usuario es el autor del tema
$sql = "SELECT * FROM temas WHERE id = ? AND autor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tema_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "No tienes permiso para eliminar este tema.";
    exit();
}

// Eliminar el tema
$sql = "DELETE FROM temas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tema_id);
if ($stmt->execute()) {
    echo "Tema eliminado exitosamente. <a href='index.php'>Volver al foro</a>";
} else {
    echo "Error al eliminar el tema: " . $stmt->error;
}
?>
