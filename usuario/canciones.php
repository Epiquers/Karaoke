<?php
session_start();
include '../includes/conexion.php';
include 'seguridad_usuario.php';

/** @var mysqli $conn */ // Para que el IDE reconozca $conn como una conexión mysqli y nos ofrezca autocompletado


$idUsuario = $_SESSION['idUsuario'];

// Añadir canción a la cola
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCancion'])) {
    $idCancion = $_POST['idCancion'];
    $idUsuario = $_SESSION['idUsuario'];
    $cantante = $_POST['cantante'];

    $consulta_añadir = "INSERT INTO cola (id_usuario, id_cancion, cantante) VALUES ('$idUsuario', '$idCancion', '$cantante')";
    $result = mysqli_query($conn, $consulta_añadir);

    echo mysqli_insert_id($conn); // devuelve el nuevo id al JS para construir el item en el DOM
    exit();
}

// Mover canción en la cola (subir o bajar)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mover'])) {
    $idUsuario = $_SESSION['idUsuario'];
    $idActual = $_POST['idCola'];

    // Recogemos el valor del botón "mover"
    $direccion = $_POST['mover'];

    $operador = ($direccion == 'subir') ? '<' : '>';
    $orden = ($direccion == 'subir') ? 'DESC' : 'ASC';

    $sql_vecino = "SELECT id FROM cola WHERE id_usuario = '$idUsuario' AND id $operador '$idActual' ORDER BY id $orden LIMIT 1";
    $res_vecino = mysqli_query($conn, $sql_vecino);

    if ($vecino = mysqli_fetch_assoc($res_vecino)) {
        $idVecino = $vecino['id'];

        // Intercambiamos los ids usando 0 como valor temporal para evitar conflicto de clave única
        mysqli_query($conn, "UPDATE cola SET id = 0 WHERE id = '$idActual'");
        mysqli_query($conn, "UPDATE cola SET id = '$idActual' WHERE id = '$idVecino'");
        mysqli_query($conn, "UPDATE cola SET id = '$idVecino' WHERE id = 0");
    }
    exit();
}

// Quitar canción de la cola
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCola'])) {
    $idCola = $_POST['idCola'];
    $idUsuario = $_SESSION['idUsuario'];

    $consulta_quitar = "DELETE FROM cola WHERE id='$idCola'";
    $result = mysqli_query($conn, $consulta_quitar);
    exit();
}

// PRIMERA canción de la cola
$consulta_primeraCancion = "SELECT * FROM cola WHERE id_usuario = '$idUsuario' ORDER BY id ASC LIMIT 1";
$result_primera = mysqli_query($conn, $consulta_primeraCancion);
$primera = mysqli_fetch_assoc($result_primera);

// Obtiene los datos de la canción actual si hay alguna en cola
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

// Cola completa del usuario (para la lista lateral) y todas las canciones (para la biblioteca)
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
    <link rel="stylesheet" href="../css/styles.css?v=4">
    <?php include '../includes/favicon.php'; ?>
</head>

