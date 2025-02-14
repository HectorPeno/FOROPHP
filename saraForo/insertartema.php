<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo']) && isset($_POST['contenido'])) {
    $titulo = trim($_POST['titulo']);
    $contenido = trim($_POST['contenido']);
    $autor_id = $_SESSION['user_id'];
    $archivoRuta = null;

    // Manejo de subida de archivos
    if (!empty($_FILES['archivo']['name'])) {
        $directorio = "uploads/";
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        $archivoNombre = basename($_FILES['archivo']['name']);
        $archivoRuta = $directorio . time() . "_" . $archivoNombre;
        $archivoTipo = strtolower(pathinfo($archivoRuta, PATHINFO_EXTENSION));

        // Validar tipos de archivo permitidos
        $formatosPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'ogg'];
        if (!in_array($archivoTipo, $formatosPermitidos)) {
            die("Error: Formato de archivo no permitido.");
        }

        // Mover el archivo al servidor
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $archivoRuta)) {
            die("Error al subir el archivo.");
        }
    }

    // Insertar el tema en la base de datos
    $sql = "INSERT INTO temas (titulo, contenido, autor_id, archivo, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $titulo, $contenido, $autor_id, $archivoRuta);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Error al crear el tema.";
    }

    $stmt->close();
}
?>
<link rel="stylesheet" href="insertar.css">
<form action="insertartema.php" method="POST" enctype="multipart/form-data">
<h2>Crear Nuevo Tema</h2>
    <label for="titulo">Título:</label>
    <input type="text" name="titulo" required><br>

    <label for="contenido">Contenido:</label>
    <textarea name="contenido" rows="4" required></textarea><br>

    <label for="archivo">Adjuntar imagen o video:</label>
    <input type="file" name="archivo" accept="image/*,video/*"><br>

    <button type="submit">Crear Tema</button>
    <a href="index.php" class="back-button">⬅ Volver</a>
</form>
