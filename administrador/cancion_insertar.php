<?php
/* session_start();
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
exit(); */

session_start();
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_cancion'])) {

    $titulo  = $_POST['titulo'];
    $artista = $_POST['artista'];
    $estilo  =  $_POST['estilo'];

    //"Limpiamos" el nombre para el archivo (Quitamos espacios por guiones)
    // Ejemplo: "Mi Canción Favorita.mp4" => "Mi_Canción_Favorita.mp4"
    $nombre_limpio = str_replace(' ', '_', $titulo);

    //Obtenemos la extensión original de los archivos (mp4, mp3, lrc, etc.)
    $ext_cancion = pathinfo($_FILES['archivo_cancion']['name'], PATHINFO_EXTENSION);

    //Creamos el nombre final (Título_Tiempo.extension)
    // Añadimos time() al final por si subes dos veces la misma canción, que no se borren
    $nombre_final_cancion = $nombre_limpio . "_" . date('s') . "." . $ext_cancion;
    $nombre_final_letra = $nombre_limpio . "_" . date('s') . "." . ".lrc";

    // Rutas para GUARDAR en la BD (mejor guardarlas sin el ../ para el reproductor)
    // Guardamos ruta relativa desde la raíz para que el archivo sea accesible desde cualquier carpeta de la web (index, admin, etc.)
    $destino_cancion = "../uploads/canciones/" . $nombre_final_cancion;
    $db_cancion      = "uploads/canciones/" . $nombre_final_cancion;

    $destino_letra = "../uploads/letras/" . $nombre_final_letra;
    $db_letra      = "uploads/letras/" . $nombre_final_letra;

    // Intentamos mover ambos archivos
    if (
        move_uploaded_file($_FILES['archivo_cancion']['tmp_name'], $destino_cancion) &&
        move_uploaded_file($_FILES['archivo_letra']['tmp_name'], $destino_letra)
    ) {

        $sql = "INSERT INTO canciones (titulo, artista, estilo, cancion, letra)
                VALUES ('$titulo', '$artista', '$estilo', '$db_cancion', '$db_letra')";

        mysqli_query($conn, $sql);
    }

    mysqli_close($conn);
}

header('Location: canciones_admin.php');
exit();
