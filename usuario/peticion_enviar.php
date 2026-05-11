<?php
session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Inserta la petición en la BD si el usuario está logueado y los campos llegan correctamente
    if (isset($_SESSION['idUsuario']) || isset($_POST['artista']) || isset($_POST['titulo'])) {
        $idUsuario = $_SESSION['idUsuario'];
        $artista =  $_POST['artista'];
        $titulo = $_POST['titulo'];

        $consulta_peticion = "INSERT INTO peticiones (usuario, artista, titulo) VALUES ('$idUsuario', '$artista', '$titulo')";
        mysqli_query($conn, $consulta_peticion);
        mysqli_close($conn);

        // Guardamos el mensaje en sesión para mostrarlo en la siguiente página
        $_SESSION['mensaje'] = "¡Petición enviada! Gracias por tu sugerencia.";
    }

    // Volver a la página de peticiones
    header('Location: peticiones.php');
    exit();
}
