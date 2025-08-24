<?php
session_start();
include('conexion.php');



// Si no tiene foto de perfil, usamos una por defecto:
$profilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : "<mtc>assets/media/perfil.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inicio</title>
  <link rel="icon" href="assets/media/Isotipo.png" />
</head>
<body>
<div class="container">
  <header>
    <div class="header">
      <div class="logo">
        <img src="assets/media/MTC.png" alt="Logo">
      </div>

      <div class="auth-buttons">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Usuario logueado -->
          <a href="confuser.php">
            <img src="<?= htmlspecialchars($profilePic) ?>" 
                 alt="Perfil" 
                 style="width:50px; height:50px; border-radius:50%; cursor:pointer;">
          </a>
        <?php else: ?>
          <!-- Usuario NO logueado -->
          <button class="btn" onclick="window.location.href='Inicio Sesion.php'">Iniciar sesión</button>
          <button class="btn" onclick="window.location.href='Registro de Usuario.php'">Registrarse</button>
        <?php endif; ?>
      </div>

      <?php if (empty($_SESSION['user_id'])): ?>
      <!-- Solo se muestra el menú móvil si NO está logueado -->
      <div class="mobile-menu">
        <button class="hamburger" id="hamburgerBtn">Menú</button>
        <div class="menu-content" id="menuContent">
          <button class="btn1" onclick="window.location.href='Inicio Sesion.php'">Iniciar sesión</button>
          <button class="btn1" onclick="window.location.href='Registro de Usuario.php'">Registrarse</button>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="text">
      <h1>¡Bienvenido a MTC!</h1>
      <h2>¿Qué es?</h2>
      <h3>Un espacio digital para cuidar tu ciudad: reporta y visualiza roturas en calles y veredas, 
      solicita reparaciones como propietario y participa del Plan Vereda para mejorar juntos el entorno urbano.</h3>
    </div>
  </header>

  <main>
    <section class="icon">
      <img src="assets/media/Denunciar.png" alt="Denunciar alerta">
      <a href="subiralerta.php"><button style="background:#315381;"><h4>Subir Alerta</h4></button></a>
    </section>

    <section class="icon">
      <img src="assets/media/Ver.png" alt="Ver alertas">
      <a href="alerta_globales.php"><button style="background:#3A6296;"><h4>Ver alertas</h4></button></a>
    </section>

    <section class="icon">
      <img src="assets/media/Unirse.png" alt="Unirse al Plan Vereda">
      <a href="inscripcionvoluntarios.html">
      <button style="background:#3E7DD1;"><h4>Unirse al Plan Vereda</h4></button></a>
    </section>

    <section class="icon">
      <img src="assets/media/Solicitar.png" alt="Solicitar arreglo">
      <a href="inscripcionvoluntarios.html">
      <button style="background:#5797ED;"><h4>Solicitar un Arreglo</h4></button></a>
    </section>
  </main>

  <footer>
    <p>El código fuente de esta aplicación está disponible bajo una 
      <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
      </a>
    </p>
  </footer>
</div>

<style>
  body {
    margin:1rem 3rem;
    font-family: sans-serif;
    background: linear-gradient(to left, #38CCCA, #7D9BC4);
  }

  .container{
    background-color: rgba(240, 255, 255, 0.28);
    border-radius: 20px;
    margin: 2rem ;
    padding: 2%;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 2rem;
  }

  .logo img {
    width: 300px;
    height: auto;
    object-fit: contain;
  }

  .auth-buttons {
    display: flex;
    gap: 0.5rem;
  }

  .btn {
    background-color: #025ace;
    color: white;
    border-radius: 1.25rem;
    border: none;
    cursor: pointer;
    width: 10rem;
    height: 2rem;
  }
  .btn:hover {
    background-color: #43caff;
  }
  .btn1{
    background-color: #025ace38;
    color: rgb(18, 26, 44);
    border: none;
    width: 100%;
    height: 2rem;
  }
  .mobile-menu {
    display: none;
    border: none;
  }

  .hamburger {
    font-size: 1rem;
    background: none;
    border: none;
    cursor: pointer;
    color: #ffffff;
  }

  .menu-content {
    display: none;
    position: absolute;
    top: 5rem;
    right: 60px;
    background-color: rgba(255, 255, 255, 0.858);
    border-radius: 10px;
    flex-direction: column;
    gap: 0.1rem;
    padding: 0.5rem;
    min-width: 150px;
    z-index: 100;
  }

  .menu-content .btn {
    width: 100%;
    padding: 0.5rem;
    font-size: 0.9rem;
    border-radius: 10px;
    color: #025ace;
    background-color: #fff;
  }

  .text {
    text-align: center;
    padding: 1rem;
    margin: 0;
  }

  h1 {
    color: white;
    font-size: 2rem;
    font-family: Georgia, 'Times New Roman', Times, serif;
    text-decoration: underline;
    margin: 0.5rem 0;
  }
  h2{
    color: rgb(1, 1, 82);
    font-size: 1.7rem;
    text-align: left;
    margin-left: 4.5rem;
  }
  h3{
    color: white;
    font-size: 1.2rem;
    margin: 0.5rem 0;
    padding: 0 1rem;
  }
  main {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
  }
  section {
    width: 15rem;
    background-color: rgba(255, 255, 255, 0.461);
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 0 10px rgba(17, 0, 255, 0.15);
    transition: transform 0.3s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
  }
  section:hover {
    transform: scale(1.05);
  }
  .icon img {
    width: 50%;
    height: auto;
    padding: auto;
  }
  .icon button {
    width: 13rem;
    height: 60px;
    font-size: 0.9rem;
    color: white;
    background-color: #3B4D8F;
    border: none;
    border-radius: 50px;
    cursor: pointer;
  }
  footer {
    margin-top: 1.5rem;
    text-align: center;
    border-radius: 1rem;
    background-color: rgba(255, 255, 255, 0.167);
    font-size: clamp(0.8rem, 2vw, 1rem);
    padding: 2rem;
  }

  /* MEDIA QUERIES */
  @media (max-width: 767px) {
    body{
      margin: 1rem;
      background: linear-gradient( #38CCCA, #7D9BC4);
    }
    .container{
      margin: 0;
    }
    .btn { display: none; }
    .mobile-menu { display: flex; }

    .logo img { width: 160px; height: auto; }

    main {
      flex-direction: column;
      align-items: center;
      gap: 1px;
    }
    section {
      width: 14rem;
      height: 6rem;
      margin: 1rem 0;
    }
    .icon img {
      width: 3rem;
      height: auto;
      margin-bottom: 0.5rem;
    }

    .icon button {
      align-items: center;
      width: 13rem;
      height: 3rem;
      border-radius: 25px;
    }

    h1 { font-size: 5mm; }
    h2 { font-size: 4mm; margin: 0.5rem 0.9rem; }
    h3 { font-size: 3mm; }
    footer{
      padding: 1rem ;
    }
  }

  @media (min-width: 768px) and (max-width: 1199px) {
    main { gap: 2rem; justify-content: center; }
    section { width: 30%; }
    .logo img { width: 160px; height: auto; }
  }

  @media (min-width: 1200px) {
    main { flex-wrap: nowrap; }
    section { width: 15rem; }
  }
</style>

<script>
  const hamburger = document.getElementById("hamburgerBtn");
  const menu = document.getElementById("menuContent");
  if (hamburger) {
    hamburger.addEventListener("click", () => {
      menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
    });
  }
</script>
</body>
</html>
