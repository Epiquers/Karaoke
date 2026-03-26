<?php
session_start();
include '../includes/conexion.php';
include 'seguridad_usuario.php';

$idUsuario = $_SESSION['idUsuario'];

// PRIMERA canción de la cola
$consulta_primeraCancion = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
$result_primera = mysqli_query($conn, $consulta_primeraCancion);
$primera = mysqli_fetch_assoc($result_primera);

$cancion_actual = null;
$id_colaPrimera = null;

if ($primera) {
    $id_colaPrimera = $primera['id'];
    $idCancionActual = $primera['id_cancion'];
    $result_cancion = mysqli_query($conn, "SELECT * FROM canciones WHERE id = '$idCancionActual'");
    $cancion_actual = mysqli_fetch_assoc($result_cancion);
}

// Borrar la primera canción de la cola al pulsar siguiente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['siguiente'])) {
    mysqli_query($conn, "DELETE FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1");
    header('Location:canciones.php');
    exit();
}

$result_cola = mysqli_query($conn, "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC");
$resultado_canciones = mysqli_query($conn, "SELECT * FROM canciones");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantabile - Karaoke Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* --- ESTILOS MOTOR KARAOKE --- */
        #pantalla-karaoke {
            background: radial-gradient(circle, #222 0%, #000 100%);
            min-height: 450px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid #444;
            border-radius: 15px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
        }

        .linea-karaoke {
            min-height: 5.5rem;
            margin: 10px 0;
            text-align: center;
            width: 100%;
            transition: opacity 0.4s ease;
        }

        #linea-actual {
            font-size: 3.5rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #linea-siguiente {
            font-size: 1.8rem;
            font-weight: 600;
            opacity: 0.25;
            color: #aaa;
            text-transform: uppercase;
            filter: blur(1px);
        }

        /* --- EFECTO RELLENO --- */
        .palabra {
            display: inline-block;
            margin: 0 10px;
            font-kerning: none;
            position: relative;
            background-image: linear-gradient(to right, #fff var(--progress, 0%), rgba(255, 255, 255, 0.2) var(--progress, 0%));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: transform 0.2s ease;
        }

        .palabra.activa {
            transform: scale(1.12);
            filter: drop-shadow(0 0 12px rgba(255, 193, 7, 0.8));
        }

        .palabra.pasada {
            background-image: linear-gradient(to right, #666 100%, #666 100%);
            opacity: 0.5;
            transform: scale(0.95);
        }

        #numero-cuenta {
            font-size: 8rem;
            text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .animate-intro {
            animation: zoomIn 0.8s ease-out;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="text-light page-canciones">

    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 border-end border-secondary bg-dark px-0">
                <ul class="nav nav-pills nav-fill border-bottom border-secondary">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-biblioteca">
                            <i class="bi bi-music-note-list me-2"></i>BIBLIOTECA
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-cola">
                            <i class="bi bi-list-ol me-2"></i>COLA
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-biblioteca">
                        <div class="search-container py-2 px-2 ms-3 me-3 mb-0 mt-3">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control form-control-dark" placeholder="Buscar canción o artista...">
                            </div>
                        </div>
                        <div class="sidebar-scroll p-3" id="listaCanciones">
                            <?php while ($row = mysqli_fetch_assoc($resultado_canciones)) { ?>
                                <div class="item-cancion d-flex justify-content-between align-items-center p-3 card-dark mb-2"
                                    data-titulo="<?= strtolower($row['titulo']) ?>"
                                    data-artista="<?= strtolower($row['artista']) ?>"
                                    data-estilo="<?= strtolower($row['estilo']) ?>">
                                    <div class="overflow-hidden">
                                        <h6 class="mb-1 fw-bold text-warning text-truncate small"><?= $row['titulo'] ?></h6>
                                        <p class="mb-0 text-secondary smaller text-truncate"><?= $row['artista'] ?></p>
                                    </div>
                                    <form action="cancion_añadir.php" method="POST" class="ms-2 d-flex flex-column gap-1">
                                        <input type="text" name="cantante" class="form-control form-control-sm bg-dark border-secondary text-white"
                                            placeholder="¿Quién?" style="font-size: 0.7rem; max-width: 80px;" required>
                                        <input type="hidden" name="idCancion" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-main btn-sm w-100" style="font-size: 0.7rem;">Añadir</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-cola">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary bg-dark">
                            <h6 class="text-secondary small mb-0">SIGUIENTES TEMAS</h6>
                            <?php if (mysqli_num_rows($result_cola) > 0) { ?>
                                <form action="vaciar_cola.php" method="POST" onsubmit="return confirm('¿Vaciar toda la lista?');">
                                    <input type="hidden" name="idUsuario" value="<?= $idUsuario ?>">
                                    <button type="submit" name="limpiar_cola" class="btn btn-outline-danger btn-sm border-0 py-0" style="font-size: 0.7rem;">
                                        <i class="bi bi-trash3-fill me-1"></i>VACIAR
                                    </button>
                                </form>
                            <?php } ?>
                        </div>

                        <div class="sidebar-scroll p-3">
                            <?php if (mysqli_num_rows($result_cola) > 0) {
                                mysqli_data_seek($result_cola, 0);
                                while ($row = mysqli_fetch_assoc($result_cola)) {
                                    $idC = $row['id_cancion'];
                                    $c = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM canciones WHERE id = '$idC'"));
                                    $esSonando = ($row['id'] == $id_colaPrimera);
                            ?>
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 <?= $esSonando ? 'border-start border-danger border-3 bg-danger bg-opacity-10' : 'card-dark' ?>">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 fw-bold <?= $esSonando ? 'text-white' : 'text-warning' ?> text-truncate small"><?= $c['titulo'] ?></h6>
                                            <span class="badge bg-secondary opacity-75 mt-1" style="font-size: 0.7rem;">
                                                <i class="bi bi-person-fill me-1"></i><?= $row['cantante'] ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <form action="cancion_mover.php" method="POST" class="d-flex gap-1">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <button type="submit" name="mover" value="subir" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-up-fill"></i></button>
                                                <button type="submit" name="mover" value="bajar" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-down-fill"></i></button>
                                            </form>
                                            <form action="cancion_quitar.php" method="POST">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                <?php }
                            } else { ?>
                                <p class="text-center text-muted py-5">Cola vacía</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-8 col-xl-9 bg-black py-4">
                <?php if ($cancion_actual) { ?>
                    <div class="container-fluid text-center">
                        <h2 class="h4 fw-bold text-warning mb-1"><?= $cancion_actual['titulo'] ?></h2>
                        <p class="text-secondary mb-4"><?= $cancion_actual['artista'] ?></p>

                        <div id="pantalla-karaoke" class="rounded shadow-lg border border-secondary mb-4 mx-auto" style="max-width: 1000px;">
                            <div id="karaoke-intro" class="d-none position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center" style="z-index: 100; background: rgba(0,0,0,0.9); top:0; left:0; border-radius: 15px;">
                                <div class="animate-intro">
                                    <h4 class="text-secondary mb-0 text-uppercase">Siguiente cantante:</h4>
                                    <h1 id="intro-cantante" class="display-3 fw-bold text-warning mb-4">NOMBRE</h1>
                                    <hr class="w-50 mx-auto border-secondary">
                                    <h2 id="intro-cancion" class="h3 text-white">Título de la canción</h2>
                                    <p id="intro-artista" class="text-secondary italic">Artista</p>

                                    <div id="cuenta-atras-container" class="mt-4">
                                        <div id="numero-cuenta" class="display-1 fw-bold text-white">10</div>
                                        <div id="mensaje-preparate" class="h2 text-danger fw-bold d-none">¡PREPÁRATE!</div>
                                    </div>
                                </div>
                            </div>
                            <div id="linea-actual" class="linea-karaoke"></div>
                            <div id="linea-siguiente" class="linea-karaoke"></div>
                        </div>

                        <div class="mx-auto mb-4" style="max-width: 900px;">
                            <audio id="videoKaraoke" controls class="w-100">
                                <source src="<?= "../" . $cancion_actual['cancion'] ?>" type="audio/mpeg">
                            </audio>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <form action="" method="POST">
                                <button name="siguiente" class="btn btn-main px-4"><i class="bi bi-skip-forward-fill me-2"></i>Siguiente</button>
                            </form>
                            <button id="btnFullscreen" class="btn btn-outline-light"><i class="bi bi-fullscreen"></i></button>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="d-flex flex-column justify-content-center align-items-center text-center w-100 h-100 py-5">
                        <i class="bi bi-mic-fill display-1 text-secondary opacity-25"></i>
                        <h3 class="text-muted mt-4">No hay canciones en cola</h3>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // --- LÓGICA DE INTRO Y CUENTA ATRÁS ---
        <?php if ($cancion_actual) :
            // Obtenemos el nombre del cantante de la base de datos para el overlay
            mysqli_data_seek($result_cola, 0);
            $datosCola = mysqli_fetch_assoc($result_cola);
            $nombreCantante = $datosCola ? $datosCola['cantante'] : "Invitado";
        ?>
                (function() {
                    const audio = document.getElementById('videoKaraoke');
                    const intro = document.getElementById('karaoke-intro');
                    const numCuenta = document.getElementById('numero-cuenta');
                    const msgPrep = document.getElementById('mensaje-preparate');

                    // Datos para el overlay
                    document.getElementById('intro-cantante').innerText = "<?= addslashes($nombreCantante) ?>";
                    document.getElementById('intro-cancion').innerText = "<?= addslashes($cancion_actual['titulo']) ?>";
                    document.getElementById('intro-artista').innerText = "<?= addslashes($cancion_actual['artista']) ?>";

                    let introMostrada = false;

                    audio.onplay = function() {
                        if (!introMostrada && audio.currentTime < 1) {
                            audio.pause(); // Pausamos el audio para la cuenta atrás
                            intro.classList.remove('d-none');
                            introMostrada = true;

                            let cuenta = 10;
                            const intervalo = setInterval(() => {
                                cuenta--;
                                numCuenta.innerText = cuenta;

                                // Efecto "Prepárate" en los últimos 3 segundos
                                if (cuenta <= 3 && cuenta > 0) {
                                    numCuenta.classList.add('text-danger');
                                    msgPrep.classList.remove('d-none');
                                }

                                if (cuenta <= 0) {
                                    clearInterval(intervalo);
                                    intro.style.opacity = '0';
                                    setTimeout(() => {
                                        intro.classList.add('d-none');
                                        audio.play(); // Arranca la música
                                    }, 500);
                                }
                            }, 1000);
                        }
                    };

                    // --- MOTOR DE KARAOKE (EL QUE YA TENÍAS) ---
                    const displayActual = document.getElementById('linea-actual');
                    const displaySiguiente = document.getElementById('linea-siguiente');
                    let letras = [];

                    fetch("../<?= $cancion_actual['letra'] ?>").then(r => r.text()).then(data => {
                        letras = parsearLRC(data);
                        requestAnimationFrame(engine);
                    });

                    function parsearLRC(lrc) {
                        const res = [];
                        const lines = lrc.split('\n');
                        const lineRegex = /\[(\d+):(\d+\.\d+)\](.*)/;
                        const wordRegex = /<(\d+):(\d+\.\d+)>/g;

                        lines.forEach((line, index) => {
                            const mL = line.match(lineRegex);
                            if (mL) {
                                const timeL = parseInt(mL[1]) * 60 + parseFloat(mL[2]);
                                const content = mL[3].trim();
                                const textParts = content.split(/<\d+:\d+\.\d+>/);
                                const innerTimes = [];
                                let mWT;
                                while ((mWT = wordRegex.exec(content)) !== null) {
                                    innerTimes.push(parseInt(mWT[1]) * 60 + parseFloat(mWT[2]));
                                }
                                let words = [];
                                if (textParts[0].trim() !== "") words.push({
                                    start: timeL,
                                    text: textParts[0].trim()
                                });
                                for (let i = 0; i < innerTimes.length; i++) {
                                    if (textParts[i + 1] && textParts[i + 1].trim() !== "") {
                                        words.push({
                                            start: innerTimes[i],
                                            text: textParts[i + 1].trim()
                                        });
                                    }
                                }
                                for (let i = 0; i < words.length; i++) {
                                    if (words[i + 1]) {
                                        words[i].end = words[i + 1].start;
                                    } else {
                                        const nextLine = lines[index + 1];
                                        const mNextL = nextLine ? nextLine.match(lineRegex) : null;
                                        if (mNextL) {
                                            const nLT = parseInt(mNextL[1]) * 60 + parseFloat(mNextL[2]);
                                            words[i].end = Math.min(words[i].start + 1.5, nLT);
                                        } else {
                                            words[i].end = words[i].start + 1.5;
                                        }
                                    }
                                }
                                if (words.length > 0) res.push({
                                    time: timeL,
                                    words
                                });
                            }
                        });
                        return res;
                    }

                    function engine() {
                        const now = audio.currentTime;
                        const idx = letras.findIndex((l, i) => now >= l.time && (!letras[i + 1] || now < letras[i + 1].time));
                        if (idx !== -1) {
                            const linea = letras[idx];
                            if (displayActual.dataset.index != idx) {
                                displayActual.innerHTML = '';
                                linea.words.forEach((w, i) => {
                                    const s = document.createElement('span');
                                    s.className = 'palabra';
                                    s.innerText = w.text + ' ';
                                    s.id = `w-${i}`;
                                    displayActual.appendChild(s);
                                });
                                displayActual.dataset.index = idx;
                                const sig = letras[idx + 1];
                                displaySiguiente.innerText = sig ? sig.words.map(w => w.text).join(' ') : '';
                            }
                            linea.words.forEach((w, i) => {
                                const el = document.getElementById(`w-${i}`);
                                if (!el) return;
                                if (now >= w.end) {
                                    el.style.setProperty('--progress', '100%');
                                    el.classList.add('pasada');
                                    el.classList.remove('activa');
                                } else if (now >= w.start && now < w.end) {
                                    const prog = ((now - w.start) / (w.end - w.start)) * 100;
                                    el.style.setProperty('--progress', `${prog}%`);
                                    el.classList.add('activa');
                                    el.classList.remove('pasada');
                                } else {
                                    el.style.setProperty('--progress', '0%');
                                    el.classList.remove('activa', 'pasada');
                                }
                            });
                        }
                        requestAnimationFrame(engine);
                    }
                })();
        <?php endif; ?>

        // Buscador y pestañas (Igual que antes)
        document.getElementById('inputBusqueda')?.addEventListener('input', function() {
            let texto = this.value.toLowerCase().trim();
            document.querySelectorAll('.item-cancion').forEach(item => {
                let match = item.dataset.titulo.includes(texto) || item.dataset.artista.includes(texto) || item.dataset.estilo.includes(texto);
                item.classList.toggle('d-none', !match);
                item.classList.toggle('d-flex', match);
            });
        });

        if (window.location.search.includes("cola=1")) {
            new bootstrap.Tab(document.querySelector('[data-bs-target="#tab-cola"]')).show();
        }
    </script>
</body>

</html>