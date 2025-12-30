<?php
session_start();
include '../includes/conexion.php';

$idUsuario = $_SESSION['idUsuario'];

// 1️⃣ PRIMERA canción de la cola (con ID fila)
$consulta_primeraCancion = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
$result_primera = mysqli_query($conn, $consulta_primeraCancion);
$primera = mysqli_fetch_assoc($result_primera);

// Detalles de la primera canción de la cola
$cancion_actual = null;
// Identificador dentro de la tabla 'cola' de la primera canción de la lista
$id_colaPrimera = null;
// ID de la primera canción de la cola
$idCancionActual = null;

if ($primera) {
    $id_colaPrimera = $primera['id'];
    $idCancionActual = $primera['id_cancion'];
    $consulta_detalleCancion = "SELECT * FROM canciones WHERE id = '$idCancionActual'";
    $result_cancion = mysqli_query($conn, $consulta_detalleCancion);
    $cancion_actual = mysqli_fetch_assoc($result_cancion);
}

// 2️⃣ BORRAR si pulsa siguiente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siguiente'])) {
    $consulta_borrarPrimera = "DELETE FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
    mysqli_query($conn, $consulta_borrarPrimera);
    header('Location: karaoke.php');
    exit();
}

// 3️⃣ Resultado cola completa (con ID fila)
$consulta_cola = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC";
$result_cola = mysqli_query($conn, $consulta_cola);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karaoke - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-12 col-lg-8 mb-4">
                <?php
                if ($cancion_actual) {
                    echo "<h1 class='display-5 fw-bold text-center mb-4 text-warning'>" . $cancion_actual['titulo'] . " - " . $cancion_actual['artista'] . "</h1>
                            <div class='position-relative mb-4'>
                                <video id='videoKaraoke' class='w-100 rounded shadow' controls preload='metadata'>
                                    <source src='" . $cancion_actual['archivo'] . "' type='video/mp4'>
                                    Tu navegador no soporta vídeo.
                                </video>
                            </div>
                            <form action='' method='POST' class='mt-3 text-center'>
                                <button name='siguiente' class='btn btn-main btn-lg'>⏭️ Siguiente canción</button>
                            </form>";
                } else {
                    echo "<div class='text-center py-5'>
                            <h3 class='text-muted'>🎤 No hay canciones en cola</h3>
                            <a href='canciones.php' class='btn btn-main btn-lg mt-3'>Añadir canciones</a>
                        </div>";
                }
                ?>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card card-dark sticky-top" style="top:20px;">
                    <div class="card-body p-4">
                        <h5 class="card-title text-center mb-4">Cola de reproducción</h5>
                        <?php
                        if (mysqli_num_rows($result_cola) > 0) {
                            while ($row = mysqli_fetch_assoc($result_cola)) {
                                $idCola = $row['id'];
                                $idCancion = $row['id_cancion'];
                                $consulta_cancion = "SELECT * FROM canciones WHERE id = '$idCancion'";
                                $result_detallesCancion = mysqli_query($conn, $consulta_cancion);
                                $cancion = mysqli_fetch_assoc($result_detallesCancion);

                                if ($idCola == $id_colaPrimera) {
                                    echo "<div class='p-3 border-bottom bg-danger bg-opacity-10 text-danger fw-bold border-start border-danger border-3'>▶️ SONANDO: " . $cancion['titulo'] . " - " . $cancion['artista'] . "</div>";
                                } else {
                                    echo "<div class='p-3 border-bottom text-warning fw-bold'>⏳ " . $cancion['titulo'] . " - " . $cancion['artista'] . "</div>";
                                }
                            }
                        } else {
                            echo "<p class='text-center text-muted py-4'>Cola vacía</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnFullscreen').addEventListener('click', function() {
            const video = document.getElementById('videoKaraoke');
            if (video.requestFullscreen) video.requestFullscreen();
            else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
            else if (video.mozRequestFullScreen) video.mozRequestFullScreen();
            else if (video.msRequestFullscreen) video.msRequestFullscreen();
        });
    </script>

</body>

</html>