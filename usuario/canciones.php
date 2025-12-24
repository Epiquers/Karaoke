<?php include '../includes/conexion.php'; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canciones - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold mb-3">🎵 Canciones</h1>
                <p class="lead text-secondary">Selecciona tus canciones favoritas</p>
            </div>
        </div>

        <!-- FILA PRINCIPAL -->
        <div class="row">

            <!-- COLUMNA IZQUIERDA: CANCIONES -->
            <div class="col-12 col-lg-7 mb-4 order-2 order-lg-0">
                <div class="card card-dark">
                    <div class="card-body p-4">
                        <h5 class="card-title text-center mb-4">Lista de canciones</h5>

                        <?php
                        $consulta_canciones = "SELECT * FROM canciones";
                        $resultado_canciones = mysqli_query($conn, $consulta_canciones);

                        if (mysqli_num_rows($resultado_canciones) > 0) {
                            while ($row = mysqli_fetch_assoc($resultado_canciones)) {
                                echo "
                            <div class='d-flex justify-content-between align-items-center p-3 border-bottom'>
                                <div>
                                    <h6 class='mb-1 fw-bold text-warning'>" . $row['titulo'] . "</h6>
                                    <p class='mb-0 text-secondary'>" . $row['artista'] . "</p>
                                </div>
                                <form action='cancion_añadir.php' method='POST'>
                                    <input type='hidden' name='idCancion' value='" . $row['id'] . "'>
                                    <button type='submit' class='btn btn-main btn-sm'>Añadir a cola</button>
                                </form>
                            </div>
                            ";
                            }
                        } else {
                            echo "<p class='text-center text-muted py-4'>No hay canciones</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: COLA -->
            <div class="col-12 col-lg-5 mb-4 order-first order-lg-0">
                <div class="card card-dark">
                    <div class="card-body p-4">
                        <h5 class="card-title text-center mb-4">Cola de reproducción</h5>

                        <?php
                        $consulta_cola = "SELECT * FROM cola WHERE id_usuario='" . $_SESSION['idUsuario'] . "'";
                        $resultado_cola = mysqli_query($conn, $consulta_cola);

                        if (mysqli_num_rows($resultado_cola) > 0) {
                            while ($row = mysqli_fetch_assoc($resultado_cola)) {
                                $consulta_canciones2 = "SELECT * FROM canciones WHERE id='" . $row['id_cancion'] . "'";
                                $resultado_canciones2 = mysqli_query($conn, $consulta_canciones2);

                                $row2 = mysqli_fetch_assoc($resultado_canciones2);

                                echo "
                                    <div class='d-flex justify-content-between align-items-center p-3 border-bottom'>
                                        <div>
                                            <h6 class='mb-1 fw-bold text-warning'>" . $row2['titulo'] . "</h6>
                                            <p class='mb-0 text-warning'>" . $row2['artista'] . "</p>
                                        </div>
                                        <form action='cancion_quitar.php' method='POST'>
                                            <input type='hidden' name='idCola' value='" . $row['id'] . "'>
                                            <button type='submit' class='btn btn-main btn-sm'>Quitar de cola</button>
                                        </form>
                                    </div>
                                    ";
                                }
                        } else {
                            echo "<p class='text-center text-danger py-4'>Aún no has añadido canciones a la cola</p>";
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