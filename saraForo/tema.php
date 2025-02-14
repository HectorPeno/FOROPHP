<?php
session_start();
echo "<pre>";
//print_r($_SESSION);
echo "</pre>";
include("config.php");

// Verificar si el tema existe
if (!isset($_GET['id'])) {
    die("Error: Tema no encontrado.");
}

$tema_id = $_GET['id'];

// Obtener los datos del tema
$stmt = $conn->prepare("SELECT t.titulo, t.contenido, t.archivo, u.username FROM temas t JOIN usuarios u ON t.autor_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Error: Tema no encontrado.");
}

$tema = $result->fetch_assoc();
?>

<h1><?php echo htmlspecialchars($tema['titulo']); ?></h1>
<p><strong>Autor:</strong> <?php echo htmlspecialchars($tema['username']); ?></p>
<p><?php echo nl2br(htmlspecialchars($tema['contenido'])); ?></p>

<?php
// Mostrar imagen o video si hay un archivo adjunto al tema
if (!empty($tema['archivo'])) {
    $archivoRuta = htmlspecialchars($tema['archivo']);
    $extension = strtolower(pathinfo($archivoRuta, PATHINFO_EXTENSION));

    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "<br><img src='$archivoRuta' width='300' alt='Imagen subida'>";
    } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
        echo "<br><video width='300' controls><source src='$archivoRuta' type='video/$extension'>Tu navegador no soporta videos.</video>";
    }
}
?>

<hr>
<h2>Respuestas</h2>

<?php
// Obtener respuestas del tema
$stmt = $conn->prepare("SELECT r.id, r.contenido, r.fecha, r.autor_id, r.archivo, u.username FROM respuestas r JOIN usuarios u ON r.autor_id = u.id WHERE r.tema_id = ? ORDER BY r.fecha ASC");
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$respuestas = $stmt->get_result();

if ($respuestas->num_rows > 0) {
    while ($respuesta = $respuestas->fetch_assoc()) {
        echo "<div>";
        echo "<strong>" . htmlspecialchars($respuesta['username']) . ":</strong> " . nl2br(htmlspecialchars($respuesta['contenido']));
        echo " <br><small>" . $respuesta['fecha'] . "</small>";

        // Mostrar imagen o video si hay un archivo adjunto
        if (!empty($respuesta['archivo'])) {
            $archivoRuta = htmlspecialchars($respuesta['archivo']);
            $extension = strtolower(pathinfo($archivoRuta, PATHINFO_EXTENSION));
            
            echo "<br>";
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<img src='$archivoRuta' width='300' alt='Imagen subida'>";
            } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                echo "<video width='300' controls><source src='$archivoRuta' type='video/$extension'>Tu navegador no soporta videos.</video>";
            }
        }

        // Mostrar botones de editar y eliminar si el usuario es el autor de la respuesta
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $respuesta['autor_id']) {
            echo " | <a href='editar_respuesta.php?id=" . $respuesta['id'] . "'>✏ Editar</a>";
            echo " | <a href='eliminar_respuesta.php?id=" . $respuesta['id'] . "' onclick='return confirm(\"¿Estás seguro de que quieres eliminar esta respuesta?\")'>❌ Eliminar</a>";
        }

        echo "</div><hr>";
    }
} else {
    echo "<p>No hay respuestas aún. ¡Sé el primero en responder!</p>";
}
?>
<link rel="stylesheet" href="tema.css">

<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0): ?>
    <h3>Responder al tema</h3>
    <form action="procesar_respuesta.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="tema_id" value="<?php echo htmlspecialchars($tema_id); ?>">
        <textarea name="contenido" rows="4" required></textarea><br>
        <label for="archivo">Adjuntar imagen o video:</label>
        <input type="file" name="archivo" accept="image/*,video/*"><br>
        <button type="submit">Enviar respuesta</button>
        <a href="index.php" class="back-button">⬅ Volver a la página principal</a>
    </form>
<?php else: ?>
    <p><a href="login.php">Inicia sesión</a> para responder.</p>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>