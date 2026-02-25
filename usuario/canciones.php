<?php
session_start();
include '../includes/conexion.php';

// --- LOGICA DE SESIÓN Y REPRODUCTOR (UNIFICADA) ---
$idUsuario = $_SESSION['idUsuario'];

// 1️⃣ PRIMERA canción de la cola (con ID fila)
$consulta_primeraCancion = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
$result_primera = mysqli_query($conn, $consulta_primeraCancion);
$primera = mysqli_fetch_assoc($result_primera);

$cancion_actual = null;
$id_colaPrimera = null;
$idCancionActual = null;

if ($primera) {
    $id_colaPrimera = $primera['id'];
    $idCancionActual = $primera['id_cancion'];
    $consulta_detalleCancion = "SELECT * FROM canciones WHERE id = '$idCancionActual'";
    $result_cancion = mysqli_query($conn, $consulta_detalleCancion);
    $cancion_actual = mysqli_fetch_assoc($result_cancion);
}

// 2️⃣ ACCIÓN: BORRAR si pulsa siguiente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siguiente'])) {
    $consulta_borrarPrimera = "DELETE FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
    mysqli_query($conn, $consulta_borrarPrimera);
    header('Location: ' . $_SERVER['PHP_SELF']); // Redirige a la misma página
    exit();
}

// 3️⃣ CONSULTAS PARA LISTADOS
$consulta_cola = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC";
$result_cola = mysqli_query($conn, $consulta_cola);

$consulta_canciones = "SELECT * FROM canciones";
$resultado_canciones = mysqli_query($conn, $consulta_canciones);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantabile - Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Estilos necesarios para la estructura de paneles */
        .app-main {
            display: flex;
            height: calc(100vh - 56px); /* Resta la altura de la navbar */
            overflow: hidden;
        }
        .sidebar-pills {
            width: 400px;
            background-color: #151515;
            border-right: 1px solid #333;
            display: flex;
            flex-direction: column;
        }
        .player-content {
            flex: 1;
            overflow-y: auto;
            background-color: #000;
        }
        .nav-pills .nav-link {
            color: #fff;
            border-radius: 0;
            padding: 15px;
        }
        .nav-pills .nav-link.active {
            background-color: #dc3545 !important;
        }
        .tab-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        @media (max-width: 992px) {
            .app-main { flex-direction: column; height: auto; overflow: visible; }
            .sidebar-pills { width: 100%; height: 500px; border-right: none; border-bottom: 1px solid #333; }
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>

    <div class="app-main">
        
        <div class="sidebar-pills">
            <ul class="nav nav-pills nav-fill border-bottom border-secondary" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-biblioteca" type="button">
                        <i class="bi bi-music-note-list me-2"></i>BIBLIOTECA
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-cola" type="button">
                        <i class="bi bi-list-ol me-2"></i>COLA
                    </button>
                </li>
            </ul>

            <div class="tab-content tab-scroll">
                
                <div class="tab-pane fade show active" id="tab-biblioteca">
                    <h6 class="text-secondary mb-3 small">DISPONIBLES</h6>
                    <?php
                    if (mysqli_num_rows($resultado_canciones) > 0) {
                        while ($row = mysqli_fetch_assoc($resultado_canciones)) {
                            ?>
                            <div class="d-flex justify-content-between align-items-center p-3 card-dark mb-2">
                                <div>
                                    <h6 class="mb-1 fw-bold text-warning small"><?php echo $row['titulo']; ?></h6>
                                    <p class="mb-0 text-secondary smaller"><?php echo $row['artista']; ?></p>
                                </div>
                                <form action="cancion_añadir.php" method="POST" class="m-0">
                                    <input type="hidden" name="idCancion" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-main btn-sm">Añadir</button>
                                </form>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <div class="tab-pane fade" id="tab-cola">
                    <h6 class="text-secondary mb-3 small">SIGUIENTES TEMAS</h6>
                    <?php
                    if (mysqli_num_rows($result_cola) > 0) {
                        // Reset cursor
                        mysqli_data_seek($result_cola, 0);
                        while ($row = mysqli_fetch_assoc($result_cola)) {
                            $idCola = $row['id'];
                            $idC = $row['id_cancion'];
                            $res_c = mysqli_query($conn, "SELECT * FROM canciones WHERE id = '$idC'");
                            $c = mysqli_fetch_assoc($res_c);
                            
                            $esSonando = ($idCola == $id_colaPrimera);
                            ?>
                            <div class="d-flex justify-content-between align-items-center p-3 mb-2 <?= $esSonando ? 'border-start border-danger border-3 bg-danger bg-opacity-10' : 'card-dark' ?>">
                                <div>
                                    <?php if($esSonando): ?>
                                        <span class="smaller text-danger fw-bold d-block" style="font-size: 0.7rem;">REPRODUCIENDO</span>
                                    <?php endif; ?>
                                    <h6 class="mb-1 fw-bold <?= $esSonando ? 'text-white' : 'text-warning' ?> small"><?php echo $c['titulo']; ?></h6>
                                    <p class="mb-0 text-secondary smaller"><?php echo $c['artista']; ?></p>
                                </div>
                                <form action="cancion_quitar.php" method="POST" class="m-0">
                                    <input type="hidden" name="idCola" value="<?php echo $idCola; ?>">
                                    <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center text-muted py-4 small'>Cola vacía</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="player-content p-4 d-flex align-items-center">
            <div class="container-fluid">
                <?php if ($cancion_actual): ?>
                    <div class="row justify-content-center">
                        <div class="col-12 col-xl-10 text-center">
                            <h2 class="display-6 fw-bold text-warning mb-1"><?= $cancion_actual['titulo'] ?></h2>
                            <p class="lead text-secondary mb-4"><?= $cancion_actual['artista'] ?></p>
                            
                            <div class="position-relative bg-dark rounded shadow-lg overflow-hidden border border-secondary mb-4">
                                <video id="videoKaraoke" class="w-100" controls preload="metadata">
                                    <source src="<?= $cancion_actual['archivo'] ?>" type="video/mp4">
                                    Tu navegador no soporta vídeo.
                                </video>
                            </div>

                            <div class="d-flex justify-content-center gap-3">
                                <form action="" method="POST">
                                    <button name="siguiente" class="btn btn-main btn-lg px-5">
                                        <i class="bi bi-skip-forward-fill me-2"></i>Siguiente canción
                                    </button>
                                </form>
                                <button id="btnFullscreen" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-fullscreen"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-mic-fill display-1 text-secondary opacity-25"></i>
                        <h3 class="text-muted mt-4">🎤 No hay canciones en cola</h3>
                        <p class="text-secondary small">Selecciona una canción en la biblioteca para empezar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para Pantalla Completa
        const btnFs = document.getElementById('btnFullscreen');
        if(btnFs) {
            btnFs.addEventListener('click', function() {
                const video = document.getElementById('videoKaraoke');
                if (video) {
                    if (video.requestFullscreen) video.requestFullscreen();
                    else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
                    else if (video.msRequestFullscreen) video.msRequestFullscreen();
                }
            });
        }
    </script>
</body>
</html>