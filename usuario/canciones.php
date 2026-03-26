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
    <title>Kantabile - Karaoke Pro Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        /* --- TUS ESTILOS ORIGINALES --- */
        #pantalla-karaoke {
            background: radial-gradient(circle, #222 0%, #000 100%);
            min-height: 500px;
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
        .linea-karaoke { min-height: 6rem; margin: 10px 0; text-align: center; width: 100%; transition: opacity 0.4s ease; z-index: 10; }
        #linea-actual { font-size: 3.5rem; font-weight: 900; text-transform: uppercase; }
        #linea-siguiente { font-size: 1.8rem; opacity: 0.3; color: #ffb973; text-transform: uppercase; filter: blur(1px); }
        .palabra { 
            display: inline-block; margin: 0 20px;
            background-image: linear-gradient(to right, #b3ff01 var(--progress, 0%), rgba(255, 255, 255, 0.2) var(--progress, 0%));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            transition: transform 0.2s ease;
        }
        .palabra.activa { transform: scale(1.18); filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.5)); }
        .palabra.pasada { background-image: linear-gradient(to right, #666 100%, #666 100%); opacity: 0.5; }
        #karaoke-intro { transition: opacity 0.5s ease; background: rgba(0, 0, 0, 0.95) !important; }
        #numero-cuenta { font-size: 9rem; text-shadow: 0 0 30px rgba(255, 193, 7, 0.4); }
        .animate-intro { animation: zoomIn 0.6s ease-out; }
        @keyframes zoomIn { from { transform: scale(0.5); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>

<body class="text-light page-canciones">
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-4 col-xl-3 border-end border-secondary bg-dark px-0">
                <ul class="nav nav-pills nav-fill border-bottom border-secondary">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-biblioteca"><i class="bi bi-music-note-list me-2"></i>BIBLIOTECA</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-cola"><i class="bi bi-list-ol me-2"></i>COLA</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-biblioteca">
                        <div class="p-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control form-control-dark" placeholder="Buscar canción o artista...">
                            </div>
                            <div class="sidebar-scroll" id="listaCanciones">
                                <?php while ($row = mysqli_fetch_assoc($resultado_canciones)) { ?>
                                    <div class="item-cancion d-flex justify-content-between align-items-center p-3 card-dark mb-2" data-titulo="<?= strtolower($row['titulo']) ?>" data-artista="<?= strtolower($row['artista']) ?>">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 fw-bold text-warning text-truncate small"><?= $row['titulo'] ?></h6>
                                            <p class="mb-0 text-secondary smaller text-truncate"><?= $row['artista'] ?></p>
                                        </div>
                                        <form action="cancion_añadir.php" method="POST" class="ms-2 d-flex flex-column gap-1">
                                            <input type="text" name="cantante" class="form-control form-control-sm bg-dark border-secondary text-white" placeholder="¿Quién?" style="font-size: 0.7rem; max-width: 80px;" required>
                                            <input type="hidden" name="idCancion" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-main btn-sm w-100" style="font-size: 0.7rem;">Añadir</button>
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-cola">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary bg-dark">
                            <h6 class="text-secondary small mb-0">SIGUIENTES TEMAS</h6>
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
                                            <span class="badge bg-secondary opacity-75 mt-1" style="font-size: 0.7rem;"><?= $row['cantante'] ?></span>
                                        </div>
                                    </div>
                            <?php }
                            } else { echo '<p class="text-center text-muted py-5">Cola vacía</p>'; } ?>
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
                            <div id="karaoke-intro" class="d-none position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center" style="z-index: 100; background: #000; top:0; left:0; border-radius: 15px;">
                                <div class="animate-intro">
                                    <h4 class="text-secondary mb-0 text-uppercase small">Siguiente cantante:</h4>
                                    <?php mysqli_data_seek($result_cola, 0); $p = mysqli_fetch_assoc($result_cola); ?>
                                    <h1 class="display-3 fw-bold text-warning mb-4"><?= $p['cantante'] ?></h1>
                                    <div id="numero-cuenta" class="fw-bold text-white">10</div>
                                    <div id="mensaje-preparate" class="h2 text-danger fw-bold d-none">¡PREPÁRATE!</div>
                                </div>
                            </div>

                            <div id="linea-actual" class="linea-karaoke"></div>
                            <div id="linea-siguiente" class="linea-karaoke"></div>
                        </div>

                        <div class="mx-auto mb-4" style="max-width: 900px;">
                            <audio id="videoKaraoke" controls class="w-100 shadow mb-2">
                                <source src="<?= "../" . $cancion_actual['voz'] ?>" type="audio/mpeg">
                            </audio>

                            <audio id="pistaInstrumental">
                                <source src="<?= "../" . $cancion_actual['instrumental'] ?>" type="audio/mpeg">
                            </audio>

                            <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
                                <button id="btnGuia" class="btn btn-warning fw-bold"><i class="bi bi-mic-fill"></i> VOZ GUÍA: ON</button>
                                <form action="" method="POST">
                                    <button name="siguiente" class="btn btn-main px-4"><i class="bi bi-skip-forward-fill me-2"></i>Siguiente</button>
                                </form>
                                <button id="btnLanzarEscenario" class="btn btn-outline-info"><i class="bi bi-window-plus"></i> LANZAR ESCENARIO</button>
                                <button id="btnFullscreen" class="btn btn-outline-light"><i class="bi bi-fullscreen"></i></button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($cancion_actual) : ?>
        (function() {
            const audioGuia = document.getElementById('videoKaraoke');
            const audioInst = document.getElementById('pistaInstrumental');
            const btnGuia = document.getElementById('btnGuia');
            const intro = document.getElementById('karaoke-intro');
            const numCuenta = document.getElementById('numero-cuenta');
            const msgPrep = document.getElementById('mensaje-preparate');
            const displayActual = document.getElementById('linea-actual');
            const displaySiguiente = document.getElementById('linea-siguiente');
            
            let letras = [];
            let introMostrada = false;
            let ventanaEscenario = null;

            // 1. CARGA DE LETRAS
            fetch("../<?= $cancion_actual['letra'] ?>")
                .then(r => r.text())
                .then(data => {
                    letras = parsearLRC(data);
                    requestAnimationFrame(engine);
                });

            // 2. CORRECCIÓN DE DESFASE (CRÍTICO)
            audioGuia.addEventListener('timeupdate', () => {
                if (Math.abs(audioGuia.currentTime - audioInst.currentTime) > 0.05) {
                    audioInst.currentTime = audioGuia.currentTime;
                }
            });
            audioGuia.onpause = () => audioInst.pause();
            audioGuia.onseeking = () => audioInst.currentTime = audioGuia.currentTime;

            // 3. CUENTA ATRÁS Y PLAY SINCRONIZADO
            audioGuia.onplay = () => {
                if (!introMostrada && audioGuia.currentTime < 1) {
                    audioGuia.pause();
                    audioInst.pause();
                    intro.classList.remove('d-none');
                    introMostrada = true;
                    let cuenta = 10;
                    let timer = setInterval(() => {
                        cuenta--;
                        numCuenta.innerText = cuenta;
                        if (cuenta <= 3 && cuenta > 0) {
                            numCuenta.classList.add('text-danger');
                            msgPrep.classList.remove('d-none');
                        }
                        if (cuenta <= 0) {
                            clearInterval(timer);
                            intro.classList.add('d-none');
                            audioGuia.currentTime = 0;
                            audioInst.currentTime = 0;
                            audioGuia.play();
                            audioInst.play();
                        }
                    }, 1000);
                } else {
                    audioInst.play();
                }
            };

            // 4. LANZAR ESCENARIO (VENTANA EXTERNA)
            document.getElementById('btnLanzarEscenario').onclick = () => {
                ventanaEscenario = window.open('', 'Escenario', 'width=1200,height=800');
                ventanaEscenario.document.write(`
                    <html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { background: black; color: white; overflow: hidden; font-family: sans-serif; }
                        #esc-cont { height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
                        #intro-esc { position: absolute; top:0; left:0; width:100%; height:100%; background:black; display:none; flex-direction:column; justify-content:center; align-items:center; z-index:100; }
                        .linea-karaoke { min-height: 8rem; width: 100%; }
                        #linea-actual-esc { font-size: 5rem; font-weight: 900; text-transform: uppercase; }
                        #linea-siguiente-esc { font-size: 2.5rem; opacity: 0.4; color: #ffb973; }
                        .palabra { display: inline-block; margin: 0 15px; background-image: linear-gradient(to right, #b3ff01 var(--progress, 0%), rgba(255,255,255,0.2) var(--progress, 0%)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
                        .palabra.activa { transform: scale(1.1); }
                    </style></head>
                    <body><div id="esc-cont"><div id="intro-esc"><h1 id="cant-esc" style="font-size:7rem; color:#ffc107"></h1><h2 id="cuenta-esc" style="font-size:10rem"></h2></div><div id="linea-actual-esc" class="linea-karaoke"></div><div id="linea-siguiente-esc" class="linea-karaoke"></div></div></body></html>
                `);
            };

            // 5. MOTOR DE RENDERIZADO (SINCRONIZA AMBAS VENTANAS)
            function engine() {
                const now = audioGuia.currentTime;
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
                        
                        // Sincronizar HTML con ventana externa
                        if (ventanaEscenario && !ventanaEscenario.closed) {
                            ventanaEscenario.document.getElementById('linea-actual-esc').innerHTML = displayActual.innerHTML;
                            ventanaEscenario.document.getElementById('linea-siguiente-esc').innerText = displaySiguiente.innerText;
                        }
                    }

                    linea.words.forEach((w, i) => {
                        const el = document.getElementById(`w-${i}`);
                        if (el) {
                            const p = now >= w.end ? 100 : now >= w.start ? ((now - w.start) / (w.end - w.start)) * 100 : 0;
                            el.style.setProperty('--progress', `${p}%`);
                            el.classList.toggle('activa', now >= w.start && now < w.end);
                            el.classList.toggle('pasada', now >= w.end);
                            
                            // Sincronizar progreso en ventana externa
                            if (ventanaEscenario && !ventanaEscenario.closed) {
                                const elExt = ventanaEscenario.document.querySelectorAll('.palabra')[i];
                                if (elExt) {
                                    elExt.style.setProperty('--progress', `${p}%`);
                                    elExt.classList.toggle('activa', now >= w.start && now < w.end);
                                }
                            }
                        }
                    });
                }

                // Sincronizar Intro en ventana externa
                if (ventanaEscenario && !ventanaEscenario.closed) {
                    const introEsc = ventanaEscenario.document.getElementById('intro-esc');
                    if (!intro.classList.contains('d-none')) {
                        introEsc.style.display = 'flex';
                        ventanaEscenario.document.getElementById('cant-esc').innerText = "<?= $p['cantante'] ?>";
                        ventanaEscenario.document.getElementById('cuenta-esc').innerText = numCuenta.innerText;
                    } else { introEsc.style.display = 'none'; }
                }

                requestAnimationFrame(engine);
            }

            // --- TUS FUNCIONES AUXILIARES ---
            btnGuia.onclick = () => {
                audioGuia.muted = !audioGuia.muted;
                btnGuia.innerHTML = audioGuia.muted ? '<i class="bi bi-mic-mute"></i> VOZ GUÍA: OFF' : '<i class="bi bi-mic-fill"></i> VOZ GUÍA: ON';
                btnGuia.className = audioGuia.muted ? 'btn btn-outline-secondary fw-bold' : 'btn btn-warning fw-bold';
            };

            function parsearLRC(lrc) {
                const res = [];
                const lines = lrc.split('\n');
                const lineRegex = /\[(\d+):(\d+\.\d+)\](.*)/;
                const wordRegex = /<(\d+):(\d+\.\d+)>/g;
                lines.forEach((line) => {
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
                        if (textParts[0].trim() !== "") words.push({ start: timeL, text: textParts[0].trim() });
                        for (let i = 0; i < innerTimes.length; i++) {
                            if (textParts[i + 1] && textParts[i + 1].trim() !== "") {
                                words.push({ start: innerTimes[i], text: textParts[i + 1].trim() });
                            }
                        }
                        for (let i = 0; i < words.length; i++) {
                            words[i].end = words[i+1] ? words[i+1].start : words[i].start + 1.5;
                        }
                        if (words.length > 0) res.push({ time: timeL, words });
                    }
                });
                return res;
            }
        })();
        <?php endif; ?>

        // BUSCADOR
        document.getElementById('inputBusqueda')?.addEventListener('input', function() {
            let t = this.value.toLowerCase().trim();
            document.querySelectorAll('.item-cancion').forEach(i => {
                let match = i.dataset.titulo.includes(t) || i.dataset.artista.includes(t);
                i.classList.toggle('d-none', !match); i.classList.toggle('d-flex', match);
            });
        });

        // FULLSCREEN ORIGINAL
        document.getElementById('btnFullscreen')?.addEventListener('click', () => {
            const p = document.getElementById('pantalla-karaoke');
            if (p.requestFullscreen) p.requestFullscreen();
            else if (p.webkitRequestFullscreen) p.webkitRequestFullscreen();
        });
    </script>
</body>
</html>