<?php
// Comprobamos que el usuario ha iniciado sesión y es '2'
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != '2') {
    header('Location: ../index.php');
    exit();
}
