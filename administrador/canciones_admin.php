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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="text-light">

    <div class="app-container">
        <?php include '../includes/navbar.php'; ?>

        <div class="admin-main">
            <div class="admin-form-panel p-4">
                <div class="card card-dark shadow">
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
                            <div class="mb-4">
                                <label class="form-label">Archivo MP4</label>
                                <input type="file" class="form-control" name="archivo" accept="video/mp4" required>
                            </div>
                            <button type="submit" class="btn btn-main w-100 text-white fw-bold">GUARDAR CANCIÓN</button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 p-3 border border-secondary rounded opacity-50">
                    <small class="text-secondary d-block text-center">
                        Solo se permiten archivos en formato MP4.
                    </small>
                </div>
            </div>

            <div class="admin-list-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="m-0 fw-bold">Gestión de Catálogo</h2>
                    <span class="badge bg-danger rounded-pill px-3"><?php echo mysqli_num_rows($result_todas); ?> canciones</span>
                </div>

                <div class="card card-dark shadow">
                    <div class="card-body p-0">
                        <?php
                        if (mysqli_num_rows($result_todas) > 0) {
                            while ($cancion = mysqli_fetch_assoc($result_todas)) {
                                echo "
                                <div class='d-flex justify-content-between align-items-center p-3 border-bottom border-secondary'>
                                    <div class='ps-2'>
                                        <h6 class='mb-1 fw-bold text-warning'>" . $cancion['titulo'] . "</h6>
                                        <p class='mb-0 text-secondary small'>" . $cancion['artista'] . "</p>
                                        <code class='text-muted smaller' style='font-size: 0.7rem;'>" . $cancion['archivo'] . "</code>
                                    </div>
                                    <div class='pe-2'>
                                        <form method='POST' style='display:inline;' onsubmit=\"return confirm('¿Seguro que quieres eliminar esta canción?');\">
                                            <input type='hidden' name='id_cancion' value='" . $cancion['id'] . "'>
                                            <button type='submit' name='eliminar' class='btn btn-outline-danger btn-sm border-0'>
                                                <i class='bi bi-trash3 fs-5'></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<p class='text-center text-muted py-5'>No hay canciones registradas</p>";
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