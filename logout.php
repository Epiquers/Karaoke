<?php
session_start();
session_destroy(); // Destruye todos los datos de la sesión activa
header("Location: index.php"); // Redirige al login
exit;
?>
