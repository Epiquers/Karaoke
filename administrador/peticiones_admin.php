<?php
session_start();
include("../includes/conexion.php");

// Cambiar estado de una petición
if (isset($_POST['cambiar_estado'])) {
    $id_peticion = ($_POST['id_peticion']);
    $nuevo_estado = ($_POST['nuevo_estado']); // 0 pendiente, 1 completo
    $consulta_estado = "UPDATE peticiones SET estado = $nuevo_estado WHERE id_peticion = $id_peticion";
    mysqli_query($conn, $consulta_estado);
    header("Location: peticiones_admin.php");
    exit();
}

// Obtener peticiones con nombre de usuario
$consulta = "SELECT p.id_peticion, p.usuario, u.nombre AS nombre_usuario, p.artista, p.titulo, p.estado, p.fechaHora
             FROM peticiones p
             LEFT JOIN usuarios u ON p.usuario = u.id
             ORDER BY p.fechaHora DESC";
$resultado = mysqli_query($conn, $consulta);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Peticiones</title>
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

        .table-dark {
            --bs-table-bg: #151515;
        }
    </style>
</head>

<body>
    <?php include("../includes/navbar.php"); ?>

    <div class="container mt-5">
        <div class="card-dark">
            <h2 class="mb-4"><i class="bi bi-chat-dots-fill me-2"></i>Peticiones de usuarios</h2>

            <table class="table table-dark table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Artista</th>
                        <th>Título</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($resultado) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($resultado)) {
                            $completa = $row['estado'] == 1;
                            $btn_color = $completa ? 'btn-outline-success' : 'btn-outline-danger';
                            $btn_texto = $completa ? 'Completo' : 'Pendiente';
                            $nombre_usuario = $row['nombre_usuario'];
                        ?>
                            <tr>
                                <td><?php echo $row['id_peticion']; ?></td>
                                <td><strong><?php echo $nombre_usuario; ?></strong></td>
                                <td class="text-secondary"><?php echo $row['artista']; ?></td>
                                <td class="text-secondary"><?php echo $row['titulo']; ?></td>
                                <td class="text-secondary"><?php echo date('d/m/Y H:i', strtotime($row['fechaHora'])); ?></td>
                                <td class="text-center">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="id_peticion" value="<?php echo $row['id_peticion']; ?>">
                                        <input type="hidden" name="nuevo_estado" value="<?php echo $completa ? 0 : 1; ?>">
                                        <button type="submit" name="cambiar_estado" class="btn btn-sm <?php echo $btn_color; ?>">
                                            <?php echo $btn_texto; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" class="text-center text-secondary">No hay peticiones registradas.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>