<?php 
include '../includes/conexion.php'; 

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canciones - Karaoke Online</title>
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

    <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card card-dark h-100">
                <div class="card-body d-flex flex-column text-center p-4">
                    <h5 class="card-title fw-bold mb-3">Bohemian Rhapsody</h5>
                    <p class="card-text text-secondary mb-3">Queen</p>
                    <p class="text-muted small mb-4">5:55 min</p>
                    <button class="btn btn-main w-100 mt-auto">Añadir a cola</button>
                </div>
            </div>
        </div>
        <!-- Más cards aquí -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
