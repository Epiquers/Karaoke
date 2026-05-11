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
                    // Muestra enlaces de admin o de usuario según el rol de la sesión
                    if (isset($_SESSION['rol']) && $_SESSION['rol'] == 2) {
                    ?>
                <li class="nav-item"><a class="nav-link fw-bold text-success" href="../usuario/canciones.php"><i class="bi bi-music-note-list me-1"></i>Vista Reproductor</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../administrador/perfil_admin.php"><i class="bi bi-people-fill me-1"></i>Usuarios</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-danger" href="../administrador/peticiones_admin.php"><i class="bi bi-envelope-fill me-1"></i>Peticiones</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../administrador/canciones_admin.php"><i class="bi bi-gear-fill me-1"></i>Gestión</a></li>

            <?php
                    } else {
            ?>
                <li class="nav-item"><a class="nav-link fw-bold text-info" href="../usuario/canciones.php"><i class="bi bi-house-door-fill me-1"></i>Principal</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-warning" href="../usuario/peticiones.php"><i class="bi bi-chat-dots-fill me-1"></i>Peticiones</a></li>
                <li class="nav-item"><a class="nav-link fw-bold text-success" href="../usuario/perfil.php"><i class="bi bi-person-circle me-1"></i>Mi Perfil</a></li>

            <?php
                    }
            ?>

            <li class="nav-item">
                <a class="btn btn-danger btn-sm px-3 ms-3" href="../logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i> Salir
                </a>
            </li>
            </ul>
        </div>
    </div>
</nav>