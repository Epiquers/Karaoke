<?php
$servidor="localhost";
$user="root";
$clave="";
$basededatos="karaoke";
//Establecimiento de la conexión al servidor localhost, 
//con el usuario root y sin clave
$conn= mysqli_connect($servidor,$user,$clave);
//Seleccionamos la base de datos empresa
mysqli_select_db($conn,$basededatos);

// Para evitar problemas con tildes y eñes
mysqli_set_charset($conn, "utf8mb4");
//Imprimimos si hay algún error
echo mysqli_error($conn);
?>