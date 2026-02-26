<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo'])) {

    $titulo  = $_POST['titulo'];
    $artista = $_POST['artista'];

    // Datos del archivo
    $nombre_original = $_FILES['archivo']['name'];
    $ruta_temporal = $_FILES['archivo']['tmp_name'];

    // Ruta final en el servidor
    $ruta_final = '../videos/' . $nombre_original;

    // Mover archivo subido a la carpeta videos (copy da error, por eso usamos move_uploaded_file que es más seguro)
    if (move_uploaded_file($ruta_temporal, $ruta_final)) {

        $sql = "INSERT INTO canciones (titulo, artista, archivo)
                VALUES ('$titulo', '$artista', '$ruta_final')";

        mysqli_query($conn, $sql);
    }
    mysqli_close($conn);
}

// Volver a la página del admin
header('Location: canciones_admin.php');
exit();
