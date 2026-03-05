<?php
session_start();
include '../includes/conexion.php';

// Verificación de seguridad básica
if (!isset($_SESSION['idUsuario'])) {
    header("Location: ../index.php");
    exit();
}

$idUsuario = $_SESSION['idUsuario'];
$mensaje = "";
$query = "";
$validar= true; // Variable para controlar si se debe ejecutar la consulta de actualización


// Lógica para actualizar el perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevoNombre = $_POST['nombre'];
    $nuevoEmail = $_POST['email']; // Nueva variable para el email
    $nuevaPassword = $_POST['passwd'];
    $nuevaPassword2 = $_POST['passwd2'];

    if (!empty($nuevaPassword)) {
        if ($nuevaPassword !== $nuevaPassword2) {
            $mensaje = "<div class='alert alert-danger bg-danger text-white border-0 text-center'>Las contraseñas no coinciden. Por favor, inténtalo de nuevo.</div>";
            $validar = false;
            // No continuar con la actualización si las contraseñas no coinciden
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
        } else {
            $mensaje = "<div class='alert alert-danger bg-danger text-white border-0 text-center'>Error al actualizar el perfil.</div>";
        }
    }
}

// Obtener datos actuales para mostrar en los inputs
$res = mysqli_query($conn, "SELECT * FROM usuarios WHERE id = '$idUsuario'");
$usuario = mysqli_fetch_assoc($res);
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
</head>

<body class="text-light">

    <?php include '../includes/navbar.php'; ?>

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
                                    <input type="text" name="passwd" id="passwd" class="form-control"
                                        value="<?= $usuario['passwd'] ?>" readonly>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-bold">NUEVA CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark border-0 text-secondary"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="passwd" id="passwd" class="form-control"
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