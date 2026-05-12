<?php
session_start();
include '../includes/conexion.php';
include 'seguridad_usuario.php';

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado


$idUsuario = $_SESSION['idUsuario'];
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
    <?php include '../includes/favicon.php'; ?>
</head>

<body class="text-light">

    <?php include '../includes/navbar.php'; ?>

    <!-- ===== FORMULARIO PARA ENVIAR UNA PETICIÓN DE CANCIÓN ===== -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="text-center mb-4">
                    <i class="bi bi-chat-text-fill display-1 text-warning"></i>
                    <h2 class="fw-bold mt-2">Peticiones</h2>
                    <p class="text-secondary small">Pide una canción que no esté en la lista</p>
                </div>


                <div class="card card-dark">
                    <div class="card-body p-4">
                        <?php
                        // Muestra el flash de confirmación si viene de peticion_enviar.php y lo borra de la sesión
                        if (isset($_SESSION['mensaje'])) {
                            echo '<div class="alert alert-success text-center" role="alert">' . $_SESSION['mensaje'] . '</div>';
                            unset($_SESSION['mensaje']); // Lo borramos para que no reaparezca al recargar
                        }
                        ?>
                        <form action="peticion_enviar.php" method="POST">
                            <div class="mb-4">
                                <label class="form-label small fw-bold">ARTISTA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-mic"></i></span>
                                    <input type="text" class="form-control" placeholder="Introduce artista" name="artista" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">TÍTULO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-music-note-beamed"></i></span>
                                    <input type="text" class="form-control" placeholder="Introduce título" name="titulo" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-main text-white py-2 fw-bold">ENVIAR PETICIÓN</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>