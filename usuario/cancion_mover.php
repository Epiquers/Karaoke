<?php
session_start();
include("../includes/conexion.php");

// ACCIÓN: MOVER CANCIÓN (SUBIR O BAJAR)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mover'])) {
    $idUsuario = $_SESSION['idUsuario'];
    $idActual = $_POST['idCola'];

    // Recogemos el valor del botón "mover"
    $direccion = $_POST['mover'];

    $operador = ($direccion == 'subir') ? '<' : '>';
    $orden = ($direccion == 'subir') ? 'DESC' : 'ASC';

    $sql_vecino = "SELECT id FROM cola WHERE id_usuario = '$idUsuario' AND id $operador '$idActual' ORDER BY id $orden LIMIT 1";
    $res_vecino = mysqli_query($conn, $sql_vecino);

    if ($vecino = mysqli_fetch_assoc($res_vecino)) {
        $idVecino = $vecino['id'];

        mysqli_query($conn, "UPDATE cola SET id = 0 WHERE id = '$idActual'");
        mysqli_query($conn, "UPDATE cola SET id = '$idActual' WHERE id = '$idVecino'");
        mysqli_query($conn, "UPDATE cola SET id = '$idVecino' WHERE id = 0");
    }
    header('Location: canciones.php');
    exit();
}
