<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="seguridad.php">
            🔐 Panel Admin
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <span class="nav-link text-warning">ADMIN: <?= htmlspecialchars($_SESSION["nombre"]) ?></span>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="canciones_admin.php">Canciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="usuarios_admin.php">Usuarios</a>
                </li>
                <li class="nav-item">
                    <?php 
                    // Detectamos si el nombre del archivo actual es el del reproductor
                    $pagina_actual = basename($_SERVER['PHP_SELF']);
                    if ($pagina_actual == 'reproductor_admin.php'){ 
                    ?>
                        <a class="nav-link fw-bold text-info" href="canciones_admin.php">Volver a Gestión</a>
                    <?php }else{ ?>
                        <a class="nav-link fw-bold text-warning" href="reproductor_admin.php">Vista Reproductor</a>
                    <?php } ?>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>