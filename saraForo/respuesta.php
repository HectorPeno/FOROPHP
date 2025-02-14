<?php
session_start();
include("config.php");

// Verificar si el tema existe
if (!isset($_GET['id'])) {
    die("Error: Tema no encontrado.");
}

$tema_id = $_GET['id'];

// Obtener el tema y sus respuestas
$stmt = $conn->prepare("SELECT t.titulo, r.contenido, r.fecha, u.username 
                        FROM respuestas r 
                        JOIN temas t ON r.tema_id = t.id 
                        JOIN usuarios u ON r.autor_id = u.id 
                        WHERE r.tema_id = ? 
                        ORDER BY r.fecha ASC");
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$respuestas = $stmt->get_result();

if ($respuestas->num_rows > 0) {
    while ($respuesta = $respuestas->fetch_assoc()) {
        echo "<h2>" . htmlspecialchars($respuesta['titulo']) . "</h2>";
        echo "<div><strong>" . htmlspecialchars($respuesta['username']) . ":</strong> " . nl2br(htmlspecialchars($respuesta['contenido'])) . " <br><small>" . $respuesta['fecha'] . "</small></div><hr>";
    }
} else {
    echo "<p>No hay respuestas aún.</p>";
}
?>
<a href="tema.php?id=<?php echo $tema_id; ?>">Volver al tema</a>
