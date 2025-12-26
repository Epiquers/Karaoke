<?php
session_start();
include 'includes/conexion.php';

$error = "";
$ok    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = $_POST["nombre"];
    $email     = $_POST["email"];
    $password  = $_POST["password"];
    $password2 = $_POST["password2"];

    if (empty($nombre) || empty($email) || empty($password) || empty($password2)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($password !== $password2) {
        $error = "Las contraseñas no coinciden";
    } else {
        $consulta_registro = "INSERT INTO usuarios (nombre, email, passwd) VALUES ('$nombre', '$email', '$password')";
        mysqli_query($conn, $consulta_registro);
        $ok = "Usuario registrado correctamente";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Karaoke Online - Registro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-dark text-light">

    <div class="container min-vh-100 d-flex align-items-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <div class="card card-dark p-4">
                    <h2 class="text-center mb-4">📝 Registro</h2>

                    <?php
                    if ($error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    } elseif ($ok) {
                        echo "<div class='alert alert-success'>$ok</div>";
                    }
                    ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Repetir contraseña</label>
                            <input type="password" name="password2" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-main w-100">Registrarse</button>
                    </form>

                    <p class="text-center text-secondary mt-3">
                        ¿Ya tienes cuenta?
                        <a href="index.php">Inicia sesión</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>