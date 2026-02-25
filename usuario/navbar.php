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
                    <a class="nav-link btn btn-outline-light btn-sm px-3 me-2" href="perfil.php">
                        <i class="bi bi-gear-fill me-1"></i> Perfil
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="btn btn-main btn-sm text-white px-3" href="../logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>