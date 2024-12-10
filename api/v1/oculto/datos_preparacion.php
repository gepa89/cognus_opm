$(document).ready(function() {
        let token = "101568";
        let lectura = prompt("Ingrese código");
        if (lectura == token) {
          setearDatos(lectura);
          setInterval(() => {
            setearDatos(lectura);
          }, 10000);
        }else{
          alert("codigo invalido");
        }

      }

    )

    function obtenerPanel(tipo, pedido, cliente) {
      return `<div class="alert alert-${tipo}" style="padding: 1px;text-align: center; margin-bottom:10px" role="alert">
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
          console.log(data);
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
          const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds
          let currentHour = date.getHours() + ":" + date.getMinutes() + ":" + formattedSeconds;
          $("#hora").text(currentHour);
        }
      });
    }