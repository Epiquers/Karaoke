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
                    <a class="nav-link" href="../usuario/canciones.php">Modo Usuario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>