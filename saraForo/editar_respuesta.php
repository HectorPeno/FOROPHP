<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $respuesta_id = $_GET['id'];

    // Obtener la respuesta y el tema al que pertenece
    $sql = "SELECT contenido, tema_id FROM respuestas WHERE id = ? AND autor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $respuesta_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("Error: Respuesta no encontrada o no tienes permisos.");
    }

    $respuesta = $result->fetch_assoc();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respuesta_id']) && isset($_POST['contenido'])) {
    $respuesta_id = $_POST['respuesta_id'];
    $contenido = trim($_POST['contenido']);

    if (!empty($contenido)) {
        // Obtener el ID del tema antes de actualizar
        $sql = "SELECT tema_id FROM respuestas WHERE id = ? AND autor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $respuesta_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $tema = $result->fetch_assoc();
        $tema_id = $tema['tema_id'];

        // Actualizar respuesta
        $sql = "UPDATE respuestas SET contenido = ? WHERE id = ? AND autor_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $contenido, $respuesta_id, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            header("Location: tema.php?id=" . $tema_id . "&edit_success=1");
            exit();
        } else {
            echo "Error al actualizar la respuesta.";
        }
    } else {
        echo "El contenido no puede estar vacío.";
    }
}
?>

<form action="editar_respuesta.php" method="POST">
    <input type="hidden" name="respuesta_id" value="<?php echo htmlspecialchars($respuesta_id); ?>">
    <input type="hidden" name="tema_id" value="<?php echo htmlspecialchars($respuesta['tema_id']); ?>">
    <textarea name="contenido" rows="4" required><?php echo htmlspecialchars($respuesta['contenido']); ?></textarea><br>
    <button type="submit">Guardar cambios</button>
</form>
