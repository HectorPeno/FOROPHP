<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tema_id']) && isset($_POST['contenido'])) {
    $tema_id = $_POST['tema_id'];
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

    // Insertar la respuesta en la base de datos con el archivo adjunto
    $sql = "INSERT INTO respuestas (tema_id, autor_id, contenido, archivo, fecha) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $tema_id, $autor_id, $contenido, $archivoRuta);
    
    if ($stmt->execute()) {
        header("Location: tema.php?id=" . $tema_id);
        exit();
    } else {
        echo "Error al insertar la respuesta.";
    }

    $stmt->close();
}
?>
