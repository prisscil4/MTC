<?php
//subida de imagenes para alertas y perfil
$carpeta = 'uploads/';
$nombreArchivo = uniqid() . '_' . $_FILES['imagen']['name'];
$rutaArchivo = $carpeta . $nombreArchivo;

move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaArchivo);

$stmt = $conn->prepare("INSERT INTO alerta (foto, descripcion) VALUES (?, ?)");
$stmt->bind_param("ss", $rutaArchivo, $descripcion);
$stmt->execute();

?>
