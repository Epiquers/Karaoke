<?php
session_start();
include 'includes/conexion.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // Consulta para sacar id del usuario
        $consulta = "SELECT * FROM usuarios WHERE email='$email' AND passwd='$password'";
        $result = mysqli_query($conn, $consulta);
        echo mysqli_error($conn);

        if (mysqli_num_rows($result) == 1) {
            //Usuario registrado
            $row = mysqli_fetch_assoc($result);
            $_SESSION["idUsuario"] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];

            // Redirigimos según el rol
            switch ($row['rol']) {
                case 1:
                    header("Location: usuario/canciones.php");
                    exit;
                case 2:
                    header('Location: administrador/lista.php');
                    exit();
                default:
                    // Si hay un rol raro, lo mandamos al login
                    header('Location: index.php');
                    exit();
            }
            mysqli_close($conn);
        } else {
            $error = "Usuario no registrado o datos incorrectos";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Karaoke Online - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="bg-dark text-light">

    <div class="container min-vh-100 d-flex align-items-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <div class="card card-dark p-4">
                    <h2 class="text-center mb-4">🎤 Karaoke Online</h2>

                    <?php
                    if ($error) {
                        echo '<div class="alert alert-danger">' . $error . '</div>';
                    }
                    ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-main w-100">Entrar</button>
                    </form>

                    <p class="text-center text-secondary mt-3">
                        ¿No tienes cuenta?
                        <a href="registro.php">Regístrate</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>