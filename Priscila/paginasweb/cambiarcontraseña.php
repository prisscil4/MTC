<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña</title>
   <link rel="icon" href="Isotipo.png" />
   
</head>
<body>
<div class="contenedor">
    <header>
      <div class="logo">
        <img src="Logo.png" width="290" height="130" alt="Logo " >
    </div>

    <h1>Cambiar contraseña</h1>
</header>
<main>
    <section>
      
      <form id="formulario"> 
        <label>Contraseña nueva:</label><br>
        <div class="input-container">
          <input 
            type="password" 
            id="pass1" 
            value="Ingrese su nueva contraseña"
            maxlength="32" 
            pattern="[A-Za-z0-9]{1,32}" 
            required
            title="Mínimo 6 carácteres. Sin puntos ni guiones (- o .)">
          <button type="button" onclick="togglePass('pass1', this)">
            <img src="img/ocultar.png" alt="ver" width="20">
          </button>
        </div><br>
      <br>
        <label>Confirmar contraseña:</label><br>
        <div class="input-container">
          <input 
            type="password" 
            id="pass2" 
            value="Confirme su nueva contraseña"
            maxlength="32" 
            pattern="[A-Za-z0-9]{1,32}" 
            required
            title="Mínimo 6 carácteres. Sin puntos ni guiones (- o .)">
          <button type="button" onclick="togglePass('pass2', this)">
            <img src="img/ocultar.png" alt="ver" width="20">
          </button>
        </div><br>
      
        <button type="submit">Enviar</button>
      </form>
      
    </section>
</main>
</div>
<script>
function togglePass(id, btn) {
  const input = document.getElementById(id);
  const img = btn.querySelector('img');

  if (input.type === "password") {
    input.type = "text";
    img.src = "media/img/mostrar.png";
  } else {
    input.type = "password";
    img.src = "media/img/ocultar.png";
  }
}

document.getElementById('formulario').addEventListener('submit', function(e) {
  const pass1 = document.getElementById('pass1');
  const pass2 = document.getElementById('pass2');

  if (pass1.value !== pass2.value) {
    pass2.setCustomValidity("Las contraseñas no coinciden");
    pass2.reportValidity();
    e.preventDefault();
  } else {
    pass2.setCustomValidity("");
  }
});
</script>

<?php
include  'C:\xampp\htdocs\mtc\paginasweb\conexion.php';
?>

<style>
     body{
  background: linear-gradient(to left, #38CCCA, #7D9BC4);
}

h1{
    font-family: sans-serif;
    color: #29436b;
    text-align: center;
}


.input-container {
  position: relative;
  display: flex;
  align-items: center;
  color: rgb(255, 255, 255);
}

.input-container input {
  padding-right: 30px; 
  width: 100%;
  box-sizing: border-box;
  
}
.logo{
  position:absolute;
    left: 7.5rem;
    top: -4rem;
}



.input-container button {
  position: absolute;
  right: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.contenedor{
  font-family: sans-serif;
  max-width: 414px;
  padding: 60px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  width: 100%;
  position: absolute;
  top: 15rem;
  left: 35.5%;
  height: 60%;
  
}
input[type="password"],[type="text"]{
  padding: 15px;
  border-radius: 10px;
  border: none;
  background-color: #1b7dba;
  color: white;
  font-size: 14px;
  width: 100%;
 
}
label{
  font-weight: bold;
}
button[ type="submit"] {
  margin-top: 3.5rem;
  padding: 12px;
  margin-left: 6rem;
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
  width: 50%;

}
button[ type="submit"]::before {
  content: '➤';
  font-size: 20px;
}

</style>
</body>
</html>