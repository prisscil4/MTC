<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Subir Alerta</title>
    <base href="http://localhost/mtc/paginasweb/">
   <link rel="icon" href="media\Isotipo.png" />
</head>

<body>
  <div class="contenedor">
    <header>
      <h2><img src="media\Logo.png" width="190" height="90"></h2>

    </header>
    <div class="volver">
<a href="inicio.php">
  <button>Volver</button>
</a>
</div>
    <h1>Alerta</h1>


 <div class="imagen-subida">
  <div class="marco-imagen">
    <img id="preview" src="media\fotogaleria.png" alt="Subir imagen" />
  </div>
  <label for="imagen" class="boton-subida">Subir imagen</label>
  <input type="file" id="imagen" accept="image/*" hidden>
</div>

    <form>
      <label for="tipo">Tipo de rotura</label>
      <select id="tipo">
        <option selected disabled>Seleccione una opción</option>
        <option>Vereda</option>
        <option>Calle</option>
      </select>


      <label for="correo">Correo electrónico</label>
        <input type="email" id="correo" placeholder="Ingrese su correo electrónico">


      <label for="telefono">Teléfono</label>
    <input type="tel" id="telefono" placeholder="Ingrese su número de teléfono">


      <label for="puerta">Número de puerta</label>
        <input type="text" id="puerta" placeholder="Ingrese su número de puerta">



      <label for="calle">Nombre de la calle</label>
      <input type="text" id="calle" placeholder="Ingrese el nombre de la calle">


      <label for="entre">Entre calle 1 y calle 2</label>
      <input type="text" id="entre" placeholder="Ingrese entre que calles está">


      <label for="descripcion">Descripción del problema</label>
      <input type="text" id="descripcion" placeholder="Ingrese la descripción del problema">


      <button type="submit">Enviar alerta</button>

    </form>
  </div>
</section>
</main>
<footer>
   <p>El código fuente de esta aplicación está disponible bajo una
    <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
    </a></p>




</footer>
<script>
  document.getElementById("imagen").addEventListener("change", function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById("preview");


    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>
<script>


  window.onscroll = function() {
    const btn = document.getElementById("btnArriba");
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  };


  document.getElementById("btnArriba").addEventListener("click", function() {
    window.scrollTo({
      top: 0,
      behavior: "smooth"
    });
  });
</script>

<?php
include 'C:\xampp\htdocs\mtc\paginasweb\conexion.php';
?>
<style>
body {
  margin: 0;
  font-family: sans-serif;
  background: linear-gradient(to left, #38CCCA, #7D9BC4);
}

h2{
  width: 0;
  position:absolute;
  left: 51.5%;
    top: 0rem;
}

.contenedor {
  max-width: 414px;
  margin: auto;
  padding: 20px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

h1 {
  text-align: center;
  color: #0c2f5d;
  margin: 20px 0;
}

.imagen-subida {
  text-align: center;
  margin-bottom: 20px;
}

.imagen-subida label {
  cursor: pointer;
  display: inline-block;
  border: 4px solid #1dd2b4;
  padding: 10px;
  border-radius: 15px;
  background-color: #e5fffc;
}

.imagen-subida img {
  width: 100px;
  height: auto;
  display: block;
  margin: auto;
}

.imagen-subida p {
  margin-top: -5px;
  font-weight: bold;
  color: #1b8369;
}

form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

label {
  font-weight: bold;
  margin-top: 10px;
}

select,
input[type="text"],
input[type="email"],
input[type="tel"] {
  padding: 10px;
  border-radius: 10px;
  border: none;
  background-color: #4fa0b9;
  color: white;
  font-size: 14px;
}

::placeholder {
  color: #d0e8f3;
}

.campo-icono {
  position: relative;
}

.campo-icono input {
  width: 100%;
  padding-right: 30px;
}

.campo-icono .icono {
  position: absolute;
  right: 10px;
  top: 10px;
  pointer-events: none;
  color: white;
}

button {
  margin-top: 20px;
  padding: 12px;
  background-color: #00e0c6;
  color: white;
  font-size: 18px;
  font-weight: bold;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

button::before {
  content: '➤';
  font-size: 20px;
}

.volver {
  text-align: center;
}

.volver button::before {
  content: '↩';
  font-size: 18px;
}


footer {
    text-align: center;
    padding: 1rem;
    background-color: rgba(255, 255, 255, 0.8);
    font-size: 4mm;
}

.marco-imagen {
  border: 4px solid #1dd2b4;
  padding: 10px;
  border-radius: 15px;
  background-color: #e5fffc;
  width: 85%;
  box-sizing: border-box;
  margin-bottom: 10px;
  margin-left: 7.5%;
}

.marco-imagen img {
 width: 95%;
  max-width: 100%;
  height: auto;
  display: block;
  margin: 0 auto;
  border-radius: 10px;
}

.boton-subida {
  cursor: pointer;
  display: inline-block;
  padding: 8px 16px;
  margin-top: 7px;
  background-color: #e5fffc;
  border: 2px solid #1dd2b4;
  border-radius: 10px;
  color: #1b8369;
  font-weight: bold;
  font-size: 14px;
  text-align: center;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.boton-subida:hover {
  background-color: #d2f8f3;
  transform: scale(1.03);
}
@media (max-width: 767px) {
  main {
    grid-template-columns: 1fr;
  }

  .logo img {
    max-width: 150px;
  }
}


@media (min-width: 768px) and (max-width: 1199px) {
  main {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1200px) {
  main {
    grid-template-columns: repeat(4, 1fr);
    max-width: 1400px;
    margin: auto;
  }
}



</style>
</body>
</html>