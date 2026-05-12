<?php
// Detectamos si el servidor es local o remoto
$is_localhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

if ($is_localhost) {
    // --- CONFIGURACIÓN PARA EL PC (XAMPP) ---
    $servidor = "localhost";
    $user = "root";
    $clave = "";
    $basededatos = "adrianvi_kantabile";
} else {
    // --- CONFIGURACIÓN PARA EL SERVIDOR (cPanel) ---
    $servidor = "localhost";           // En cPanel se mantiene localhost
    $user = "adrianvi_kantabile"; // El usuario que creaste en cPanel
    $clave = "Kantabile--2026--";     // La contraseña que anotaste
    $basededatos = "adrianvi_kantabile"; // El nombre de la BD en cPanel
}

// Intentamos la conexión
$conn = mysqli_connect($servidor, $user, $clave, $basededatos);

// Verificamos si hubo error de conexión
if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Forzamos el set de caracteres para evitar problemas con tildes y eñes
mysqli_set_charset($conn, "utf8mb4");

// Opcional: Para debug en desarrollo (puedes comentarlo luego)
// if ($is_localhost) { echo "Conectado en LOCAL"; }
?>