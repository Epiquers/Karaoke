<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-secondary">
    <div class="container-fluid px-4 mx-lg-4">
        <a class="navbar-brand fw-bold text-warning" href="canciones_admin.php">
            🎤 Kantabile
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUsuario">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarUsuario">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <span class="nav-link text-secondary me-2">
                        <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION["nombre"]) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <?php
                    if (isset($_SESSION['rol']) && $_SESSION['rol'] == 2) {

                        // Detectamos si el nombre del archivo actual es el del reproductor
                        $pagina_actual = basename($_SERVER['PHP_SELF']);
                        if ($pagina_actual == 'canciones.php') {
                    ?>
                            <a class="nav-link fw-bold text-info" href="../administrador/canciones_admin.php">Gestión</a>
                        <?php } else { ?>
                            <a class="nav-link fw-bold text-warning" href="../usuario/canciones.php">Vista Reproductor</a>
                        <?php } ?>
                </li>

                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light btn-sm px-3 me-2" href="perfil_admin.php">
                        <i class="bi bi-gear-fill me-1"></i> Usuarios
                    </a>
                </li>
                <?php
                    } else {
                        $pagina_actual = basename($_SERVER['PHP_SELF']);

                        if ($pagina_actual == 'perfil.php') {
                ?>
                    <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php">Principal</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/peticiones.php">Peticiones</a></li>

                <?php
                        } elseif ($pagina_actual == 'canciones.php') {
                ?>
                    <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/perfil.php">Mi Perfil</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/peticiones.php">Peticiones</a></li>

                <?php
                        } elseif ($pagina_actual == 'peticiones.php') {
                ?>
                    <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php">Principal</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/perfil.php">Mi Perfil</a></li>

            <?php
                        } 
                    } 
            ?>

            <li class="nav-item">
                <a class="btn btn-main btn-sm text-white px-3 ms-3" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>