<?php
session_start();
include 'config.php';

// Contador de visitas
$archivo_visitas = 'visitas.txt';

// Verificar si el archivo existe, si no, crearlo con 0 visitas
if (!file_exists($archivo_visitas)) {
    file_put_contents($archivo_visitas, '0');
}

// Leer el número de visitas actual
$visitas = (int) file_get_contents($archivo_visitas);

// Incrementar el contador
$visitas++;

// Guardar el nuevo valor en el archivo
file_put_contents($archivo_visitas, $visitas);

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener los temas existentes
$sql = "SELECT * FROM temas ORDER BY fecha_creacion DESC";
$result = $conn->query($sql);

// Verificar si el tema debe ser eliminado
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql = "DELETE FROM temas WHERE id = ? AND autor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $delete_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        echo "Tema eliminado exitosamente.";
        header("Location: index.php"); // Redirigir después de eliminar
        exit();
    } else {
        echo "Error al eliminar el tema.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro - Página Principal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>Bienvenido, <?php echo $_SESSION['username']; ?></h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="perfil.php">Mi perfil</a></li>
                <li><a href="cierre.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2><a href="insertartema.php">Añadir nuevo tema</a></h2>
    </section>

    <section>
        <h2>Temas del foro</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='tema'>";
                echo "<h3><a href='tema.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['titulo']) . "</a></h3>";
                echo "<p>" . nl2br(htmlspecialchars($row['contenido'])) . "</p>";
                echo "<p><small>Publicado por: " . $row['autor_id'] . " | Fecha: " . $row['fecha_creacion'] . "</small></p>";

                // Mostrar imagen o video si hay un archivo adjunto
                if (!empty($row['archivo'])) {
                    $archivoRuta = htmlspecialchars($row['archivo']);
                    $extension = strtolower(pathinfo($archivoRuta, PATHINFO_EXTENSION));

                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo "<br><img src='$archivoRuta' width='300' alt='Imagen subida'>";
                    } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                        echo "<br><video width='300' controls><source src='$archivoRuta' type='video/$extension'>Tu navegador no soporta videos.</video>";
                    }
                }

                // Opciones de edición y eliminación solo si el usuario es el autor del tema
                if ($row['autor_id'] == $_SESSION['user_id']) {
                    echo "<a href='editartema.php?id=" . $row['id'] . "'>Editar</a> | ";
                    echo "<a href='index.php?delete=" . $row['id'] . "' onclick='return confirm(\"¿Estás seguro de que quieres eliminar este tema?\")'>Eliminar</a>";
                }

                echo "</div>";
            }
        } else {
            echo "<p>No hay temas disponibles.</p>";
        }
        ?>
    </section>

</body>
</html>
