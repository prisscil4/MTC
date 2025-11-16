<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - MTC</title>
    <link rel="stylesheet" href="assets/css/TablaDeAdmin.css">
</head>
<body>

    <header class="header">
        <a href="index.html" class="back-link">← Inicio</a>
        <img src="uploads/Interfaces del software.png" alt="Logo Sistema de Alerta Veloz" class="logo">
        <img src="uploads/Logo Software.png" alt="Logo MTC Mejora Tu Ciudad" class="logo">
        <img src="MEDIA/logo_intendencia.jpg" alt="Logo de La Intendencia" class="logo">
    </header>

    <main class="main-content">
<div class="footer-links">
            <a href="#">Listado de usuarios</a>
            <a href="#">Listado de Voluntarios</a>
        </div>
        <section class="panel">
            <h2>Panel de Alertas</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Cédula</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Dirección</th>
                            <th>Ubicacion</th>
                            <th>progreso</th>
                            <th></th>
                        </tr>
                    </thead>
            <tbody>
                <tr>
                    <td><input type="text" value="1"></td>
                    <td><input type="text" value="Lucía Pérez"></td>
                    <td><input type="email" value="lucia@mail.com"></td>
                    <td><input type="text" value="099123456"></td>
                    <td><input type="text" value="12345678"></td>
                    <td><input type="date" value="2025-08-06"></td>
                    <td><input type="time" value="14:30"></td>
                    <td><input type="text" value="Montevideo 123"></td>
                    <td><input type="text" value="134.566.45"></td>
                    <td><select type="text" value="progreso"></td>
                    <td><input type="text" value="Eliminar"></td>
                </tr>
            </tbody>        
                </table>
            </div>
        </section>

        <section class="panel">
            <h2>Panel de Voluntarios</h2>
             <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Cédula</th>
                            <th>Disponibilidad</th>
                            <th>Alertas asignadas</th>
                        </tr>
                    </thead>
         <tbody>
                <tr>
                    <td><input type="text" value="Lucía Pérez"></td>
                    <td><input type="text" value="099123456"></td>
                    <td><input type="text" value="Montevideo 123"></td>
                    <td><input type="text" value="12345678"></td>
                    <td><input type="text" value="De lunes a jueves"></td>
                    <td><input type="text" value="2"></td>
                    <td><input type="text" value="Eliminar"></td>
                </tr>
            </tbody>                 
                </table>
            </div>
        </section>

    </main>

    <footer class="footer">
        <p>2025 @Derechos de Mejora Tu Ciudad</p>
    </footer>


<?php
include 'conexion.php'

?>

</body>
</html>
