<?php
session_start();
include 'includes/conexion.php';

// Forzamos UTF-8
header('Content-Type: text/html; charset=utf-8');

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        $consulta = "SELECT * FROM usuarios WHERE email='$email' AND passwd='$password'";
        $result = mysqli_query($conn, $consulta);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["idUsuario"] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['rol'] = $row['rol'];

            switch ($_SESSION['rol']) {
                case 1:
                    header("Location: usuario/canciones.php");
                    exit;
                case 2:
                    header('Location: administrador/canciones_admin.php');
                    exit();
                default:
                    header('Location: index.php');
                    exit();
            }
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
    <title>Kantabile - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include __DIR__ . '/includes/favicon.php'; ?>
    <style>
        body {
            background-color: #1a1a1a;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .card-login {
            background: rgba(0, 0, 0, 0.75); 
            backdrop-filter: blur(10px);
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

        /* Placeholder en blanco con transparencia */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* -- Responsive móvil -- */
        @media (max-width: 575.98px) {
            .card-login {
                padding: 2rem 1.5rem !important;
            }
            .form-label {
                font-size: 1.15rem;
            }
            .form-control {
                font-size: 1.15rem;
                padding: 0.7rem 1rem;
            }
            .btn-main {
                font-size: 1.15rem;
                padding: 0.75rem;
            }
            .card-login img {
                width: 180px !important;
            }
            .card-login p, .card-login a, .card-login small {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                <div class="card card-login p-4 text-light text-center">
                    <img src="img/logoregistro.png" alt="Logo Kantabile" class="mb-3 align-self-center" style="width: 150px;">
                

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="text-start">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-main w-100 py-2 mt-2">ENTRAR</button>
                    </form>

                    <p class="mt-4 mb-0">
                        <small class="text-secondary">¿Aún no tienes cuenta?</small><br>
                        <a href="registro.php" class="text-info text-decoration-none">Regístrate gratis</a>
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>