<?php
session_start();
include '../includes/conexion.php';

if (!isset($_SESSION['nombre'])) {
    header("Location: ../login.php");
    exit();
}

$consulta = "SELECT * FROM canciones ORDER BY id DESC";
$resultado = mysqli_query($conn, $consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reproductor Profesional - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* Ajustes específicos para el look KaraFun */
        .karaoke-list-item {
            background: rgba(255, 255, 255, 0.03);
            transition: all 0.3s ease;
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid transparent;
        }
        .karaoke-list-item:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: #dc3545;
            transform: translateX(5px);
        }
        .play-icon-hover {
            color: #dc3545;
            font-size: 1.5rem;
            opacity: 0.7;
        }
        .karaoke-list-item:hover .play-icon-hover {
            opacity: 1;
        }
        .badge-quality {
            font-size: 0.6rem;
            letter-spacing: 1px;
            border: 1px solid #444;
        }
    </style>
</head>
<body class="text-light">

    <?php include '../includes/navbar.php'; ?>

    <div class="container my-5">
        <div class="row mb-5 align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold"><span class="text-danger">Kanta</span>bile Player</h1>
                <p class="text-secondary">Modo de visualización profesional para administradores.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Buscar canción o artista...">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-dark border-0 shadow-lg">
                    <div class="card-body p-4">
                        
                        <div class="d-flex text-secondary mb-3 px-3 small fw-bold text-uppercase">
                            <div style="width: 50px;">#</div>
                            <div class="flex-grow-1">Título / Artista</div>
                            <div class="d-none d-md-block" style="width: 150px;">Formato</div>
                            <div class="text-end" style="width: 100px;">Acción</div>
                        </div>

                        <?php
                        $contador = 1;
                        if (mysqli_num_rows($resultado) > 0) {
                            while ($c = mysqli_fetch_assoc($resultado)) {
                                ?>
                                <div class="karaoke-list-item d-flex align-items-center p-3">
                                    <div class="text-secondary fw-bold" style="width: 50px;">
                                        <?= str_pad($contador++, 2, "0", STR_PAD_LEFT) ?>
                                    </div>
                                    
                                    <div class="flex-grow-1 d-flex align-items-center">
                                        <div class="me-3 d-none d-sm-block">
                                            <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-mic-fill text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($c['titulo']) ?></h6>
                                            <small class="text-secondary"><?= htmlspecialchars($c['artista']) ?></small>
                                        </div>
                                    </div>

                                    <div class="d-none d-md-block" style="width: 150px;">
                                        <span class="badge badge-quality text-secondary uppercase">VIDEO MP4</span>
                                        <span class="badge badge-quality text-danger ms-1">HD</span>
                                    </div>

                                    <div class="text-end" style="width: 100px;">
                                        <a href="reproductor_video.php?id=<?= $c['id'] ?>" class="play-icon-hover">
                                            <i class="bi bi-play-circle-fill"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<div class='text-center py-5'><i class='bi bi-disc text-secondary display-1'></i><p class='mt-3 text-muted'>Tu biblioteca está vacía.</p></div>";
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>