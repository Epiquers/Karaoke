<?php
session_start();
include 'includes/conexion.php';

$error = "";
$ok    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre    = mysqli_real_escape_string($conn, $_POST["nombre"]);
    $email     = mysqli_real_escape_string($conn, $_POST["email"]);
    $password  = $_POST["password"];
    $password2 = $_POST["password2"];

    if (empty($nombre) || empty($email) || empty($password) || empty($password2)) {
        $error = "❌ Todos los campos son obligatorios.";
    } elseif ($password !== $password2) {
        $error = "❌ Las contraseñas no coinciden.";
    } else {
        $check_email = "SELECT id FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($conn, $check_email);
        if (mysqli_num_rows($resultado) > 0) {
            $error = "⚠️ Este correo ya está registrado.";
        } else {
            $consulta_registro = "INSERT INTO usuarios (nombre, email, passwd) VALUES ('$nombre', '$email', '$password')";
            if (mysqli_query($conn, $consulta_registro)) {
                $ok = "✅ Registro exitoso. Redirigiendo...";
                header("refresh:2;url=index.php");
            } else {
                $error = "❌ Error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantabile - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <?php include __DIR__ . '/includes/favicon.php'; ?>
    <style>
        body {
            background: url('img/fondologin.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card-registro {
            background: rgba(0, 0, 0, 0.75); /* Fondo oscuro semitransparente */
            backdrop-filter: blur(10px);    /* Efecto de desenfoque cristal */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
        }

        .btn-main {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            border: none;
            color: white;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-main:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
            color: white;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: none;
            border-color: #00d2ff;
        }
         .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* -- Responsive móvil -- */
        @media (max-width: 575.98px) {
            .card-registro {
                padding: 2.5rem 1.5rem !important;
            }
            .form-label {
                font-size: 1.3rem;
            }
            .form-control {
                font-size: 1.3rem;
                padding: 0.85rem 1rem;
                min-height: 3.2rem;
            }
            .btn-main {
                font-size: 1.3rem;
                padding: 0.9rem;
            }
            .card-registro img {
                width: 200px !important;
            }
            .card-registro p, .card-registro a, .card-registro small {
                font-size: 1.1rem;
            }
        }

    </style>
</head>
<body>

    <div class="container  min-vh-100 d-flex align-items-center">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                
                <div class="card card-registro p-4 text-light">
                    <img src="img/logoregistro.png" alt="Logo Kantabile" class="mb-3 align-self-center" style="width: 150px;">

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2" style="font-size: 0.9rem;"><?php echo $error; ?></div>
                    <?php elseif ($ok): ?>
                        <div class="alert alert-success py-2" style="font-size: 0.9rem;"><?php echo $ok; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre de usuario</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre y apellido">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar Contraseña</label>
                            <input type="password" name="password2" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-main w-100 py-2 mt-2">REGISTRARSE</button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        <small class="text-secondary">¿Ya tienes cuenta?</small><br>
                        <a href="index.php" class="text-info text-decoration-none">Inicia sesión aquí</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>