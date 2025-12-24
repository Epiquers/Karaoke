<?php
session_start();
include("../includes/conexion.php");

// Comprobamos que nos llegan datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCola'])) {

    $idCola = $_POST['idCola'];
    $idUsuario = $_SESSION['idUsuario'];

    $consulta_quitar = "DELETE FROM cola WHERE id='$idCola'";
    $result = mysqli_query($conn, $consulta_quitar);

    // Cerramos conexion
    mysqli_close($conn);
}

// Devolvemos al usuario a canciones
header('Location: canciones.php');
exit();