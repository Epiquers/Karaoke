<?php
session_start();
include '../includes/conexion.php';
include 'seguridad_admin.php';

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado

// ELIMINAR canción y sus archivos físicos (incluyendo limpieza de carpetas)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar'])) {
    $idEliminar = mysqli_real_escape_string($conn, $_POST['id_cancion']);

    // 1. Obtenemos todas las rutas de la base de datos
    $res = mysqli_query($conn, "SELECT voz, instrumental, letra FROM canciones WHERE id = '$idEliminar'");
    
    if ($fila = mysqli_fetch_assoc($res)) {
        // Guardamos las rutas de los directorios para revisarlos luego
        $directoriosARevisar = [];

        // Lista de archivos a borrar
        $archivosABorrar = [
            '../' . $fila['voz'],      // La voz guía
            '../' . $fila['instrumental'], // La instrumental
            '../' . $fila['letra']         // El archivo .lrc
        ];

        foreach ($archivosABorrar as $rutaArchivo) {
            if (!empty($rutaArchivo) && file_exists($rutaArchivo)) {
                // Guardamos la carpeta que contiene el archivo
                $directoriosARevisar[] = dirname($rutaArchivo);
                
                // Borramos el archivo
                unlink($rutaArchivo);
            }
        }

        // 2. Limpieza de directorios vacíos
        // Eliminamos duplicados de directorios por si voz y letra comparten carpeta
        $directoriosARevisar = array_unique($directoriosARevisar);

        foreach ($directoriosARevisar as $dir) {
            // Verificamos si es un directorio y si está vacío
            // scandir devuelve . y .. por eso contamos si es igual a 2
            if (is_dir($dir)) {
                $files = scandir($dir);
                if (count($files) <= 2) { 
                    rmdir($dir); 
                }
            }
        }
    }

    // 3. Ahora borramos el registro de la BD
    $consulta_borrar = "DELETE FROM canciones WHERE id = '$idEliminar'";
    mysqli_query($conn, $consulta_borrar);

    // Refrescamos
    header("Location: canciones_admin.php");
    exit();
}

// Consulta todas las canciones del catálogo ordenadas por últimas añadidas
$consulta_todas = "SELECT * FROM canciones ORDER BY id DESC";
$result_todas = mysqli_query($conn, $consulta_todas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Canciones - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body class="text-light">

    <div class="app-container">
        <?php include '../includes/navbar.php'; ?>

        <!-- ===== LAYOUT: columna izquierda (formulario) + derecha (catálogo) ===== -->
        <div class="container-fluid">
            <div class="row">
                <!-- Columna izquierda: formulario para subir nueva canción -->
                <div class="col-lg-4 p-4" style="background: #151515;">
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
                            <div class="mb-3">
                                <label class="form-label">Estilo</label>
                                <select class="form-select" name="estilo" required>
                                    <option value="">Selecciona un estilo</option>
                                    <option value="Pop">Pop</option>
                                    <option value="Rock">Rock</option>
                                    <option value="Pop/Rock">Pop/Rock</option>
                                    <option value="Rock Alternativo">Rock Alternativo</option>
                                    <option value="Reggae">Reggae</option>
                                    <option value="Blues">Blues</option>
                                    <option value="Jazz">Jazz</option>
                                    <option value="Rap">Rap</option>
                                    <option value="Indie">Indie</option>
                                    <option value="Bachata">Bachata</option>
                                    <option value="Salsa">Salsa</option>
                                    <option value="Merengue">Merengue</option>
                                    <option value="Cumbia">Cumbia</option>
                                    <option value="Flamenco">Flamenco</option>
                                    <option value="Balada">Balada</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Archivo de Voz (.mp3)</label>
                                <input type="file" class="form-control" name="archivo_voz" accept=".mp3" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Archivo de Instrumental (.mp3)</label>
                                <input type="file" class="form-control" name="archivo_instrumental" accept=".mp3" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Archivo de Letra (.lrc/.txt)</label>
                                <input type="file" name="archivo_letra" class="form-control form-control-dark" accept=".lrc, .txt" required>
                            </div>
                            <button type="submit" class="btn btn-main w-100 text-white fw-bold">GUARDAR CANCIÓN</button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 p-3 border border-secondary rounded opacity-50">
                    <small class="text-secondary d-block text-center">
                        Solo se permiten archivos en formato MP3/MP4-LRC/TXT.
                    </small>
                </div>
            </div>
            <!-- Columna derecha: listado del catálogo con opción de eliminar -->
            <div class="col-lg-8 p-4">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>