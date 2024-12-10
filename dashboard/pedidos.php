<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="icon" type="image/x-icon" href="../img/alas_favicon.ico" />
  <title>ALAS S.A.</title>
  <style>
    @keyframes parpadeo {
      0% {
        background-color: red;
        color: white;
        /* Elemento completamente visible al inicio */
      }

      50% {
        background-color: blue;
        color: white;
        /* Elemento invisible a la mitad de la animación */
      }

      100% {
        background-color: red;
        color: white;
        /* Elemento completamente visible al final */
      }
    }

    /* Aplicamos la animación a un elemento con la clase "parpadeo" */
    .parpadeo {
      animation: parpadeo 3s;
      /* La animación dura 2 segundos y se repite infinitamente */
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-light" style="background-color: #0f57a6;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../img/alas_logo.png" width="150" style=" filter: grayscale(100%) brightness(1000%);" class="d-inline-block align-text-top">
      </a>
      <div class="float-right text-white" style="font-size: 24px;">
        <strong>Última Actualización: <span id="hora"></span></strong>
      </div>
    </div>
  </nav>
  <div class="container-fluid">
    <div class="row" style="text-align: center; background-color: green;color: white; font-size: 26px;">
      <div class="col-4">
        <strong>EN PREPARACIÓN</strong>
      </div>
      <div class="col-4">
        <strong>PREPARADO</strong>
      </div>
      <div class="col-4">
        <strong>PEDIDO LISTO</strong>
      </div>
    </div>
    <div class="row" style="margin-top: 10px;">
      <div class="col-4" id="columna-preparacion">
      </div>
      <div class="col-4" id="columna-preparados">
      </div>
      <div class="col-4" id="columna-entregados">
      </div>
    </div>
  </div>
  <audio id="audio" autoplay src="/wmsd/assets/sounds/alert.wav"></audio>
  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
      localStorage.removeItem("datos_preparados");
      localStorage.removeItem("datos_preparacion");
      let lectura = prompt("Ingrese código");
      setearDatos(lectura);
      setInterval(() => {
        setearDatos(lectura);
      }, 10000);
      setearDatos1(lectura);
      setInterval(() => {
        setearDatos1(lectura);
      }, 300000);
    })

    function obtenerPanel(tipo, pedido, cliente, alerta = false) {
      if (cliente.length > 26) {
        cliente = cliente.substr(0, 26) + "...";
      }
      let parpadeo = "";
      if (alerta) {
        parpadeo = "parpadeo";
      }
      return `<div class="alert alert-${tipo} ${parpadeo}" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>${pedido}</strong></small> <br>
                ${cliente}
              </div>`;
    }

    function setearDatos(lectura) {
      $.ajax({
        type: 'GET',
        url: '/wmsd/api/v1/obtener_datos_preparacion.php',
        beforeSend: function(xhr) {
          xhr.setRequestHeader("Authorization", "Bearer " + lectura);
        },
        success: function(data) {
          $("#columna-preparacion").empty();
          $("#columna-preparados").empty();
          /*$("#columna-entregados").empty();*/
          let preparados = [];
          let preparacion = [];
          let datosPreparadosStorage = localStorage.getItem("datos_preparados");
          let datosPreparacionsStorage = localStorage.getItem("datos_preparacion");
          if (datosPreparadosStorage) {
            preparados = datosPreparadosStorage.split(',');
          }
          if (datosPreparacionsStorage) {
            preparacion = datosPreparacionsStorage.split(',');
          }
          let existeAlerta = false;
          for (let i = 0; i < data.preparados.length; i++) {
            let alerta = false;
            if (preparados.indexOf(data.preparados[i][0]) == -1) {
              alerta = true;
              existeAlerta = true;
              preparados.push(data.preparados[i][0]);
            }
            let html = obtenerPanel("info", data.preparados[i][0], data.preparados[i][3], alerta);

            $("#columna-preparados").append(html);
          }
          localStorage.setItem("datos_preparados", preparados.toString());

          for (let i = 0; i < data.preparacion.length; i++) {
            let alerta = false;
            if (preparacion.indexOf(data.preparacion[i][0]) == -1) {
              alerta = true;
              existeAlerta = true;
              preparacion.push(data.preparacion[i][0]);
            }
            let html = obtenerPanel("warning", data.preparacion[i][0], data.preparacion[i][2], alerta)
            $("#columna-preparacion").append(html);
          }
          localStorage.setItem("datos_preparacion", preparacion.toString());

          /*for (let i = 0; i < data.entregados.length; i++) {
            let html = obtenerPanel("success", data.entregados[i][0], data.entregados[i][2])
            $("#columna-entregados").append(html);*/

          let date = new Date();
          let seconds = date.getSeconds();
          let minutes = date.getMinutes();
          let hour = date.getHours();
          const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;
          const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
          const formattedHours = hour < 10 ? `0${hour}` : hour;
          let currentHour = formattedHours + ":" + formattedMinutes + ":" + formattedSeconds;
          $("#hora").text(currentHour);
          console.log(existeAlerta);
          if (existeAlerta) {
            $("#hola").trigger("click")
            let music = new Audio('/wmsd/assets/sounds/alert.wav');
            //music.play();
            var x = document.getElementById("audio");
            x.play();
          }
        }
      });
    }

    function setearDatos1(lectura) {
      $.ajax({
        type: 'GET',
        url: '/wmsd/api/v1/obtener_datos_preparacion2.php',
        beforeSend: function(xhr) {
          xhr.setRequestHeader("Authorization", "Bearer " + lectura);
        },
        success: function(data) {
          /*$("#columna-preparacion").empty();
          $("#columna-preparados").empty();*/
          $("#columna-entregados").empty();
          /*for (let i = 0; i < data.preparados.length; i++) {
            let html = obtenerPanel("info", data.preparados[i][0], data.preparados[i][3])
            $("#columna-preparados").append(html);
          }
          for (let i = 0; i < data.preparacion.length; i++) {
            let html = obtenerPanel("warning", data.preparacion[i][0], data.preparacion[i][2])
            $("#columna-preparacion").append(html);
          }*/
          let entregas = [];
          let datosEntregasStorage = localStorage.getItem("datos_entrega");
          if (datosEntregasStorage) {
            entregas = datosEntregasStorage.split(',');
          }
          for (let i = 0; i < data.entregados.length; i++) {
            let alerta = false;
            if (entregas.indexOf(data.entregados[i][0]) == -1) {
              alerta = true;
              entregas.push(data.entregados[i][0]);
            }
            let html = obtenerPanel("success", data.entregados[i][0], data.entregados[i][2]);

            $("#columna-entregados").append(html);
          }
          localStorage.setItem("datos_entrega", entregas.toString());
        }
      });
    }
  </script>
</body>

</html>