<body class="text-light page-canciones">

    <!-- ===== BARRA DE NAVEGACIÓN ===== -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- ===== COLUMNA LATERAL: BIBLIOTECA Y COLA ===== -->
            <div class="col-12 col-lg-4 col-xl-3 border-end border-secondary bg-dark px-0">
                <!-- Pestañas de navegación entre Biblioteca y Cola -->
                <ul class="nav nav-pills nav-fill border-bottom border-secondary">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" data-bs-toggle="pill" data-bs-target="#tab-biblioteca"><i class="bi bi-music-note-list me-2"></i>BIBLIOTECA</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" data-bs-toggle="pill" data-bs-target="#tab-cola"><i class="bi bi-list-ol me-2"></i>COLA</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab: Biblioteca de canciones con buscador -->
                    <div class="tab-pane fade show active" id="tab-biblioteca">
                        <div class="p-3">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control form-control-dark" placeholder="Buscar canción, artista o estilo...">
                            </div>
                            <div class="sidebar-scroll" id="listaCanciones">
                                <?php while ($row = mysqli_fetch_assoc($resultado_canciones)) { ?>
                                    <div class="item-cancion d-flex justify-content-between align-items-center p-3 card-dark mb-2" data-titulo="<?= strtolower($row['titulo']) ?>" data-artista="<?= strtolower($row['artista']) ?>" data-estilo="<?= strtolower($row['estilo']) ?>">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 fw-bold text-warning text-truncate small"><?= $row['titulo'] ?> <span class="badge bg-secondary ms-2"><?= $row['estilo'] ?></span></h6>
                                            <p class="mb-0 text-secondary smaller text-truncate"><?= $row['artista'] ?> </p>
                                        </div>
                                        <form action="canciones.php" method="POST" class="ms-2 d-flex flex-column gap-1 form-añadir">
                                            <input type="text" name="cantante" class="form-control form-control-sm bg-dark border-secondary text-white" placeholder="¿Quién canta?" style="font-size: 0.7rem; max-width: 80px;" required>
                                            <input type="hidden" name="idCancion" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-main btn-sm w-100" style="font-size: 0.7rem;">Añadir</button>
                                        </form>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Cola del usuario con opciones de reordenar y vaciar -->
                    <div class="tab-pane fade" id="tab-cola">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary bg-dark">
                            <h6 class="text-secondary small mb-0">SIGUIENTES TEMAS</h6>
                            <?php if (mysqli_num_rows($result_cola) > 0) { ?>
                                <form action="vaciar_cola.php" method="POST" onsubmit="return confirm('¿Vaciar toda la lista?');">
                                    <input type="hidden" name="idUsuario" value="<?= $idUsuario ?>">
                                    <button type="submit" name="limpiar_cola" class="btn btn-outline-danger btn-sm border-0 py-0" style="font-size: 0.7rem;"><i class="bi bi-trash3-fill me-1"></i>VACIAR</button>
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
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 <?= $esSonando ? 'border-start border-danger border-3 bg-danger bg-opacity-10' : 'card-dark' ?> item-cola">
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 fw-bold <?= $esSonando ? 'text-white' : 'text-warning' ?> text-truncate small"><?= $c['titulo'] ?></h6>
                                            <span class="badge bg-secondary opacity-75 mt-1" style="font-size: 0.7rem;"><i class="bi bi-person-fill me-1"></i><?= $row['cantante'] ?></span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <form method="POST" class="form-mover">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="mover" value="subir">
                                                <button type="submit" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-up-fill"></i></button>
                                            </form>
                                            <form method="POST" class="form-mover">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="mover" value="bajar">
                                                <button type="submit" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-down-fill"></i></button>
                                            </form>
                                            <form method="POST" class="form-quitar">
                                                <input type="hidden" name="idCola" value="<?= $row['id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                            <?php }
                            } else {
                                echo '<p class="text-center text-muted py-5">Cola vacía</p>';
                            } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== COLUMNA PRINCIPAL: REPRODUCTOR DE KARAOKE ===== -->
            <div class="col-12 col-lg-8 col-xl-9 bg-black py-4">
                <?php if ($cancion_actual) { /* Muestra el reproductor si hay canción activa */ ?>
                    <div class="container-fluid text-center">
                        <h2 class="h4 fw-bold text-warning mb-1"><?= $cancion_actual['titulo'] ?></h2>
                        <p class="text-secondary mb-4"><?= $cancion_actual['artista'] ?></p>

                        <div id="pantalla-karaoke" class="rounded shadow-lg border border-secondary mb-4 mx-auto" style="max-width: 1000px;">
                            <div id="karaoke-intro" class="d-none position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center" style="z-index: 100; background: #000; top:0; left:0; border-radius: 15px;">
                                <div class="animate-intro">
                                    <h4 class="text-secondary mb-0 text-uppercase small">Siguiente cantante:</h4>
                                    <?php mysqli_data_seek($result_cola, 0);
                                    $p = mysqli_fetch_assoc($result_cola); ?>
                                    <h1 class="display-3 fw-bold text-warning mb-4"><?= $p['cantante'] ?></h1>
                                    <hr class="w-50 mx-auto border-secondary mb-4">
                                    <h2 class="h3 text-white"><?= $cancion_actual['titulo'] ?></h2>
                                    <p class="text-secondary italic"><?= $cancion_actual['artista'] ?></p>
                                    <div class="mt-4">
                                        <div id="numero-cuenta" class="fw-bold text-white">5</div>
                                        <div id="mensaje-preparate" class="h2 text-danger fw-bold d-none">¡PREPÁRATE!</div>
                                    </div>
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

                            <form id="form-siguiente" action="" method="POST" style="display:none">
                                <input type="hidden" name="siguiente" value="1">
                            </form>
                            <div class="controles-player d-flex justify-content-center align-items-center gap-3 mt-3">
                                <button id="btnGuia" class="btn btn-player-voz activo">
                                    <i class="bi bi-mic-fill"></i> VOZ: ON
                                </button>
                                <button type="button" class="btn btn-player-siguiente px-4" onclick="document.getElementById('form-siguiente').submit()"><i class="bi bi-skip-forward-fill me-2"></i>Siguiente</button>
                                <button id="btnFullscreen" class="btn btn-player-sec"><i class="bi bi-fullscreen"></i></button>
                                <button id="btn-abrir-escenario" class="btn btn-player-sec d-none d-md-inline-flex"><i class="bi bi-tv me-2"></i>Escenario</button>
                            </div>
                        </div>
                    </div>
                <?php } else { /* Cola vacía: muestra pantalla de espera */ ?>
                    <div class="d-flex flex-column justify-content-center align-items-center text-center h-100 py-5">
                        <!-- <i class="bi bi-mic-fill display-1 text-secondary opacity-25"></i> -->
                        <img src="../img/logosinfondosolo.png" alt="Logo Kantabile" class="mb-3" style="width: 200px;">
                        <h3 class="text-secondary mt-4">No hay canciones en cola</h3>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- ===== SCRIPTS: Bootstrap y lógica del karaoke ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($cancion_actual) : ?>
                // Bloque principal del reproductor de karaoke, se ejecuta solo si hay canción en cola
                (function() {
                    // Referencias a los elementos del DOM del reproductor
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

                    // --- FUNCIÓN LIMPIA PARA PREPARAR EL ESCENARIO ---
                    function prepararVentanaEscenario() {
                        if (!ventanaEscenario || ventanaEscenario.closed) return;
                        
                        const doc = ventanaEscenario.document;
                        // Inyectamos estructura base sin doc.write
                        doc.body.innerHTML = `
                            <div id="linea-actual-esc" class="linea-karaoke">ESPERANDO CANCIÓN...</div>
                            <div id="linea-siguiente-esc" class="linea-siguiente-esc"></div>
                        `;
                        doc.body.style.cssText = " background: radial-gradient(circle, #212121 25%, #000 100%); color: white; margin: 0; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh; overflow: hidden; font-family: 'Segoe UI', sans-serif;";
                        
                        const style = doc.createElement('style');
                        style.textContent = `
                            .linea-karaoke { min-height: 8rem; text-align: center; width: 90%; font-size: 5.5rem; font-weight: 900; text-transform: uppercase; }
                            .linea-siguiente-esc { font-size: 2.8rem; opacity: 0.4; color: #ffb973; text-transform: uppercase; margin-top: 50px; text-align: center; }
                            .palabra { display: inline-block; margin: 0 20px; background-image: linear-gradient(to right, #b3ff01 var(--progress, 0%), rgba(255, 255, 255, 0.2) var(--progress, 0%)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
                            .palabra.activa { transform: scale(1.1); filter: drop-shadow(0 0 15px rgba(179, 255, 1, 0.6)); }
                        `;
                        doc.head.appendChild(style);
                    }

                    // RECONEXIÓN AUTOMÁTICA AL CARGAR
                    window.addEventListener('load', () => {
                        ventanaEscenario = window.open("", "Escenario");
                        if (ventanaEscenario && !ventanaEscenario.closed) {
                            prepararVentanaEscenario();
                        }
                    });

                    // Carga el archivo .lrc de la canción actual y arranca el motor de karaoke
                    fetch("../<?= $cancion_actual['letra'] ?>")
                        .then(r => r.text())
                        .then(data => {
                            letras = parsearLRC(data);
                            requestAnimationFrame(engine);
                        });

                    // Botón para silenciar/activar la pista de voz guía
                    btnGuia.onclick = () => {
                        audioGuia.muted = !audioGuia.muted;
                        btnGuia.innerHTML = audioGuia.muted ? '<i class="bi bi-mic-mute"></i> VOZ: OFF' : '<i class="bi bi-mic-fill"></i> VOZ: ON';
                        btnGuia.className = audioGuia.muted ? 'btn btn-player-voz' : 'btn btn-player-voz activo';
                    };

                    // Al dar play: muestra la pantalla de intro con cuenta atrás antes de empezar
                    audioGuia.onplay = () => {
                        if (!introMostrada && audioGuia.currentTime < 1) {
                            audioGuia.pause();
                            audioInst.pause();
                            intro.classList.remove('d-none');
                            introMostrada = true;
                            let cuenta = 5;
                            let timer = setInterval(() => {
                                cuenta--;
                                numCuenta.innerText = cuenta;
                                if (cuenta <= 3 && cuenta > 0) {
                                    numCuenta.classList.add('text-danger');
                                    msgPrep.classList.remove('d-none');
                                }
                                if (cuenta <= 0) {
                                    clearInterval(timer);
                                    intro.style.opacity = '0';
                                    setTimeout(() => {
                                        intro.classList.add('d-none');
                                        audioGuia.currentTime = 0;
                                        audioInst.currentTime = 0;
                                        audioGuia.play();
                                        audioInst.play();
                                    }, 800);
                                }
                            }, 1000);
                        } else {
                            audioInst.currentTime = audioGuia.currentTime;
                            audioInst.play();
                        }
                    };

                    // Mantiene la pista instrumental sincronizada con la voz guía
                    audioGuia.onpause = () => audioInst.pause();
                    audioGuia.onseeking = () => audioInst.currentTime = audioGuia.currentTime;

                    // Abre o enfoca la ventana del escenario secundario
                    document.getElementById('btn-abrir-escenario').onclick = () => {
                        if (!ventanaEscenario || ventanaEscenario.closed) {
                            ventanaEscenario = window.open("", "Escenario", "width=1200,height=800");
                            setTimeout(prepararVentanaEscenario, 200);
                        } else {
                            ventanaEscenario.focus();
                        }
                    };

                    // Motor de karaoke: se ejecuta en cada frame, colorea las palabras según el tiempo del audio
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
                            }
                            linea.words.forEach((w, i) => {
                                const el = document.getElementById(`w-${i}`);
                                if (!el) return;
                                if (now >= w.end) {
                                    el.style.setProperty('--progress', '100%');
                                    el.classList.add('pasada');
                                    el.classList.remove('activa');
                                } else if (now >= w.start) {
                                    const p = ((now - w.start) / (w.end - w.start)) * 100;
                                    el.style.setProperty('--progress', `${p}%`);
                                    el.classList.add('activa');
                                }
                            });

                            // SINCRONIZACIÓN AUTOMÁTICA CON ESCENARIO
                            if (ventanaEscenario && !ventanaEscenario.closed) {
                                const escActual = ventanaEscenario.document.getElementById('linea-actual-esc');
                                const escSig = ventanaEscenario.document.getElementById('linea-siguiente-esc');
                                if (escActual) escActual.innerHTML = displayActual.innerHTML;
                                if (escSig) escSig.innerHTML = displaySiguiente.innerHTML;
                            }
                        }
                        requestAnimationFrame(engine);
                    }

                    // Convierte el texto del archivo .lrc en un array de líneas con tiempo y palabras
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
                                if (words.length > 0) res.push({ time: timeL, words });
                            }
                        });

                        // Cálculo dinámico del final de palabra basándose en el inicio de la siguiente frase
                        for (let i = 0; i < res.length; i++) {
                            let fraseActual = res[i].words;
                            let proximaFrase = res[i + 1];

                            for (let j = 0; j < fraseActual.length; j++) {
                                if (fraseActual[j + 1]) {
                                    fraseActual[j].end = fraseActual[j + 1].start;
                                } else {
                                    if (proximaFrase) {
                                        fraseActual[j].end = proximaFrase.time;
                                    } else {
                                        fraseActual[j].end = fraseActual[j].start + 2;
                                    }
                                }
                            }
                        }
                        return res;
                    }
                })();
        <?php endif; ?>

        // Filtro de búsqueda en tiempo real sobre la lista de canciones de la biblioteca
        document.getElementById('inputBusqueda')?.addEventListener('input', function() {
            let t = this.value.toLowerCase().trim();
            document.querySelectorAll('.item-cancion').forEach(i => {
                let match = i.dataset.titulo.includes(t) || i.dataset.artista.includes(t) || i.dataset.estilo.includes(t);
                i.classList.toggle('d-none', !match);
                i.classList.toggle('d-flex', match);
            });
        });

        // Pone la pantalla de karaoke en modo pantalla completa
        document.getElementById('btnFullscreen')?.addEventListener('click', () => {
            const p = document.getElementById('pantalla-karaoke');
            if (p.requestFullscreen) p.requestFullscreen();
            else if (p.webkitRequestFullscreen) p.webkitRequestFullscreen();
        });

        // Añadir canción a la cola sin recargar la página
        document.querySelectorAll('.form-añadir').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // evita que el formulario recargue la página
                var titulo = this.closest('.item-cancion').querySelector('h6').innerText; // título de la canción del item de biblioteca
                var cantante = this.querySelector('[name=cantante]').value; // nombre escrito en el input "¿Quién canta?"
                fetch('canciones.php', { method: 'POST', body: new FormData(this) }) // envía los datos al servidor (idCancion + cantante)
                    .then(r => r.text()) // recoge la respuesta: el nuevo id de la fila insertada en la tabla cola
                    .then(id => {
                        if (!document.getElementById('videoKaraoke')) { // si el reproductor no estaba visible (cola estaba vacía), recarga para mostrarlo
                            location.reload();
                            return;
                        }
                        var cola = document.querySelector('#tab-cola .sidebar-scroll'); // contenedor de la lista de la cola
                        cola.querySelector('.text-muted')?.remove(); // si la cola estaba vacía, quita el mensaje "Cola vacía"
                        var div = document.createElement('div'); // crea el bloque HTML del nuevo item
                        div.className = 'd-flex justify-content-between align-items-center p-3 mb-2 card-dark item-cola'; // mismas clases que los items existentes
                        div.innerHTML = `<div class="overflow-hidden"><h6 class="mb-1 fw-bold text-warning text-truncate small">${titulo}</h6><span class="badge bg-secondary opacity-75 mt-1" style="font-size:0.7rem"><i class="bi bi-person-fill me-1"></i>${cantante}</span></div><div class="d-flex align-items-center gap-2"><form method="POST" class="form-mover"><input type="hidden" name="idCola" value="${id.trim()}"><input type="hidden" name="mover" value="subir"><button type="submit" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-up-fill"></i></button></form><form method="POST" class="form-mover"><input type="hidden" name="idCola" value="${id.trim()}"><input type="hidden" name="mover" value="bajar"><button type="submit" class="btn btn-link text-secondary p-0"><i class="bi bi-caret-down-fill"></i></button></form><form method="POST" class="form-quitar"><input type="hidden" name="idCola" value="${id.trim()}"><button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button></form></div>`; // rellena el item con título, cantante y botones de subir/bajar/quitar
                        div.querySelectorAll('.form-quitar').forEach(f => f.addEventListener('submit', function(e2) { e2.preventDefault(); this.closest('.item-cola').remove(); fetch('canciones.php', { method: 'POST', body: new FormData(this) }); })); // adjunta el evento de borrar al botón quitar del nuevo item
                        div.querySelectorAll('.form-mover').forEach(f => f.addEventListener('submit', function(e2) { e2.preventDefault(); var item = this.closest('.item-cola'), dir = this.querySelector('[name=mover]').value, sib = dir === 'subir' ? item.previousElementSibling : item.nextElementSibling; if (sib) dir === 'subir' ? item.parentNode.insertBefore(item, sib) : item.parentNode.insertBefore(sib, item); fetch('canciones.php', { method: 'POST', body: new FormData(this) }); })); // adjunta el evento de mover a los botones subir/bajar del nuevo item
                        cola.appendChild(div); // inserta el nuevo item al final de la cola
                        this.querySelector('[name=cantante]').value = ''; // limpia el campo "¿Quién canta?"
                    });
            });
        });

        // Borrar canción de la cola y actualizar interfaz sin recargar
        document.querySelectorAll('.form-quitar').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // evita que el formulario recargue la página
                this.closest('.item-cola').remove(); // quita el bloque de la canción del DOM
                fetch('canciones.php', { method: 'POST', body: new FormData(this) }); // avisa al servidor para borrarla de la BD
            });
        });

        // Mover canción en la cola y actualizar interfaz sin recargar
        document.querySelectorAll('.form-mover').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // evita que el formulario recargue la página
                let item = this.closest('.item-cola'); // bloque de la canción que queremos mover
                let dir = this.querySelector('[name=mover]').value; // "subir" o "bajar", viene del input hidden
                if (dir === 'subir') {
                    let prev = item.previousElementSibling; // canción que está justo encima
                    if (prev) item.parentNode.insertBefore(item, prev); // la coloca antes de esa
                } else {
                    let next = item.nextElementSibling; // canción que está justo debajo
                    if (next) item.parentNode.insertBefore(next, item); // la coloca antes de la nuestra, efecto de bajar
                }
                fetch('canciones.php', { method: 'POST', body: new FormData(this) }); // avisa al servidor para actualizar el orden en la BD
            });
        });
    </script>
</body>

</html>