<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>panel de alertas</title>
    
</head>
<body>
<p>El código fuente de esta aplicación está disponible bajo una 
    <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
    </a></p>

</body>
</html>
<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    echo "Acceso denegado.";
    exit;
}

$sql = "SELECT a.*, u.Nombre_Completo, u.Telefono, u.Mail
        FROM alerta a
        JOIN usuario u ON a.CI_deUsuario = u.CI
        ORDER BY a.Fecha DESC, a.Hora DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta SQL: " . $conn->error);
}
?>

<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Fecha</th>
    <th>Hora</th>
    <th>Tipo</th>
    <th>Descripción</th>
    <th>Estado</th>
    <th>Usuario</th>
    <th>Teléfono</th>
    <th>Email</th>
    <th>Fotos</th>
    <th>Acciones</th>
</tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['Numero_Alerta'] ?></td>
    <td><?= $row['Fecha'] ?></td>
    <td><?= $row['Hora'] ?></td>
    <td><?= $row['Tipo'] ?></td>
    <td><?= $row['Descripcion'] ?></td>
    <td><?= ($row['Estado'] == 0 ? 'Pendiente' : ($row['Estado'] == 1 ? 'En proceso' : 'Resuelta')) ?></td>
    <td><?= $row['Nombre_Completo'] ?></td>
    <td><?= $row['Telefono'] ?></td>
    <td><?= $row['Mail'] ?></td>
    <td>
        <?php if (!empty($row['Foto_de_Rotura'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['Foto_de_Rotura']) ?>" width="80"><br>
        <?php endif; ?>
        <?php if (!empty($row['Foto_de_arreglo'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['Foto_de_arreglo']) ?>" width="80">
        <?php endif; ?>
    </td>
    <td>
        <a href="cambiar_estado.php?Numero=<?= $row['Numero_Alerta'] ?>">Cambiar Estado</a><br>
        <a href="eliminar_alerta.php?Numero=<?= $row['Numero_Alerta'] ?>" onclick="return confirm('¿Eliminar alerta?')">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>


<p><a href="logout.php">Cerrar sesión</a></p>
