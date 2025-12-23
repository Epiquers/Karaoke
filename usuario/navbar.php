<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="canciones.php">
            🎤 Kantabile
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUsuario">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarUsuario">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Hola, <!--<?= htmlspecialchars($_SESSION["usuario"]) ?>--></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="canciones.php">Canciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cola.php">Cola</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="karaoke.php">Karaoke</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
