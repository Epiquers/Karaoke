<?php
session_start();
include("../includes/conexion.php");

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado

// Elimina todas las canciones de la cola del usuario y redirige al reproductor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['limpiar_cola'])) {
    // Usamos el idUsuario que recogimos del formulario oculto, aunque también podríamos usar el de la sesión
    $idUsuario = $_POST['idUsuario'];
    $consulta_limpiar = "DELETE FROM cola WHERE id_usuario = '$idUsuario'";

    if (mysqli_query($conn, $consulta_limpiar)) {
        // Redirigimos para que se refresque la lista visualmente
        header('Location: canciones.php');
        exit();
    }
}
?>