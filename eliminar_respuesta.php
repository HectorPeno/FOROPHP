<?php
session_start();
include 'config.php';

if (isset($_GET['respuesta_id'])) {
    $respuesta_id = $_GET['respuesta_id'];

    $sql = "DELETE FROM respuestas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $respuesta_id);
    if ($stmt->execute()) {
        header("Location: temas.php?tema_id=" . $_GET['tema_id']);
    } else {
        echo "Error al eliminar la respuesta.";
    }
}
?>
