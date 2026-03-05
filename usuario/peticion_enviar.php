<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_SESSION['idUsuario']) || isset($_POST['artista']) || isset($_POST['titulo'])) {
        $idUsuario = $_SESSION['idUsuario'];
        $artista =  $_POST['artista'];
        $titulo = $_POST['titulo'];

        $consulta_peticion = "INSERT INTO peticiones (usuario, artista, titulo) VALUES ('$idUsuario', '$artista', '$titulo')";
        mysqli_query($conn, $consulta_peticion);
        mysqli_close($conn);

        $_SESSION['mensaje'] = "¡Petición enviada! Gracias por tu sugerencia.";
    }

    // Volver a la página de peticiones
    header('Location: peticiones.php');
    exit();
}
