<?php
header('Content-Type: application/json');
$conexion = new mysqli("localhost", "root", "", "mtc");
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

$action = $_GET['action'] ?? '';

if ($action === 'buscar') {
    $texto = $conexion->real_escape_string($_GET['texto'] ?? '');
    $sql = "SELECT a.Numero, a.Fecha, a.Hora, a.Descripcion, a.Foto_de_rotura, 
                   u.Nombre_Completo, e.Nombre AS Estado, c.Nombre AS Ciudad, ub.Nombre_Calle, ub.Numero_dePuerta
            FROM alerta a
            JOIN usuario u ON a.CI_deUsuario = u.CI
            JOIN estado e ON a.ID_Estado = e.ID_Estado
            JOIN ciudad c ON a.Codigo_deCiudad = c.Codigo
            JOIN ubicacion ub ON a.Numero_deUbi = ub.Numero AND a.Codigo_deCiudad = ub.Codigo_deCiudad
            WHERE a.Descripcion LIKE '%$texto%'
            ORDER BY a.Fecha DESC, a.Hora DESC";
    $resultado = $conexion->query($sql);
    $alertas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $alertas[] = $fila;
    }
    echo json_encode($alertas);
}

if ($action === 'subir' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $tipo = $conexion->real_escape_string($_POST['tipo']);
    $ci_usuario = intval($_POST['ci_usuario']);
    $codigo_ciudad = intval($_POST['codigo_ciudad']);
    $numero_ubi = intval($_POST['numero_ubi']);
    
    $foto_rotura = '';
    if (isset($_FILES['foto_rotura']) && $_FILES['foto_rotura']['error'] == 0) {
        $nombre_archivo = time() . "_" . basename($_FILES['foto_rotura']['name']);
        move_uploaded_file($_FILES['foto_rotura']['tmp_name'], "uploads/".$nombre_archivo);
        $foto_rotura = $nombre_archivo;
    }

    $sql = "INSERT INTO alerta (Numero, Fecha, Hora, Foto_de_rotura, Foto_de_arreglo, Tipo, Descripcion, Numero_deUbi, CI_deUsuario, Codigo_deCiudad, ID_Estado)
            VALUES (NULL, CURDATE(), CURTIME(), '$foto_rotura', '', '$tipo', '$descripcion', $numero_ubi, $ci_usuario, $codigo_ciudad, 1)";
    if ($conexion->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => $conexion->error]);
    }
}

if ($action === 'ver') {
    $id = intval($_GET['id'] ?? 0);
    $sql = "SELECT * FROM alerta WHERE Numero = $id";
    $resultado = $conexion->query($sql);
    echo json_encode($resultado->fetch_assoc());
}

$conexion->close();
?>
