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
$stmt = $conn->prepare("SELECT t.titulo, t.contenido, u.username FROM temas t JOIN usuarios u ON t.autor_id = u.id WHERE t.id = ?");
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

<hr>

<h2>Respuestas</h2>

<?php
// Obtener respuestas del tema
$stmt = $conn->prepare("SELECT r.contenido, r.fecha, u.username FROM respuestas r JOIN usuarios u ON r.autor_id = u.id WHERE r.tema_id = ? ORDER BY r.fecha ASC");
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$respuestas = $stmt->get_result();

if ($respuestas->num_rows > 0) {
    while ($respuesta = $respuestas->fetch_assoc()) {
        echo "<div><strong>" . htmlspecialchars($respuesta['username']) . ":</strong> " . nl2br(htmlspecialchars($respuesta['contenido'])) . " <br><small>" . $respuesta['fecha'] . "</small></div><hr>";
    }
} else {
    echo "<p>No hay respuestas aún. ¡Sé el primero en responder!</p>";
}
?>
<link rel="stylesheet" href="tema.css">
<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0): ?>
    <h3>Responder al tema</h3>
    <form action="procesar_respuesta.php" method="POST">
        <input type="hidden" name="tema_id" value="<?php echo htmlspecialchars($tema_id); ?>">
        <textarea name="contenido" rows="4" required></textarea><br>
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
