<?php
session_start();
include("../includes/conexion.php");

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado

// Lógica de actualización completa (nombre, email y contraseña opcional) desde el formulario inline
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $estado = $_POST['nuevo_estado'];
    $password = $_POST['passwd'];

    // Si el password no está vacío, lo actualizamos también
    if (!empty($password)) {
        $sql = "UPDATE usuarios SET nombre='$nombre', email='$email', passwd='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE usuarios SET nombre='$nombre', email='$email' WHERE id=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: perfil_admin.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
// Botón rápido para cambiar el estado bloqueado/activo del usuario
if (isset($_POST['accion_estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['nuevo_estado'];
    
    mysqli_query($conn, "UPDATE usuarios SET estado = '$estado' WHERE id = $id");
    header("Location: perfil_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Kantabile - Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #000;
            color: white;
        }

        .card-dark {
            background-color: #151515;
            border: 1px solid #333;
            padding: 20px;
            border-radius: 10px;
        }

        .form-control-dark {
            background: #222;
            border: 1px solid #444;
            color: white;
        }

        .form-control-dark:focus {
            background: #222;
            color: white;
            border-color: #dc3545;
            box-shadow: none;
        }

        .fila-edicion {
            background-color: #0a0a0a !important;
            border-left: 4px solid #dc3545;
        }

        .table-dark {
            --bs-table-bg: #151515;
        }
    </style>
</head>

<body>
    <?php include("../includes/navbar.php"); ?>

    <!-- ===== TABLA DE USUARIOS: edición inline y control de estado ===== -->
    <div class="container mt-5">
    <div class="card-dark">
            <h2 class="mb-4"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h2>

            <table class="table table-dark table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th class="d-none d-sm-table-cell">Password</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                        <?php
                        // Recorre todos los usuarios y genera una fila normal + una fila colapsable de edición
                        $resultado = mysqli_query($conn, "SELECT * FROM usuarios");
                    while ($row = mysqli_fetch_assoc($resultado)) {
                        $id =  $row['id'];
                        // ID único para la fila de edición
                        $bloqueado = $row['estado'] == 1; // 1 = Bloqueado, 0 = Activo

                        // Si es 1 (bloqueado) -> botón rojo. Si es 0 (activo) -> botón verde.
                        $btn_color = $bloqueado ? 'btn-outline-danger' : 'btn-outline-success';
                        $btn_texto = $bloqueado ? 'Bloqueado' : 'Desbloqueado';

                    ?>

                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><strong><?php echo $row['nombre']; ?></strong></td>
                            <td class="text-secondary"><?php echo $row['email']; ?></td>

                            <td class="text-secondary d-none d-sm-table-cell"><?php echo $row['passwd']; ?></td>

                            <td class="text-end">

                             <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                    <?php if ($bloqueado) { ?>
                                        <input type="hidden" name="nuevo_estado" value="0">
                                        <button type="submit" name="accion_estado" class="btn btn-sm <?php echo $btn_color ?>">
                                            <?php echo $btn_texto ?>
                                        </button>
                                    <?php } else { ?>
                                        <input type="hidden" name="nuevo_estado" value="1">
                                        <button type="submit" name="accion_estado" class="btn btn-sm <?php echo $btn_color ?>">
                                            <?php echo $btn_texto ?>
                                        </button>
                                    <?php } ?>
                                </form>

                                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="collapse" data-bs-target="#fila_<?php echo $id;?>">Editar</button>

                            </td>
                        </tr>

                        <tr class="collapse fila-edicion" id="fila_<?php echo $id;?>">
                            <td colspan="6">
                                <form method="POST" class="row p-3 g-2">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                    <div class="col-md-3">
                                        <label class="small text-secondary">Nombre</label>
                                        <input type="text" name="nombre" class="form-control form-control-dark" value="<?php echo $row['nombre']; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-secondary">Email</label>
                                        <input type="email" name="email" class="form-control form-control-dark" value="<?php echo $row['email']; ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small text-secondary">Password (Opcional)</label>
                                        <input type="password" name="passwd" class="form-control form-control-dark" placeholder="Nueva contraseña">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" name="update_user" class="btn btn-danger w-100">Guardar</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php }; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>