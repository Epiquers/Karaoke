<?php
session_start();
include("../includes/conexion.php");

// Comprobamos que nos llegan datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCancion'])) {

    $idCancion = $_POST['idCancion'];
    $idUsuario = $_SESSION['idUsuario'];

    $consulta_añadir = "INSERT INTO cola (id_usuario, id_cancion) VALUES ('$idUsuario', '$idCancion')";
    $result = mysqli_query($conn, $consulta_añadir);

    // Cerramos conexion
    mysqli_close($conn);
}

// Devolvemos al usuario a canciones
header('Location: canciones.php');
exit();
