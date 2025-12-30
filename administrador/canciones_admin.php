<?php
session_start();
include '../includes/conexion.php';

// 1️⃣ ELIMINAR canción
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $idEliminar = $_POST['id_cancion'];
    $consulta_borrar = "DELETE FROM canciones WHERE id = '$idEliminar'";
    mysqli_query($conn, $consulta_borrar);
}

// 2️⃣ TODAS las canciones
$consulta_todas = "SELECT * FROM canciones ORDER BY id DESC";
$result_todas = mysqli_query($conn, $consulta_todas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión Canciones - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container my-5">
        <div class="row">
            <!-- FORMULARIO AÑADIR -->
            <div class="col-12 col-lg-6 mb-5">
                <div class="card card-dark">
                    <div class="card-body p-4">
                        <h3 class="mb-4 text-center text-warning">🎵 Añadir nueva canción</h3>
                        <form action="cancion_insertar.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" class="form-control" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Artista</label>
                                <input type="text" class="form-control" name="artista" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Archivo MP4</label>
                                <input type="file" class="form-control" name="archivo" accept="video/mp4" required>
                            </div>
                            <button type="submit" class="btn btn-main w-100">Guardar canción</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- LISTA CANCIONES -->
            <div class="col-12 col-lg-6">
                <div class="card card-dark">
                    <div class="card-body p-4">
                        <h3 class="mb-4 text-center text-warning">📋 Canciones (<?php echo mysqli_num_rows($result_todas); ?>)</h3>

                        <?php
                        if (mysqli_num_rows($result_todas) > 0) {
                            while ($cancion = mysqli_fetch_assoc($result_todas)) {
                                echo "
                                <div class='d-flex justify-content-between align-items-center p-3 border-bottom'>
                                    <div>
                                        <h6 class='mb-1 fw-bold text-warning'>" . $cancion['titulo'] . "</h6>
                                        <p class='mb-0 text-secondary'>" . $cancion['artista'] . "</p>
                                        <small class='text-muted'>" . $cancion['archivo'] . "</small>
                                    </div>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='id_cancion' value='" . $cancion['id'] . "'>
                                        <button type='submit' name='eliminar' class='btn btn-danger btn-sm'>🗑️ Eliminar</button>
                                    </form>
                                </div>";
                            }
                        } else {
                            echo "<p class='text-center text-muted py-4'>No hay canciones</p>";
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