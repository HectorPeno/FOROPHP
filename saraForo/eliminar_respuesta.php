<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $respuesta_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Obtener el ID del tema antes de eliminar la respuesta
    $sql = "SELECT tema_id FROM respuestas WHERE id = ? AND autor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $respuesta_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        die("Error: No se encontró la respuesta o no tienes permisos.");
    }

    $tema = $result->fetch_assoc();
    $tema_id = $tema['tema_id'];

    // Eliminar respuesta
    $sql = "DELETE FROM respuestas WHERE id = ? AND autor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $respuesta_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: tema.php?id=" . $tema_id . "&delete_success=1");
        exit();
    } else {
        echo "Error al eliminar la respuesta.";
    }
} else {
    echo "Acción no permitida.";
}
?>
