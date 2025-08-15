<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inicio</title>
  <link rel="icon" href="media\Isotipo.png" />
    <base href="http://localhost/mtc/paginasweb/">
</head>
<body>

  <header>
    <div class="logo">
        <img src="media\Logo.png" width="290" height="130" alt="Logo" >
    </div>

    <h1>¡Bienvenido a MTC!</h1>
    <h2>¿Quienes somos?</h2>
    <h3>Un espacio digital para cuidar tu ciudad: reporta y visualiza roturas en calles y veredas, 
    solicita reparaciones como propietario y participa del Plan Vereda para mejorar juntos el entorno urbano.</h3>
  </header>

  <main>
    <section>
      <img src="media\denunciar.png" alt="Denunciar alerta">
      <a href="subiralerta.php"><button>Denunciar alerta</button></a>
    </section>


    <section>
      <img src="media\ver.png" alt="Ver alertas">
      <button>Ver alertas</button>
    </section>


    <section>
      
  <img src="media\unirse.png" alt="Inscribirse al Plan Vereda" id="img-unir">
      <button>Inscribirse al Plan Vereda</button>
    </section>


    <section>
      <img src="media\solicitar.png" alt="Solicitar arreglo">
      <button>Solicitar arreglo</button>
    </section>
  </main>


  <footer>
    <p>El código fuente de esta aplicación está disponible bajo una 
    <a href="http://creativecommons.org/licenses/by/4.0/" target="_blank">
      Licencia Creative Commons Atribución 4.0 Internacional
    </a></p>
  </footer>

  <?php
include 'C:\xampp\htdocs\mtc\paginasweb\conexion.php';
?>
<style>
body {
    margin: 0;
    font-family: sans-serif;
    background: linear-gradient(to left, #38CCCA, #7D9BC4);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

header {
    padding: 2rem 1rem;
}

h1 {
    color: black;
    font-size: 13mm;
    font-family: Georgia, 'Times New Roman', Times, serif;
    text-align: center;
    text-decoration: underline;
}

h2{
  color: #0c2f5d;
  font-size: 9mm;
  font-family: sans-serif;

}

h3{
  color: black;
  font-size: 7mm;
  font-family: sans-serif;
}
main {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    padding: 2rem;
}
#img-unir {
    width: 100px;
    height: auto; 
}

section {
    width: 280px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 40px;
    padding: 1rem;
    text-align: center;
    box-shadow: 0 0 10px rgba(17, 0, 255, 0.2);
    transition: transform 0.3s;
    display: flex;           
    flex-direction: column;   
    align-items: center;      
    justify-content: center;  
    gap: 1rem;  
}

section:hover {
    transform: scale(1.05);
}

section img {
  
    max-width: 120px;
    height: auto;
}

button {
  color: #0c2f5d;
  font-size: clamp(1rem, 2.5vw, 1.5rem);
  font-family: sans-serif;
  font-weight: bolder;
  background-color: #5797ed;
  border: none;
  border-radius: 40px;
  padding: 0.5rem 1rem;
  margin-top: 2rem;
  cursor: pointer;
  transition: background-color 0.3s;
  
}

button:hover {
    background-color: #75a9ec;
}

footer {
  text-align: center;
  padding: 1rem;
  background-color: rgba(255, 255, 255, 0.8);
  font-size: clamp(0.8rem, 2vw, 1rem);
  
}


.logo{
    position:flex;
    flex-wrap: wrap;
    left: 0rem;
    top: 0rem;
  
}


@media (max-width: 768px) {
  main {
    grid-template-columns: 1fr;
  flex-direction: column;
  }

  section {
    width: 90%;
    max-width: none;
  }

  .logo {
    max-width: 150px;
  }

  section img{
    max-width: 33%;
  }

 button{
  padding: 1rem 2.5rem;
 }
}


@media (min-width: 768px) and (max-width: 1199px) {
  main {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
  
  section {
    width: 90%;
    max-width: none;
  }
}
button{
  padding: 1rem 2.5rem;
 }
 .logo {
    max-width: 150px;
  }

@media (min-width: 1200px) {
  main {
    grid-template-columns: repeat(4, 1fr);
    max-width: 1400px;
    margin: auto;
  }
  .logo {
    max-width: 150px;
  }
}

</style>

</body>
</html>
