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

                        $pagina_actual = basename($_SERVER['PHP_SELF']);

                        // Para admin: mostrar siempre dos enlaces, ocultando el actual y alternando colores
                        if ($pagina_actual == 'canciones.php') {
                    ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../administrador/canciones_admin.php"><i class="bi bi-gear-fill me-1"></i>Gestión</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../administrador/perfil_admin.php"><i class="bi bi-people-fill me-1"></i>Usuarios</a></li>

            <?php
                        } elseif ($pagina_actual == 'canciones_admin.php') {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php"><i class="bi bi-music-note-list me-1"></i>Vista Reproductor</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../administrador/perfil_admin.php"><i class="bi bi-people-fill me-1"></i>Usuarios</a></li>

            <?php
                        } elseif ($pagina_actual == 'perfil_admin.php') {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php"><i class="bi bi-music-note-list me-1"></i>Vista Reproductor</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../administrador/canciones_admin.php"><i class="bi bi-gear-fill me-1"></i>Gestión</a></li>

            <?php
                        }
                    } else {
                        $pagina_actual = basename($_SERVER['PHP_SELF']);

                        if ($pagina_actual == 'perfil.php') {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php"><i class="bi bi-house-door-fill me-1"></i>Principal</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/peticiones.php"><i class="bi bi-chat-dots-fill me-1"></i>Peticiones</a></li>

            <?php
                        } elseif ($pagina_actual == 'canciones.php') {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/perfil.php"><i class="bi bi-person-circle me-1"></i>Mi Perfil</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/peticiones.php"><i class="bi bi-chat-dots-fill me-1"></i>Peticiones</a></li>

            <?php
                        } elseif ($pagina_actual == 'peticiones.php') {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php"><i class="bi bi-house-door-fill me-1"></i>Principal</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/perfil.php"><i class="bi bi-person-circle me-1"></i>Mi Perfil</a></li>

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