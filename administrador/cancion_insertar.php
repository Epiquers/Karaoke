<?php
session_start();
include '../includes/conexion.php';

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archivo_voz'])) {

    // Recogemos y limpiamos los datos del formulario para evirar inyecciones SQL y problemas con caracteres especiales como por ejemplo
    // que el artista se llame Guns N' Roses, que el apóstrofe da problemas en la consulta SQL
    $titulo  =  mysqli_real_escape_string($conn, $_POST['titulo']);
    $artista = mysqli_real_escape_string($conn, $_POST['artista']);
    $estilo  =  mysqli_real_escape_string($conn, $_POST['estilo']);

    // limpiamos los nombres (Evitamos espacios y caracteres raros en carpetas y archivos)
    // "Rosalía - Motomami" -> "Rosalia_Motomami"
    $artista_folder = str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\- ]/', '', $artista));
    $nombre_limpio  = str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\- ]/', '', $titulo));

    $dir_canciones = "../uploads/canciones/" . $artista_folder;
    $dir_letras    = "../uploads/letras/" . $artista_folder;

    //creamos las carpetas si no existen (con permisos totales para evitar problemas al subir archivos)
    // El 0755 es permisos de lectura y ejecución para el propietario y lectura para el resto, el 'true' permite crear carpetas anidadas
    if (!is_dir($dir_canciones)) {
        mkdir($dir_canciones, 0755, true);
    }
    if (!is_dir($dir_letras)) {
        mkdir($dir_letras, 0755, true);
    }

    //Obtenemos la extensión original de los archivos (mp4, mp3, lrc, etc.)
    $ext_voz = pathinfo($_FILES['archivo_voz']['name'], PATHINFO_EXTENSION);
    $ext_instrumental = pathinfo($_FILES['archivo_instrumental']['name'], PATHINFO_EXTENSION);

    //Creamos el nombre final (Título_Tiempo.extension)
    // Añadimos time() al final por si subes dos veces la misma canción, que no se borren
    $nombre_final_voz = $nombre_limpio . "_(voz)_" . date('s') . "." . $ext_voz;
    $nombre_final_instrumental = $nombre_limpio . "_(instrumental)_" . date('s')  . "." . $ext_instrumental;
    $nombre_final_letra = $nombre_limpio . "_" . date('s')  . ".lrc";

    // Rutas para GUARDAR en la BD (mejor guardarlas sin el ../ para el reproductor)
    // Guardamos ruta relativa desde la raíz para que el archivo sea accesible desde cualquier carpeta de la web (index, admin, etc.)
    $db_voz = "uploads/canciones/{$artista_folder}/". $nombre_final_voz;
    $db_instrumental = "uploads/canciones/{$artista_folder}/". $nombre_final_instrumental;
    $db_letra = "uploads/letras/{$artista_folder}/". $nombre_final_letra;

    // Intentamos mover ambos archivos
    if (
        move_uploaded_file($_FILES['archivo_voz']['tmp_name'], "../" . $db_voz) && 
        move_uploaded_file($_FILES['archivo_instrumental']['tmp_name'], "../" . $db_instrumental) &&
        move_uploaded_file($_FILES['archivo_letra']['tmp_name'], "../" . $db_letra)
    ) {

        // Cambiamos permisos a los archivos subidos para evitar problemas de acceso (lectura para todos)
        chmod("../" . $db_voz, 0644);
        chmod("../" . $db_instrumental, 0644);
        chmod("../" . $db_letra, 0644);

        $sql = "INSERT INTO canciones (titulo, artista, estilo, voz, instrumental, letra)
                VALUES ('$titulo', '$artista', '$estilo', '$db_voz', '$db_instrumental', '$db_letra')";

        mysqli_query($conn, $sql);
    }

    mysqli_close($conn);
}

header('Location: canciones_admin.php');
exit();
