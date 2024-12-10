<!doctype html>
<html lang="es">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="icon" type="image/x-icon" href="https://www.chacomer.com.py/media/favicon/stores/1/chacomer-icon-36x36.png" />
  <title>CHACOMER SAE</title>
</head>

<body>
  <nav class="navbar navbar-light" style="background-color: #0f57a6;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="../img/logo_traslucido.svg" class="d-inline-block align-text-top">
      </a>
      <div class="float-right text-white" style="font-size: 24px;">
        <strong>Última Actualización: <span id="hora"></span></strong>
      </div>
    </div>
  </nav>
  <div class="container-fluid">
    <div id="carouselExampleIndicators" class="carousel slide vertical" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="row">
            <div class="col">
              <div class="alert alert-success" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
            <div class="col">
              <div class="alert alert-success" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
            <div class="col">
              <div class="alert alert-success" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
          </div>
        </div>
        <div class="carousel-item">
        <div class="col">
              <div class="alert alert-warning" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
            <div class="col">
              <div class="alert alert-warning" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
            <div class="col">
              <div class="alert alert-warning" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>3</strong></small> <br>
                asd
              </div>
            </div>
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>

  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
      //let lectura = prompt("Ingrese código");
      console.log("aca");

      /*setInterval(() => {
        setearDatos("lectura");
      }, 5000);*/
    })

    function obtenerPanel(tipo, pedido, cliente) {
      if (cliente.length > 26) {
        cliente = cliente.substr(0, 26) + "...";
      }
      return `<div class="alert alert-${tipo}" style="padding: 0px;text-align: center; margin-bottom:10px;font-size: 30px;" role="alert">
                <small><strong>${pedido}</strong></small> <br>
                ${cliente}
              </div>`;
    }

    function setearDatos(lectura) {
      $.ajax({
        type: 'GET',
        url: '/wmsd/api/v1/obtener_datos_preparacion.php',
        beforeSend: function(xhr) {
          xhr.setRequestHeader("Authorization", "Bearer " + "fd");
        },
        success: function(data) {
          $("#columna-preparacion").empty();
          $("#columna-preparados").empty();
          $("#columna-entregados").empty();
          for (let i = 0; i < data.preparados.length; i++) {
            let html = obtenerPanel("info", data.preparados[i][0], data.preparados[i][3])
            $("#columna-preparados").append(html);
          }
          for (let i = 0; i < data.preparacion.length; i++) {
            let html = obtenerPanel("warning", data.preparacion[i][0], data.preparacion[i][2])
            $("#columna-preparacion").append(html);
          }
          for (let i = 0; i < data.entregados.length; i++) {
            let html = obtenerPanel("success", data.entregados[i][0], data.entregados[i][2])
            $("#columna-entregados").append(html);
          }
          let date = new Date();
          let seconds = date.getSeconds();
          let minutes = date.getMinutes();
          let hour = date.getHours();
          const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;
          const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;
          const formattedHours = hour < 10 ? `0${hour}` : hour;
          let currentHour = formattedHours + ":" + formattedMinutes + ":" + formattedSeconds;
          $("#hora").text(currentHour);
        }
      });
    }
  </script>

</body>

</html>