<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top border-bottom border-secondary">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-warning" href="canciones.php">
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
                    <a class="nav-link" href="karaoke.php">Karaoke</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">  
                <li class="nav-item">
                    <a class="nav-link" href="../administrador/canciones_admin.php">Administrar</a>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white">Hola, <?= htmlspecialchars($_SESSION["nombre"]) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>