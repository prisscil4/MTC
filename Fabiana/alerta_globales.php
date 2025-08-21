<?php
session_start();
include(__DIR__ . '/config/conexion.php');

$usuario = null;
if (isset($_SESSION['usuario_id'])) {
    $id = intval($_SESSION['usuario_id']);
    $sql = "SELECT nombre, edad, email, telefono, foto_perfil FROM usuarios WHERE id_usuario = $id LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $usuario = mysqli_fetch_assoc($res);
    }
}


$fotoSrc = $usuario['foto_perfil'] ?? 'assets\media\perfil.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alertas Globales</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
html { box-sizing: border-box; font-size: 16px; }
*, *::before, *::after { box-sizing: inherit; }
body { margin: 0; font-family: sans-serif; background: linear-gradient(to left, #7D9BC4, #38CCCA); }

.header {
  background: linear-gradient(to bottom, #537eba, #ddfffeff);
  padding: 1.25rem;
  border-bottom-left-radius: 2.5rem;
  border-bottom-right-radius: 2.5rem;
  color: white;
  text-align: center;
  width: 100%;
  margin: 0;
  position: relative;
}

.header-title { font-size: 1.25rem; margin: 0; }
.header-logo { display: flex; flex-direction: column; align-items: center; margin: 0; }
.header-logo img { width: 10rem; height: auto; }

.search-bar { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; justify-content:center; flex-wrap: wrap; }
.search-icon-button { background: none; border: none; color: #29436B; font-size: 1.25rem; cursor: pointer; padding: 0.5rem; position: relative; }
.search-input-container { flex-grow: 1; display: flex; align-items: center; background-color: white; border-radius: 3rem; padding: 0.5rem 1rem; }
.search-input-container i { color: #29436B; }
.search-input { border: none; outline: none; width: 100%; background: none; margin: 0 0.5rem; font-size: 1rem; }
.search-input::placeholder { color: #aaa; }

.user-profile { display: flex; align-items: center; gap: 10px; margin: 20px; }
.user-avatar { width: 6.25rem; height: 6.25rem; border-radius: 50%; object-fit: cover; cursor: pointer; border: 2px solid #ccc; margin-right: 2rem; }
.user-info p { margin: 4px 0; text-align: left; }

.report-card {
  transition: all 0.3s ease;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 15px;
  background: rgba(241, 253, 255, 0.32);
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 800px;
  margin: 2rem auto 1rem auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.report-card:hover { color: #ffffff; transform: scale(1.05); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); }
.report-location { display: flex; justify-content: space-between; font-weight: bold; color: #333; gap: 15px; }
.report-location i { margin-right: 5px; color: #38CCCA; }
.report-main { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; }
.report-details { flex: 1 1 60%; }
.report-details p { margin-bottom: 8px; color: #444; }
.report-image { width: 30%; height: auto; }
.report-image img { width: 100%; height: auto; object-fit: cover; border-radius: 8px; border: 2px solid #dddddd9c; }
.report-status-inprogress { display: inline-block; width: 12px; height: 12px; background-color: orange; border-radius: 2px; margin-right: 5px; }

.filter-menu {
  position: absolute;
  top: 3.5rem;
  right: 0.5rem;
  background: white;
  border-radius: 0.5rem;
  padding: 1rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  display: none;
  z-index: 1000;
  width: 180px;
  opacity: 0;
  transition: opacity 0.2s;
}
@media (max-width: 600px) {
  .filter-menu { top: 3rem; right: 0.25rem; width: 150px; }
}
.filter-menu label { display: block; margin-top: 0.5rem; font-size: 0.85rem; }
.filter-menu select { width: 100%; margin-bottom: 0.5rem; font-size: 0.85rem; }
.filter-menu button.apply-filter {
  width: 100%; padding: 0.4rem; background-color: #38CCCA; border: none; color: white; border-radius: 0.5rem; cursor: pointer; font-size: 0.85rem;
}

.rotating { transform: rotate(90deg); transition: transform .6s ease; }
.listening { color: #ffeb3b; }

</style>
</head>
<body>

<header class="header">
  <div class="header-logo">
    <img src="assets\media\MTC.png" alt="Logo MTC">
  </div>

  <div class="user-profile">
    <label for="upload-photo">
      <img src="<?php echo $fotoSrc; ?>" alt="Foto de perfil" id="preview-photo" class="user-avatar">
    </label>
    <input type="file" id="upload-photo" accept="uploads/*" style="display: none">
    <div class="user-info">
      <p>Usuario: <?php echo $usuario['nombre'] ?? 'Invitado'; ?></p>
      <p>Edad: <?php echo $usuario['edad'] ?? '-'; ?></p>
      <p>Correo electrónico: <?php echo $usuario['email'] ?? '-'; ?></p>
      <p>Teléfono: <?php echo $usuario['telefono'] ?? '-'; ?></p>
    </div>
  </div>

  <div class="search-bar">
    <a href="index.php">
      <button class="search-icon-button back-button">
        <i class="fas fa-arrow-left"></i>
      </button>
    </a>

    <div class="search-input-container">
      <i class="fas fa-search"></i>
      <input type="text" class="search-input" placeholder="Buscar alerta...">
      <i class="fas fa-microphone"></i>
    </div>

    <button class="search-icon-button" id="filter-toggle"><i class="fas fa-filter"></i></button>

    <div class="filter-menu" id="filter-menu">
      <label>Tipo:</label>
      <select id="filter-tipo">
        <option value="">Todos</option>
        <option value="Calle">Calle</option>
        <option value="Vereda">Vereda</option>
      </select>
      <label>Estado:</label>
      <select id="filter-estado">
        <option value="">Todos</option>
        <option value="Pendiente">Pendiente</option>
        <option value="En revisión">En revisión</option>
        <option value="Aceptada">Aceptada</option>
        <option value="Finalizada">Finalizada</option>
      </select>
      <button type="button" class="apply-filter">Aplicar filtros</button>
    </div>

    <button class="search-icon-button"><i class="fas fa-sync-alt"></i></button>
  </div>

  <h1 class="header-title">Ver alertas</h1>

  <a href="subiralerta.php">
    <button type="button" class="search-icon-button">
      <i class="fas fa-plus"></i> Subir alerta
    </button>
  </a>
</header>

<div id="alerts-container"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputPhoto = document.getElementById('upload-photo');
  const previewPhoto = document.getElementById('preview-photo');
  const searchInput = document.querySelector('.search-input');
  const micIcon = document.querySelector('.search-input-container .fa-microphone');
  const syncBtn = document.querySelector('.search-bar .fa-sync-alt')?.closest('button');


  const savedImage = localStorage.getItem('userPhoto');
  if (savedImage) previewPhoto.src = savedImage;
  inputPhoto?.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewPhoto.src = e.target.result;
        localStorage.setItem('userPhoto', e.target.result);
      }
      reader.readAsDataURL(file);
    }
  });

  const alertsContainer = document.getElementById('alerts-container');
  let filtroTipo = '', filtroEstado = '', lastQuery = '';

  function debounce(fn, wait) { let t; return function(...args){ clearTimeout(t); t=setTimeout(()=>fn.apply(this,args),wait);} }

  function buildFetchUrl(texto='') {
    let url=`alertas.php?action=buscar&texto=${encodeURIComponent(texto)}`;
    if(filtroTipo) url+=`&tipo=${encodeURIComponent(filtroTipo)}`;
    if(filtroEstado) url+=`&estado=${encodeURIComponent(filtroEstado)}`;
    return url;
  }

  function cargarAlertas(texto='') {
    lastQuery = texto;
    fetch(buildFetchUrl(texto))
      .then(res => res.ok ? res.json() : Promise.reject(`HTTP ${res.status}`))
      .then(alertas => {
        if(!Array.isArray(alertas)) alertas = alertas ? [alertas] : [];
        mostrarAlertas(alertas);
      }).catch(err => {
        console.error(err);
        alertsContainer.innerHTML='<p style="text-align:center;color:#800;">No se pudieron cargar las alertas.</p>';
      });
  }

  function mostrarAlertas(alertas){
    alertsContainer.innerHTML='';
    if(!alertas.length){
      alertsContainer.innerHTML='<p style="text-align:center;color:#fff;margin:1rem;">No hay alertas que mostrar.</p>';
      return;
    }
    alertas.forEach(a=>{
      const numero = a.Numero ?? a.numero ?? a.id ?? '';
      const fecha = a.Fecha ?? a.fecha ?? a.date ?? '';
      const hora = a.Hora ?? a.hora ?? a.time ?? '';
      const descripcion = a.Descripcion ?? a.descripcion ?? a.desc ?? '';
      const foto = a.Foto_de_rotura ?? a.foto_de_rotura ?? a.foto ?? '';
      const estado = a.Estado ?? a.estado ?? a.Nombre ?? a.nombre ?? (a.ID_Estado ? `Estado ${a.ID_Estado}` : 'N/A');
      const calle = a.Nombre_Calle ?? a.nombre_calle ?? a.Calle ?? a.calle ?? a.Nombre ?? '';
      const ciudad = a.Ciudad ?? a.ciudad ?? a.NombreCiudad ?? '';
      const card=document.createElement('div');
      card.className='report-card';
      card.style.marginBottom='1rem';
      card.innerHTML=`
        <div class="report-location">
          <span><i class="fas fa-map-marker-alt"></i> ${calle||ciudad}</span>
          <span><i class="fas fa-map-marker-alt"></i> N° ${numero}</span>
        </div>
        <div class="report-main">
          <div class="report-details">
            <p><strong>Fecha y hora:</strong> ${fecha} · ${hora}</p>
            <p><strong>Descripción:</strong> ${escapeHtml(descripcion)}</p>
            <p><strong>Estado actual:</strong> <span class="report-status-inprogress"></span> ${escapeHtml(estado)}</p>
          </div>
          <div class="report-image">
            <img src="${foto ? 'uploads/'+foto:'assets/media/'}" alt="Imagen del reporte" onerror="this.src='assets/media/isotipo.png'">
          </div>
        </div>
      `;
      alertsContainer.appendChild(card);
    });
  }

  function escapeHtml(str){
    if(!str) return '';
    return String(str).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'","&#39;");
  }

  if(searchInput){
    const debounced = debounce(e=>cargarAlertas(e.target.value.trim()),300);
    searchInput.addEventListener('input', debounced);
  }

  if(syncBtn){
    syncBtn.addEventListener('click', ()=>{
      cargarAlertas(lastQuery);
      syncBtn.classList.add('rotating');
      setTimeout(()=>syncBtn.classList.remove('rotating'),600);
    });
  }

  
  const filterToggle=document.getElementById('filter-toggle');
  const filterMenu=document.getElementById('filter-menu');
  const filterTipoEl=document.getElementById('filter-tipo');
  const filterEstadoEl=document.getElementById('filter-estado');
  const applyFilterBtn=filterMenu.querySelector('.apply-filter');

  filterToggle.addEventListener('click', ()=>{
    if(filterMenu.style.display==='block'){
      filterMenu.style.opacity=0;
      setTimeout(()=>filterMenu.style.display='none',200);
    }else{
      filterMenu.style.display='block';
      setTimeout(()=>filterMenu.style.opacity=1,10);
    }
  });

  applyFilterBtn.addEventListener('click', ()=>{
    filtroTipo=filterTipoEl.value;
    filtroEstado=filterEstadoEl.value;
    cargarAlertas(searchInput?.value.trim()??'');
    filterMenu.style.opacity=0;
    setTimeout(()=>filterMenu.style.display='none',200);
  });

  if(micIcon){
    micIcon.style.cursor='pointer';
    micIcon.addEventListener('click', ()=>{
      const SpeechRec=window.SpeechRecognition || window.webkitSpeechRecognition;
      if(!SpeechRec){ alert('Tu navegador no soporta reconocimiento de voz.'); return; }
      const recognition=new SpeechRec();
      recognition.lang='es-ES';
      recognition.interimResults=false;
      recognition.maxAlternatives=1;
      recognition.start();
      recognition.onstart=()=>micIcon.classList.add('listening');
      recognition.onend=()=>micIcon.classList.remove('listening');
      recognition.onresult=event=>{
        const text=event.results[0][0].transcript;
        if(searchInput) searchInput.value=text;
        cargarAlertas(text);
      };
      recognition.onerror=e=>micIcon.classList.remove('listening');
    });
  }

 
  cargarAlertas();
});
</script>

</body>
</html>
