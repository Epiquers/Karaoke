<?php
// Comprobamos que el usuario ha iniciado sesión
if (!isset($_SESSION['rol'])) {
    header('Location: ../index.php');
    exit();
}
