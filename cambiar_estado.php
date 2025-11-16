<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION["admin"])) {
    echo "Acceso denegado.";
    exit;
}

if (!isset($_GET["Numero"])) {
    echo "No se especificó alerta.";
    exit;
}

$id = $_GET["Numero"];
$sql = "SELECT estado FROM alerta WHERE Numero='$id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$nuevo_estado = ($row["Estado"] + 1) % 3;
$conn->query("UPDATE alerta SET Estado='$nuevo_estado' WHERE Numero='$id'");

header("Location: panel_alertas.php");
