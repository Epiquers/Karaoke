<?php
session_start();
include '../includes/conexion.php';

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado


// Verificación de seguridad básica
if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../index.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$mensaje = "";
$query = "";
$validar = true;

// Carga los datos actuales para rellenar el formulario (necesario antes del POST para validar contraseña antigua)
$res = mysqli_query($conn, "SELECT * FROM usuarios WHERE id = '$idUsuario'");
$usuario = mysqli_fetch_assoc($res);

// Actualiza nombre, email y contraseña (opcional) del usuario en sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevoNombre = $_POST['nombre'];
    $nuevoEmail = $_POST['email'];
    $passwordAntigua = $_POST['passwd'];
    $nuevaPassword = $_POST['passwd1'];
    $nuevaPassword2 = $_POST['passwd2'];

    if (!empty($nuevaPassword)) {
        // Verificar que la contraseña antigua es correcta
        if ($passwordAntigua !== $usuario['passwd']) {
            $mensaje = "<div class='alert alert-danger bg-danger text-white border-0 text-center'>La contraseña antigua no es correcta.</div>";
            $validar = false;
        } elseif ($nuevaPassword !== $nuevaPassword2) {
            $mensaje = "<div class='alert alert-danger bg-danger text-white border-0 text-center'>Las contraseñas no coinciden. Por favor, inténtalo de nuevo.</div>";
            $validar = false;
        } else {
            // Actualización con contraseña, nombre y email
            $query = "UPDATE usuarios SET nombre = '$nuevoNombre', email = '$nuevoEmail', passwd = '$nuevaPassword' WHERE id = '$idUsuario'";
        }
    } else {
        // Actualización solo de nombre y email
        $query = "UPDATE usuarios SET nombre = '$nuevoNombre', email = '$nuevoEmail' WHERE id = '$idUsuario'";
    }

    if ($validar && !empty($query)) {
        if (mysqli_query($conn, $query)) {
            $_SESSION['nombre'] = $nuevoNombre; // Actualizar el nombre en la sesión para el navbar
            $mensaje = "<div class='alert alert-success bg-success text-white border-0 text-center'>Perfil actualizado correctamente.</div>";
            // Recargar datos actualizados
            $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE id = '$idUsuario'");
            $usuario = mysqli_fetch_assoc($res);
        } else {
            $mensaje = "<div class='alert alert-danger bg-danger text-white border-0 text-center'>Error al actualizar el perfil.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Kantabile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <?php include '../includes/favicon.php'; ?>
</head>

<body class="text-light">

    <?php include '../includes/navbar.php'; ?>

    <!-- ===== FORMULARIO DE EDICIÓN DEL PERFIL ===== -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="text-center mb-4">
                    <i class="bi bi-person-circle display-1 text-danger"></i>
                    <h2 class="fw-bold mt-2">Mi Perfil</h2>
                    <p class="text-secondary small">Gestiona tu información de cuenta</p>
                </div>

                <?= $mensaje ?>

                <div class="card card-dark">
                    <div class="card-body p-4">
                        <form action="" method="POST">

                            <div class="mb-4">
                                <label for="nombre" class="form-label small fw-bold">NOMBRE DE USUARIO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nombre" id="nombre" class="form-control"
                                        value="<?= $usuario['nombre'] ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label small fw-bold">CORREO ELECTRÓNICO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="<?= $usuario['email'] ?>" required>
                                </div>
                            </div>

                            <hr class="border-secondary my-4">

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">ANTIGUA CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="passwd" id="passwd" class="form-control"
                                        placeholder="Requerida solo para cambiar contraseña">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">NUEVA CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="passwd1" id="passwd1" class="form-control"
                                        placeholder="Dejar en blanco para no cambiar">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">CONFIRMAR CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="passwd2" id="passwd" class="form-control"
                                        placeholder="Dejar en blanco para no cambiar">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-main text-white py-2 fw-bold">
                                    GUARDAR CAMBIOS
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>