<?php
session_start();
include '../includes/conexion.php';

$idUsuario = $_SESSION['idUsuario'];

// 1️⃣ PRIMERA canción de la cola
$consulta_primeraCancion = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
$result_primera = mysqli_query($conn, $consulta_primeraCancion);
$primera = mysqli_fetch_assoc($result_primera);

$cancion_actual = null;
$id_colaPrimera = null;

if ($primera) {
    $id_colaPrimera = $primera['id'];
    $idCancionActual = $primera['id_cancion'];
    $result_cancion = mysqli_query($conn, "SELECT * FROM canciones WHERE id = '$idCancionActual'");
    $cancion_actual = mysqli_fetch_assoc($result_cancion);
}

// 2️⃣ ACCIÓN: BORRAR si pulsa siguiente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siguiente'])) {
    mysqli_query($conn, "DELETE FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1");
    header('Location:canciones.php ');
    exit();
}

// 3️⃣ CONSULTAS
$result_cola = mysqli_query($conn, "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC");
$resultado_canciones = mysqli_query($conn, "SELECT * FROM canciones");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantabile - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="text-light page-canciones">

    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 border-end border-secondary bg-dark px-0">
                <ul class="nav nav-pills nav-fill border-bottom border-secondary">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-biblioteca">
                            <i class="bi bi-music-note-list me-2"></i>BIBLIOTECA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-cola">
                            <i class="bi bi-list-ol me-2"></i>COLA
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-biblioteca">
                        <div class="search-container py-2 px-2 ms-3 me-3 mb-0 mt-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control form-control-dark" placeholder="Buscar canción o artista...">
                            </div>
                        </div>
                        <div class="sidebar-scroll p-3" id="listaCanciones">
                            <?php while ($row = mysqli_fetch_assoc($resultado_canciones)) { ?>
                                <div class="item-cancion d-flex justify-content-between align-items-center p-3 card-dark mb-2"
                                    data-titulo="<?= strtolower($row['titulo']) ?>"
                                    data-artista="<?= strtolower($row['artista']) ?>">
                                    <div class="overflow-hidden">
                                        <h6 class="mb-1 fw-bold text-warning text-truncate small"><?= $row['titulo'] ?></h6>
                                        <p class="mb-0 text-secondary smaller text-truncate"><?= $row['artista'] ?></p>
                                    </div>
                                    <form action="cancion_añadir.php" method="POST" class="ms-2">
                                        <input type="hidden" name="idCancion" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-main btn-sm">Añadir</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-cola">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary bg-dark">
                            <h6 class="text-secondary small mb-0">SIGUIENTES TEMAS</h6>
                            <?php if (mysqli_num_rows($result_cola) > 0) { ?>
                                <form action="vaciar_cola.php" method="POST" onsubmit="return confirm('¿Vaciar toda la lista?');">
                                    <button type="submit" name="limpiar_cola" class="btn btn-outline-danger btn-sm border-0 py-0" style="font-size: 0.7rem;">
                                        <input type="hidden" name="idUsuario" value="<?= $idUsuario ?> ">
                                        <i class="bi bi-trash3-fill me-1"></i>VACIAR
                                    </button>
                                </form>
                            <?php } ?>
                        </div>

                        <div class="sidebar-scroll p-3">
                            <?php if (mysqli_num_rows($result_cola) > 0) {
                                mysqli_data_seek($result_cola, 0);
                                while ($row = mysqli_fetch_assoc($result_cola)) {
                                    $idC = $row['id_cancion'];
                                    $res_c = mysqli_query($conn, "SELECT * FROM canciones WHERE id = '$idC'");
                                    $c = mysqli_fetch_assoc($res_c);
                                    $esSonando = ($row['id'] == $id_colaPrimera);
                            ?>
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 <?= $esSonando ? 'border-start border-danger border-3 bg-danger bg-opacity-10' : 'card-dark' ?>">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 fw-bold <?= $esSonando ? 'text-white' : 'text-warning' ?> text-truncate small"><?= $c['titulo'] ?></h6>
                                            <p class="mb-0 text-secondary smaller text-truncate"><?= $c['artista'] ?></p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">


                                            <!-- BOTONES DE MOVER EN LA COLA -->
                                            <form action="cancion_mover.php" method="POST" class="d-flex gap-1">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">

                                                <button type="submit" name="mover" value="subir" class="btn btn-link text-secondary p-0">
                                                    <i class="bi bi-caret-up-fill me-5"></i>
                                                </button>

                                                <button type="submit" name="mover" value="bajar" class="btn btn-link text-secondary p-0">
                                                    <i class="bi bi-caret-down-fill me-5"></i>
                                                </button>
                                            </form>

                                            <!-- BOTONES DE QUITAR DE LA COLA -->
                                            <form action="cancion_quitar.php" method="POST">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php }
                            } else { ?>
                                <p class="text-center text-muted py-5">Cola vacía</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8 col-xl-9 player-height bg-black py-4">
                <?php if ($cancion_actual) { ?>
                    <div class="container-fluid text-center">
                        <h2 class="h4 fw-bold text-warning mb-1"><?= $cancion_actual['titulo'] ?></h2>
                        <p class="text-secondary mb-4"><?= $cancion_actual['artista'] ?></p>
                        <div class="ratio ratio-16x9 bg-dark rounded shadow-lg border border-secondary mb-4 mx-auto" style="max-width: 900px;">
                            <video id="videoKaraoke" controls preload="metadata">
                                <source src="<?= $cancion_actual['archivo'] ?>" type="video/mp4">
                            </video>
                        </div>
                        <div class="d-flex justify-content-center gap-2">
                            <form action="" method="POST">
                                <button name="siguiente" class="btn btn-main px-4"><i class="bi bi-skip-forward-fill me-2"></i>Siguiente</button>
                            </form>
                            <button id="btnFullscreen" class="btn btn-outline-light"><i class="bi bi-fullscreen"></i></button>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="d-flex flex-column justify-content-center align-items-center text-center w-100 h-100 py-5">
                        <i class="bi bi-mic-fill display-1 text-secondary opacity-25"></i>
                        <h3 class="text-muted mt-4">No hay canciones en cola</h3>
                    </div>
                <?php } ?>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                // Filtro de búsqueda 
                document.addEventListener('DOMContentLoaded', function() {
                    const buscador = document.getElementById('inputBusqueda');

                    buscador.addEventListener('input', function() {
                        let texto = this.value.toLowerCase().trim();
                        let canciones = document.querySelectorAll('.item-cancion');

                        canciones.forEach(function(item) {
                            let titulo = item.getAttribute('data-titulo')
                            "";
                            let artista = item.getAttribute('data-artista')
                            "";

                            if (titulo.includes(texto) || artista.includes(texto)) {
                                item.classList.remove('d-none');
                                item.classList.add('d-flex');
                            } else {
                                item.classList.remove('d-flex');
                                item.classList.add('d-none');
                            }
                        });
                    });
                });
                // Pantalla completa
                const btnFs = document.getElementById('btnFullscreen');
                if (btnFs) {
                    btnFs.addEventListener('click', () => {
                        const video = document.getElementById('videoKaraoke');
                        if (video.requestFullscreen) video.requestFullscreen();
                        else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
                    });
                }

                // Si venimos de mover o borrar una canción, abrimos la pestaña de cola automáticamente
                if (window.location.search.includes("cola=1")) {
                    new bootstrap.Tab(
                        document.querySelector('[data-bs-target="#tab-cola"]')
                    ).show();
                }
            </script>
</body>

</html>