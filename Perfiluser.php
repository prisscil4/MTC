<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi Perfil - MTC</title>
  <link rel="stylesheet" href="formularios.css"> 
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> 
</head>
<body>

  <div class="form-container">

    <!-- Flecha y Logo -->
    <div class="header-flex">
      <a href="index.php" class="back-arrow"><i class="fas fa-arrow-left fa-lg"></i></a>
      <div class="logo-container">
        <img src="https://via.placeholder.com/150x50?text=MTC+Logo" alt="Logo" class="logo-img">
      </div>
    </div>

    <!-- Perfil -->
    <div style="text-align: center; margin-top: -10px;">
      <i class="fas fa-user-circle fa-3x"></i>
      <h2>Mi perfil</h2>
      <p><strong>Trabajador Nº • 10</strong></p>
    </div>

   <!-- Nombre -->
<div class="input-wrapper">
  <label class="titulo-from">Nombre:</label>
  <input type="text" id="nombre" placeholder="{usuario}" disabled>
  <i class="fas fa-pen" onclick="editarCampo('nombre')"></i>
</div>

<!-- Teléfono -->
<div class="input-wrapper">
  <label class="titulo-from">Teléfono:</label>
  <input type="text" id="telefono" placeholder="+598" disabled>
  <i class="fas fa-pen" onclick="editarCampo('telefono')"></i>
</div>

<!-- Área -->
<div class="input-wrapper">
  <label class="titulo-from">Área:</label>
  <input type="text" id="area" placeholder="Lavalleja y Zalpican" disabled>
  <i class="fas fa-pen" onclick="editarCampo('area')"></i>
</div>

<!-- Disponibilidad -->
<div class="input-wrapper">
  <label class="titulo-from">Disponibilidad:</label>
  <input type="text" id="disponibilidad" placeholder="Lunes, jueves y viernes..." disabled>
  <i class="fas fa-pen" onclick="editarCampo('disponibilidad')"></i>
</div>


    <!-- Botones -->
    <div class="">
      <button style="width: 50%;">Configuración de Cuenta</button>
      <button style="width: 50%;">Ver alertas asignadas</button>
    </div>

    <!-- Eliminar cuenta -->
    <div style="text-align: center; margin-top: 20px;">
      <button style="background: red; width: auto; padding: 10px 20px;">Eliminar Cuenta</button>
    </div>

    <!-- Footer -->
    <p style="text-align: center; font-size: 12px; margin-top: 20px;">Mejora Tu Ciudad MTC Todos los derechos de autor</p>

  </div>
<script>
  function editarCampo(id) {
    const campo = document.getElementById(id);
    campo.disabled = false;
    campo.focus(); // pone el cursor directamente en el campo
  }
  document.querySelectorAll('input').forEach(input => {
  input.addEventListener('blur', () => {
    input.disabled = true;
  });
});

</script>

</body>
</html>
