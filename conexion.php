<?php
$host = "mysql.hostinger.com"; // ← usa el host exacto que te muestra Hostinger
$user = "u302434035_admin";       // ← tu usuario MySQL real
$pass = "MTCutu_2025";   // ← la que creaste en el paso anterior
$db   = "u302434035_MTC2";         // ← el nombre completo de tu base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
