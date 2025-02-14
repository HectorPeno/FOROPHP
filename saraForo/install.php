<?php
include 'config.php';

// Crear la base de datos
$sql = "CREATE DATABASE IF NOT EXISTS foro";
if ($conn->query($sql) === TRUE) {
    echo "Base de datos creada o ya existente.";
} else {
    echo "Error al crear la base de datos: " . $conn->error;
}

$conn->select_db($dbname);

// Crear tabla de usuarios
$sql = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
$conn->query($sql);

// Crear tabla de temas
$sql = "CREATE TABLE IF NOT EXISTS temas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido TEXT NOT NULL,
    autor_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);";
$conn->query($sql);

// Crear tabla de respuestas
$sql = "CREATE TABLE IF NOT EXISTS respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tema_id INT NOT NULL,
    autor_id INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tema_id) REFERENCES temas(id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);";
$conn->query($sql);

echo "Tablas creadas correctamente.";
?>
