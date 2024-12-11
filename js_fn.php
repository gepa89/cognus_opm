

    <script>
            $(document).ready(function() {
//                $('#mped').addClass('active');
//                $('#inputEntrega').focus();
                let parametros = new URLSearchParams(window.location.search);
                let almacen = parametros.get('almacen');
                let busqueda = "";
                if (almacen) {
                    busqueda = `?almacen=${almacen}`
                }
                loadPieR1(busqueda);
                loadPieR2(busqueda);
                loadPieR3(busqueda);
                loadPieR7(busqueda);
                loadPieR4(busqueda);
                loadPieR5(busqueda);
                loadPieR6(busqueda);
                loadHCColumns(busqueda);
                setInterval(function(){
                    let parametros = new URLSearchParams(window.location.search);
                    let almacen = parametros.get('almacen');
                    let busqueda = "";
                    if (almacen) {
                        busqueda = `?almacen=${almacen}`
                    }
                    loadPieR1(busqueda);
                    loadPieR2(busqueda);
                    loadPieR3(busqueda);
                    loadPieR7(busqueda);
                    loadPieR4(busqueda);
                    loadPieR5(busqueda);
                    loadPieR6(busqueda);
                    loadHCColumns(busqueda);
                },300000);
                
            });
            
            
            function swModal(id){
                $('#'+id).modal('show');
//                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
            }
            function loadPieR1(busqueda){
                $.ajax({ 
                    url: `graphs/e_pedidos.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        console.log(dt);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_1').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total de Entregas: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'right',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            function loadPieR2(busqueda){
                $.ajax({ 
                    url: `graphs/e_pedidos2.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_2').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'right',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            
            function loadPieR3(busqueda){
                $.ajax({ 
                    url: `graphs/e_pedidos3.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_3').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'right',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            function loadPieR7(busqueda){
                $.ajax({ 
                    url: `graphs/e_pedidos4.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_7').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'right',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            function loadPieR4(busqueda){
                $.ajax({ 
                    url: `graphs/r_pedidos.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_4').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'bottom',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            function loadPieR5(busqueda){
                $.ajax({ 
                    url: `graphs/r_pedidos2.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_5').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'bottom',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
            
            function loadPieR6(busqueda){
                $.ajax({ 
                    url: `graphs/r_pedidos3.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
                        
//                        var object={'positive':94,'neutral':2,'negative':2};
                        var data=[];
                        for(i in dt.dat){
                            data.push({"name":i+" ["+dt.dat[i]+"]","y":parseFloat(dt.dat[i])});
                        }
                        $('#gr_6').highcharts({
                            chart: {
                                plotBackgroundColor: null,
                                plotBorderWidth: null,
                                plotShadow: false,
                                type: 'pie'
                            },
                            title: {text:  'Total: '+dt.total},
                            tooltip: {
                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
                            },
                            legend: {
                                align: 'bottom',
                                verticalAlign: 'bottom',
                                layout: 'vertical'
                            },
                            plotOptions: {
                                pie: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: {
                                        color:'black',
                                        distance: -20,
                                        formatter: function () {
                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';

                                        }
                                    },
                                    showInLegend: true
                                }
                            },
                            series: [{
                                name: 'Estado',
                                colorByPoint: true,
                                data: data
                            }],
                            responsive: {
                                rules: [{
                                    condition: {
                                        maxWidth: 500
                                    },
                                    chartOptions: {
                                        legend: {
                                            align: 'center',
                                            verticalAlign: 'bottom',
                                            layout: 'vertical'
                                        },
                                        yAxis: {
                                            labels: {
                                                align: 'bottom',
                                                x: 0,
                                                y: -5
                                            },
                                            title: {
                                                text: null
                                            }
                                        },
                                        subtitle: {
                                            text: null
                                        },
                                        credits: {
                                            enabled: false
                                        }
                                    }
                                }]
                            }
                        });
                    }
                });
            };
    </script>
    


    <script>
        var entsMn = [];
            var entsBl = [];
            function clsDetail(){
                $('#smpl_tbl tbody').empty();
                $('#tbl_cnt').toggle("fast","linear");
                $("#btnPk").prop('disabled', true);  
                $("#btnAnl").prop('disabled', true);  
                $("#vlnEntrega").val('');
                $("#vlnHdnEntrega").val('');                
            }
            function clsBoleta(){
                entsBl = [];
                $('#blEntrega').val('');
                $('#blNroManifiesto').val('');
                $('#blcontent').empty();
                $('#prBlArea').empty();
                $(".blEntTbl").empty();
                $("#previewBl").prop('disabled', true);  
            }
            function impBoleta(){
                var cntnt = '';
                var mnChofer = $('#blTransporte').val();
                var mnChapa = $('#blChapa').val();
                var mnObs  = $('#blObs').val();
                $('#prBlArea').empty();
                $.each(entsBl, function(id, val){
                    var printContents = document.getElementById('mnHdr'+val).innerHTML;
                    for(var i= 0; i < 3; i++){
                        cntnt += '<div class="saltoP" style="clear:both;"></div>';
                        cntnt += '<div class=" col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 12px;padding: 12px;border: 1px dashed #dcdcdc;">';
                            cntnt += printContents;
                            cntnt += '<br/><br/><div style="width:100% !important; text-align:center; font-size: 10px;">';
                            switch(i){
                                case 0:
                                    var emp = $("#inHdnEnt"+val).val();
                                    var empx = emp.split(' ');
                                    cntnt += '1 '+empx[0];
                                    break;
                                case 1:
                                    cntnt += '2 COBRANZA';
                                    break;
                                case 2:
                                    cntnt += '3 CLIENTE';
                                    break;
                            }
                            
                            cntnt += '</div>';
                        cntnt += '</div>';
                        cntnt += '<div class="saltoP2" style="clear:both;page-break-after: always !important;"></div>';
                    }
                });
                $('#prBlArea').append(cntnt);
                setLevel(entsBl,mnChofer,mnChapa,mnObs,mnChofer);
                printDiv('prBlArea');
            }
            function padLeft(nr, n, str){
                return Array(n-String(nr).length+1).join(str||'0')+nr;
            }
            function genPedComment(){
                var count = parseInt($("#cmtCount").val())+1;
                cnttn = '<br/><input type="text" class="col-lg-12 col-sm-12 col-xs-12 form-control" name="pedComent" value="" placeholder="Comentario '+ count +'">';
                $("#cmtCount").val(count);
                $("#commentdiv").append(cnttn);
            }
            function genPed(id = ""){
                if(id == ''){
                    var pedID = $('#pedID').val();
                }else{
                    var pedID = id;
                }
                
                var pedCantBul = $('#pedCantBul').val();
//                var pedComent = $('input[name="pedComent"]').serialize().;
                var pedComent = [];
                $("input[name='pedComent']").each(function() {
                    pedComent.push($(this).val());
                });
                $.ajax({ 
                    url: 'requests/print.php', 
                    type: 'POST',
                    data: {
                        pedID:pedID,
                        impresion:'reped'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        $("#toPrintPed").html("");
                        var d = new Date();
                        var strDate = d.getDate() + "/" + (d.getMonth()+1) + "/" + d.getFullYear();
                        var cntnt = '';
                        var dato = dt.data[0];                        
                        if(dt.data.length > 0){
                            for(var i = 1; i <= pedCantBul; i++){
                                cntnt += '<div id="codepedin'+i+'"  style="padding-left: 5px !important;page-break-after: always;"><br/>';
                                cntnt += '<table  border="0" cellspacing="0" cellpadding="0" style="max-width: 400px !important; width: 100% !important; border:none;" class="">';                     
                                cntnt += '<thead style="padding: 0px !important;">';                            
                                cntnt += '<tr>';
                                cntnt += '<th style="padding: 0px !important; font-size: 12px" colspan="3">'+dato.clinom+'</th>';
                                cntnt += '</tr>';
                                cntnt += '</thead>';                        
                                cntnt += '<tbody style="padding: 0px !important;">';                            
                                cntnt += '<tr>';
                                cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">Cod. Cliente</td><td style="padding: 0px !important;">'+dato.clirefer+'</td><td rowspan="5" style="width: 60% !important;"><div id="codeped'+i+'"></div></td>';
                                cntnt += '</tr>';
                                cntnt += '<tr>';
                                cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">Pedido</td><td style="padding: 0px !important; font-size: 20px;"><span style="padding: 2px !important; border: 1px solid gray;">'+dato.pedexentre+'</span></td>';
                                cntnt += '</tr>';
                                if(dato.desdirec != '' && dato.desdirec){
                                    cntnt += '<tr>';
                                    cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">Dirección</td><td style="padding: 0px !important;">'+dato.desdirec+'</td>';
                                    cntnt += '</tr>';
                                }
                                cntnt += '<tr>';
                                cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">Fecha</td><td style="padding: 0px !important;">'+strDate+'</td>';
                                cntnt += '</tr>';                                
                                cntnt += '<tr>';
                                cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">C.Bultos</td><td style="padding: 0px !important;">'+i+"/"+pedCantBul+'</td>';
                                cntnt += '</tr>';
                                if(dato.comentario != '' && dato.comentario){
                                    cntnt += '<tr>';
                                    cntnt += '<td style="padding: 0px !important;padding-right: 5px !important;">Comentario</td><td style="padding: 0px !important;">'+dato.comentario+'</td>';
                                    cntnt += '</tr>';                                    
                                }
                                cntnt += '</tbody>';
                                cntnt += '<tfoot style="padding: 0px !important;">';  
                                j= i-1;
                                if(pedComent[j] && pedComent[j] != ''  && pedComent[j] != 'null'){
                                    cntnt += '<tr>';
                                    cntnt += '<td style="padding: 0px !important;padding-right: 5px !important; white-space:pre-wrap !important;">Obs.:</td><td style="padding: 0px !important;" colspan="2">'+pedComent[j]+'</td>';
                                    cntnt += '</tr>';                                   
                                }                                
                                cntnt += '</tfoot>';
                                cntnt += '</table>';
                                cntnt += '</div>';
                            }
                            
                            $("#toPrintPed").empty().append(cntnt);
                            
                            for(var i = 1; i <= pedCantBul; i++){      
                                generateBarcodePed(dato.pedexentre,'code128', 'codeped'+i);
                            }
                            $("#printEtPed").prop('disabled', false);  
                        }else{
                            alert("Sin datos para mostrar");
                        }
                        
                    }
                });
            }
            function genEan(){
                var etNros = $('#etEanList').val();
                $.ajax({ 
                    url: 'requests/print.php', 
                    type: 'POST',
                    data: {
                        etNros:etNros,
                        impresion:'reean'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        $("#toPrintEan").html("");
                        var cntnt = '';
                        $.each(dt.data,function(idx, val){                            
                            cntnt = '<div id="codeean'+idx+'" style="page-break-after: always;"></div><br/>';
                            $("#toPrintEan").append(cntnt);
                            generateBarcode(val,'code128', 'codeean'+idx+'');
                            $("#printEtEan").prop('disabled', false);  
                        });
                    }
                });
            }
            function genEanBulk(){
                var eanList = $('#inputEanBulk').jexcel('getData', false);
                var cEtiq = $("#cEtiq").val();
                var hsep = $("#hsep").val();
                var vsep = $("#vsep").val();
                var almed = $("#almed").val();
                var anmed = $("#anmed").val();
                if(cEtiq != ''){
                    $.ajax({ 
                        url: 'requests/print.php', 
                        type: 'POST',
                        data: {
                            eanList:eanList,
                            impresion:'buean'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            $("#toPrintEan").html("");
                            var cntnt = '';
                            
                            width2pxl = anmed * 3.7795275591;
                            height2pxl = almed * 3.7795275591;
                            width2pxl = parseInt(width2pxl)*(2);
                            height2pxl = parseInt(height2pxl)*(2);
                            svert2pxl = vsep * 3.7795275591;
                            shor2pxl = hsep * 3.7795275591;
                            svert2pxl = parseInt(svert2pxl)*(2);
                            shor2pxl = parseInt(shor2pxl)*(2);
                            
                            var cancol = Math.ceil(dt.data.length / cEtiq);
                            cntnt = '<div style="oveerflow:hidden; width:'+width2pxl+'px; display:inline-block; position:relative;">';
                            $.each(dt.data,function(idx, val){                            
                                cntnt += '<div style="padding: 2px;    border: 1px solid black;    display: flex;    overflow: hidden;   align-items: center;    align-content: space-around;    flex-wrap: nowrap;    justify-content: space-between;    flex-direction: column;height:'+height2pxl+'px ;margin: 0 '+svert2pxl+'px '+shor2pxl+'px 0;">';
                                cntnt += '<div style="width:100%;top:0; position:relative; white-space:nowrap;display:flex;"><label style="width:100%;font-size:110%;text-align:center;">'+val.artdesc+'</label></div>';
                                cntnt += '<div style="width:100%;" id="eanblk'+idx+'" style="align-self: center;"></div>';
                                cntnt += '<div style="width:100%;bottom:0; position:relative; display:flex;"><label style="width:100%;font-size:110%;text-align:center;">'+val.ean+'</label></div>';
                                cntnt += '</div>';
                                var cntr = idx + 1;
                                if(cntr % cEtiq == 0){
                                    cntnt += '</div><div style="width:'+width2pxl+'px; display:inline-block; position:relative;">';
                                }
                            });
                            cntnt += '</div>';
                            $("#toPrintEanBlk").append(cntnt);
                            var settings = {
                                output:'css',
                                bgColor: '#FFFFFF',
                                color: '#000000',
                                showHRI: false,
                                barWidth: 1,
                                barHeight: 50,
                                margin: 1,
                                fontSize: 30,
                                moduleSize:5
                              };
                            $.each(dt.data,function(idx, val){                                    
                                generateBarcodeBlk(val.ean, 'code128', 'eanblk'+idx+'',settings);
                            });
//                            generateBarcode(val,'ean13', 'codebean'+idx+'');
                            $("#printEtEanBlk").prop('disabled', false);  
                        }
                    });
                }else{
                    alert("Debe ingresar cantidad de etiquetas por linea.");
                }
                
            }
            function genEtiUbi(){
                var dEstante = $('#dEstante').val();
                var hEstante = $('#hEstante').val();
                var dHueco = $('#dHueco').val();
                var hHueco = $('#hHueco').val();
                var dNiv = $('#dNiv').val();
                var hNiv = $('#hNiv').val();
                var dSubNiv = $('#dSubNiv').val();
                var hSubNiv = $('#hSubNiv').val();
                var codalma = $('#codalma').val();
                var formatoImpresion = $('#formato_impresion').val();
                let params = new URLSearchParams();
                params.set('dEstante',dEstante);
                params.set('hEstante',hEstante);
                params.set('dHueco',dHueco);
                params.set('hHueco',hHueco);
                params.set('dNiv',dNiv);
                params.set('hNiv',hNiv);
                params.set('dSubNiv',dSubNiv);
                params.set('hSubNiv',hSubNiv);
                params.set('codalma',codalma);
                params.set('impresion','reub');
                params.set('configuracion',formatoImpresion);
                window.open("/wmsd/pdf/generar_pdf_etiquetas.php?"+params.toString(), '_blank');
                /*$.ajax({ 
                    url: 'requests/prueba_impresion.php', 
                    type: 'GET',
                    data: {
                        dEstante:dEstante,
                        hEstante:hEstante,
                        dHueco:dHueco,
                        hHueco:hHueco,
                        dNiv:dNiv,
                        hNiv:hNiv,
                        dSubNiv:dSubNiv,
                        hSubNiv:hSubNiv,
                        impresion:'reub',
                        codalma:codalma,
                        configuracion:formatoImpresion
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        $("#toPrintUbi").html("");
                        var cntnt = '';
                        $.each(dt.data,function(idx, val){                            
                            cntnt = '<div style="display:inline-block;page-break-before: always;" id="codeUbi'+idx+'"></div><br/>';
                            $("#toPrintUbi").append(cntnt);
                            console.log(val);
                            generateBarcodeX(val.forcod,val.forlbl,'code128', 'codeUbi'+idx+'');
                            $("#printEtUbi").prop('disabled', false);  
                        });
                    }
                });*/
            }
            function genEtiqueta(){
                var etTipo = $('#etTip').val();
                var etCantidad = $('#etCantidad').val();
                var etNros = $('#etList').val();
                $.ajax({ 
                    url: 'requests/print.php', 
                    type: 'POST',
                    data: {
                        etCantidad:etCantidad,
                        etTipo:etTipo,
                        etNros:etNros,
                        impresion:'reex'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        $("#toPrint").html("");
                        var cntnt = '';
                        $.each(dt.data,function(idx, val){                            
                            cntnt = '<div id="code'+idx+'" style="page-break-after: always;"></div><br/>';
                            $("#toPrint").append(cntnt);
                            generateBarcode(val,'code128', 'code'+idx+'');
                            $("#printEt").prop('disabled', false);  
                        });
                    }
                });
            }
            function btnVlnSv(){
                var toSend = [];
                $.each($('input[id^="row_sel_"]'), function(){
                    var dd = $(this).attr('id');
                    var ddx = dd.split('_');
                    if($(this).is(':checked')){
//                        console.log(ddx[2]);
                        toSend.push({ "pos" : ddx[2], "anu" : "X"});
                    }else{
                        var pk_val = $('#row_pk_'+ddx[2]+'').val();
//                        console.log(ddx[2]+' '+pk_val);
                        toSend.push({ "pos" : ddx[2], "pk" : pk_val});
                    }
                });
                if(toSend.length > 0){
                    var entrega = $('#vlnHdnEntrega').val();
                    $.ajax({ 
                        url: 'vl02n_send.php', 
                        type: 'POST',
                        data: {
                            entrega:entrega,
                            codigos:toSend,
                            usuario:'<?php echo $_SESSION["user"] ?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            $('#smpl_tbl tbody').empty();
                            $('#tbl_cnt').toggle("fast","linear");
                            $("#btnPk").prop('disabled', true);  
                            $("#btnAnl").prop('disabled', true);  
                            $("#vlnEntrega").val('');
                            $("#vlnHdnEntrega").val('');  
                            $('#vl02nModal').modal('hide');
                        }
                    });
                }
            }
            function viewBoleta(){
                if(entsBl.length > 0){
                    $.ajax({ 
                        url: 'view_boleta.php', 
                        type: 'POST',
                        data: {
                            codigos:entsBl,
                            usuario:'<?php echo $_SESSION["user"] ?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            var fg = 0;
                            if(dt.err == 1){
                                alert(dt.msg);                                
                            }else{                             
                                var cnnt = '';
                                var trans = $('#blTransporte').val();
                                $('#blcontent').empty();
                                cnnt += '<div id="accordion1" class="panel-group accordion">';
                                        
                                $.each(dt.dat,function(idx, val){
//                                    if(val.cabecera.manif){
                                        var dt = getDateTime();
                                        var dts = dt.split(' ');
                                        if(!trans){
                                            trans = val.cabecera.nom_trans
                                        }
                                        cnnt += '    <div class="panel panel-default">';
                                        cnnt += '            <div class="panel-heading" id="collapsePar'+idx+'">';
                                        cnnt += '                    <a style="cursor:pointer;text-decoration: none;" href="#collapse'+idx+'" data-parent="#accordion1" data-toggle="collapse" class="accordion-toggle collapsed">';
                                        cnnt += '                            Entrega #'+idx+'';
                                        cnnt += '                    </a>';
                                        cnnt += '            </div>';
                                        cnnt += '            <div class="panel-collapse collapse" id="collapse'+idx+'">';
                                        cnnt += '                    <div class="panel-body">';
                                                                    cnnt += '<div class="col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 12px;padding: 12px;border: 1px dashed #dcdcdc;" id="mnHdr'+idx+'">';
                                                                    cnnt += '<div>';
                                                                    cnnt += '<input autocomplete="on" type="hidden" id="inHdnEnt'+idx+'" value="'+val.cabecera.vkorg+'">';
                                                                        cnnt += '<table style="width: 100% !important;" class="table table-striped">';
                                                                            cnnt += '<thead >';                                                
                                                                                cnnt += '<tr>';
                                                                                    cnnt += '<th colspan="6" style="background-color: #FFF !important;text-align:center;font-size: 18px;"><b>Manifiesto de carga - '+val.cabecera.vkorg+' </b></th>';
                                                                                cnnt += '</tr>';
                                                                                cnnt += '<tr style="border-bottom: 2px solid #fff !important;">';
                                                                                    cnnt += '<th colspan="3" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:left;"><b>Boleta #'+val.cabecera.manif+' </b></th>';
                                                                                    cnnt += '<th colspan="3" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:right;">Fecha '+dts[0]+' - Hora '+dts[1]+'</th>';
                                                                                cnnt += '</tr>';
                                                                                cnnt += '<tr style="border-bottom: 2px solid #fff !important;">';
                                                                                    cnnt += '<th colspan="2" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;"> <b>Cliente: </b>'+val.cabecera.name1+'</th>';
                                                                                    cnnt += '<th colspan="2" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:center;"> <b>Ciudad: </b>'+val.cabecera.bezei+'</th>';
                                                                                    cnnt += '<th colspan="2" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:right;"> <b>Entrega: </b>'+idx+'</th>';
                                                                                cnnt += '</tr>';
                                                                                cnnt += '<tr style="border-bottom: 2px solid #fff !important;">';
                                                                                    cnnt += '<th colspan="3" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:left;"><b>Transportista: '+trans+' </b></th>';
                                                                                    cnnt += '<th colspan="3" style="border-bottom: 2px solid #fff !important;background-color: #FFF !important;text-align:right;">Factura #'+val.cabecera.vbeln+'</th>';
                                                                                cnnt += '</tr>';
                                                                                cnnt += '<tr>';
                                                                                    cnnt += '<th colspan="6" style="text-align:left;background-color: #FFF !important;"><b>Dirección: </b>'+val.cabecera.stras+'</th>';
                                                                                cnnt += '</tr>';
                                                                            cnnt += '</thead>';
                                                                        cnnt += '</table>';
                                                                        cnnt += '<table style="width: 100% !important;" class="table table-striped">';
                                                                        cnnt += '<thead >';      
                                                                            cnnt += '<tr>';
                                                                                cnnt += '<th style="background-color: #FFF !important;text-align:left;"><b>Caja</b></th><th style="background-color: #FFF !important;text-align:left;"><b>Peso</b></th><th style="background-color: #FFF !important;text-align:left;"><b>Bultos</b></th><th style="background-color: #FFF !important;text-align:left;"><b>Ubicación</b></th><th style="background-color: #FFF !important;text-align:left;"><b>Descripción</b></th>';
                                                                            cnnt += '</tr>';
                                                                        cnnt += '</thead>';
                                                                        cnnt += '<tbody>'; 
                                                                        $.each(val.cajas, function(ix,vx){                                                 
                                                                            cnnt += '<tr>';
                                                                                cnnt += '<td>'+vx.caja+'</td><td>'+vx.peso+'</td><td>'+vx.bulto+'</td><td>'+vx.ubicacion+'</td><td>'+vx.descripcion+'</td>';
                                                                            cnnt += '</tr>';                                            
                                                                        });
                                                                        cnnt += '</tbody>'; 
                                                                        cnnt += '</table>';
                                                                         cnnt += '<div style="clear:both;"></div><br/><br/><br/><br/>';
                                                                        cnnt += '<table style="width: 100% !important; martin-top: 50px !important;">';
                                                                        cnnt += '<tbody>'; 
                                                                            cnnt += '<tr>';
                                                                                cnnt += '<td>Forma de pago:</td><td>Crédito:____</td><td>Contado:____</td><td>Destino:____</td><td style="text-align:right;">Total Gs.:_______________________</td>';
                                                                            cnnt += '</tr>';
                                                                        cnnt += '</tbody>'; 
                                                                        cnnt += '</table>'; 
                                                                        cnnt += '<div style="clear:both;"></div> <br/><br/><table style="width: 90% !important;">';
                                                                        cnnt += '<tbody>';  
                                                                            cnnt += '<tr style="border-top:70px solid #FFF;border-bottom:20px solid #FFF"">';
                                                                                cnnt += '<td style="text-align:center">___________________________</td><td style="text-align:center">___________________________</td><td style="text-align:center">___________________________</td>';
                                                                            cnnt += '</tr>';
                                                                            cnnt += '<tr style="border-bottom:20px solid #FFF; margin-bottom:20px !important">';
                                                                                cnnt += '<td style="text-align:center">Remitente</td><td style="text-align:center">Transporte</td><td style="text-align:center">Destinatario</td>';
                                                                            cnnt += '</tr>';
                                                                            cnnt += '<tr style="border-bottom:20px solid #FFF">';
                                                                                cnnt += '<td style="text-align:left; ">Aclaración:________________</td><td style="text-align:left">Aclaración:__________________</td><td style="text-align:left">Aclaración:__________________</td>';
                                                                            cnnt += '</tr>';                                                                      
                                                                            cnnt += '<tr>';
                                                                                cnnt += '<td style="text-align:left">C.I.Nro.:__________________</td><td style="text-align:left">C.I.Nro.:____________________</td><td style="text-align:left">C.I.Nro.:____________________</td>';
                                                                            cnnt += '</tr>';
                                                                        cnnt += '</tbody>'; 
                                                                        cnnt += '</table>';
                                                                    cnnt += '</div>';
                                                                    cnnt += '</div>';
                                                                    cnnt += '<div style="clear:both;"></div>';
                                        cnnt += '                    </div>';
                                        cnnt += '            </div>';
                                        cnnt += '    </div>';
                                        
//                                    }else{
//                                        fg = 1;
//                                        alert('Entrega sin manifiesto');
//                                    }
                                });
                                
                                cnnt += '</div>';
//                                console.log(cnnt);
                                $('#blcontent').append(cnnt);
                            }
                            if(fg == 0){
                                $("#printBl").prop('disabled', false); 
                            }
                            
                        }
                    });
                }else{
                    alert("Debe ingresar una entrega");
                }
            }
            function btnSvBoleta(){
                var mnChofer = $('#entsBl').val();
                var mnChapa = $('#blChapa').val();
                var mnObs  = $('#blObs').val();
//                console.log(entsMn);
                    if(entsBl.length > 0){
                        $.ajax({ 
                            url: 'save_mn_entrega.php', 
                            type: 'POST',
                            data: {
                                codigos:entsBl,
                                mnChofer:mnChofer,
                                mnChapa:mnChapa,
                                mnObs:mnObs,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                if(dt.err == 0){
                                    $('#blNroManifiesto').val(dt.dat);
                                    $("#previewBl").prop('disabled', false);
//                                    $("#printBl").prop('disabled', false);
                                    $("#clsBtnMn").prop('disabled', true);                                    
                                }else{
                                    alert(dt.msg);
                                    $("#printBl").prop('disabled', true);
                                }
                            }
                        });
                    }else{
                        alert("Debe ingresar una entrega");
                    }
            }
            function btnSvManifiesto(){
                var mnChofer = $('#mnChofer').val();
                var mnChapa = $('#mnChapa').val();
                var mnObs  = $('#mnObs').val();
//                console.log(entsMn);
                    if(entsMn.length > 0){
                        $.ajax({ 
                            url: 'save_mn_entrega.php', 
                            type: 'POST',
                            data: {
                                codigos:entsMn,
                                mnChofer:mnChofer,
                                mnChapa:mnChapa,
                                mnObs:mnObs,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                if(dt.err == 0){
                                    $('#mnNroManifiesto').val(dt.dat);
                                    $("#printMn").prop('disabled', false);
                                    $("#clsBtnMn").prop('disabled', true);                                    
                                }else{
                                    alert(dt.msg);
                                }
                            }
                        });
                    }else{
                        alert("Debe ingresar una entrega");
                    }
            }
            function clsManifiesto(){
                entsMn = [];
                $('#mncontent').empty();
                $('#prMnArea').empty();
            }
            function getDateTime() {
                var now     = new Date(); 
                var year    = now.getFullYear();
                var month   = now.getMonth()+1; 
                var day     = now.getDate();
                var hour    = now.getHours();
                var minute  = now.getMinutes();
                var second  = now.getSeconds(); 
                if(month.toString().length == 1) {
                     month = '0'+month;
                }
                if(day.toString().length == 1) {
                     day = '0'+day;
                }   
                if(hour.toString().length == 1) {
                     hour = '0'+hour;
                }
                if(minute.toString().length == 1) {
                     minute = '0'+minute;
                }
                if(second.toString().length == 1) {
                     second = '0'+second;
                }   
                var dateTime = day+'/'+month+'/'+year+' '+hour+':'+minute+':'+second;   
                 return dateTime;
            }
            function generateBarcodeX(entrega, label, code, id){
                var value = entrega;
                var btype = code;
                var renderer = 'css';
                var dat = {
                    code: value, 
                    crc:false
                };
                var settings = {
                  output:renderer,
                  bgColor: '#FFFFFF',
                  color: '#000000',
                  showHRI: false,
                  barWidth: 2,
                  barHeight: 80,
                  marginHRI: 5,
                  fontSize: 30
                };
                $("#"+id).html("").show().append(label).barcode(dat, btype, settings).append('<div style="clear:both;"></div><div style="position:relative;width:95%;padding:5px;text-align:center;font-size:24px;overflow:none;">'+label+'</div>');
            }
            function generateBarcode(entrega, code, id){
                var value = entrega;
                var btype = code;
                var renderer = 'css';
                var dat = {
                    code: value, 
                    crc:false
                };
                var settings = {
                  output:renderer,
                  bgColor: '#FFFFFF',
                  color: '#000000',
                  showHRI: true,
                  barWidth: 2,
                  barHeight: 80,
                  margin: 15,
                  fontSize: 30
                };
                $("#"+id).html("").show().barcode(dat, btype, settings);
            }
            function generateBarcodeBlk(entrega, code, id, options){
                var value = entrega;
                var btype = code;
                var renderer = 'css';
                var dat = {
                    code: value, 
                    crc:false
                };
                var settings = options;
                $("#"+id).html("").show().barcode(dat, btype, settings);
            }
            function generateBarcodePed(entrega, code, id){
                var value = entrega;
                var btype = code;
                var renderer = 'css';
                var dat = {
                    code: value, 
                    crc:false
                };
                var settings = {
                  output:renderer,
                  bgColor: '#FFFFFF',
                  color: '#000000',
                  showHRI: false,
                  barWidth: 2,
                  barHeight: 50,
                  margin: 15,
                  fontSize: 20
                };
                $("#"+id).html("").show().barcode(dat, btype, settings);
            }
            function impEtiquetTra(){
                var etTrEp = $('#etTrEMP').val();
                var etTrEntrega = $('#etTrEntrega2').val();
                var etTrCant = $('#etTrCant').val();
                var etTrTANSP = $('#etTrTANSP').val();
                var etTrCIUDAD = $('#etTrCIUDAD').val();
                var etTrLGORT = $('#etTrLGORT').val();
                var etTrRESLO = $('#etTrRESLO').val();
                var etTrBUKRS = $('#etTrBUKRS').val();
                var etTrLGOBE = $('#etTrLGOBE').val();
                var etTrBULKS = $('#etTrBULKS').val();
                var bultos = 0;
                if(!etTrCant){
                    bultos = parseInt(etTrBULKS);
                }else{
                    bultos = parseInt(etTrCant);
                }
                
                var cntnt = '';
                var ct = '';
                var bars = [];
                for(var i = 0; i <= bultos; i++){
                    if(i == 0){
                        ct = 'TRANSFERENCIA';
                    }else{
                        ct = i+'/'+bultos;
                    }
                    cntnt += '<div class="saltoP" style="width:90% !important" id="prEtiquetTra'+i+'">'+
                                    '<div style="width:400px !important; height: 160px !important; display:inline-block !important; margin-top: 0px !important; margin-left: 20px !important; overflow:hidden; "id="vwEtiquetTra'+i+'">'+
                                    
                                        '<div id="etTrpvPrev'+i+'">                                        '+
                                        '    <div class="vcard v1">'+
                                        '        <ul>'+
                                        '            <li class="v-heading" id="etTrpvCliente'+i+'">'+etTrEp+' - '+etTrLGORT+
                                        '            </li>                                                '+
                                        '            <li>'+
                                        '                    <div class="v-heading" id="etTrpvLGOBE'+i+'">'+etTrLGOBE+'</div>'+
                                        '            </li>'+                                        
                                        '            <li>'+
                                        '                    <span class="item-key">Transporte</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvTANSP'+i+'">'+etTrTANSP+'</div>'+
                                        '            </li>'+
                                        '            <li>'+
                                        '                    <span class="item-key">Ciudad</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvCIUDAD'+i+'">'+etTrCIUDAD+'</div>'+
                                        '            </li>'+
                                        '            <li>'+
                                        '                    <span class="item-key">Alm. Emisor</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvRESLO'+i+'">'+etTrRESLO+'</div>'+
                                        '            </li>'+
                                        '            <li>'+
                                        '                    <span class="item-key">Nro. Pedido</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvBUKRS'+i+'">'+etTrEntrega+'</div>'+
                                        '            </li>'+
                                        '            <li>'+
                                                            '<span class="item-key">FECHA</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvFecha'+i+'">'+getDateTime()+'</div>'+
                                        '            </li>'+
                                        '            <li>'+
                                        '                    <span class="item-key">C.BULTOS</span>'+
                                        '                    <div class="vcard-item" style="margin-left: 78px !important;" id="etTrpvBulto'+i+'">'+ct+'</div>'+
                                        '            </li>'+
                                        '        </ul> '+
                                        '    </div>'+
                                        '    <div id="barcodeTr'+i+'" class="barcode2"></div>'+
                                        '</div>'+
                                    '</div>'+
                               ' </div>';
                    bars.push('barcodeTr'+i);
                    
                }
                
//                generateBarcode(etTrEntrega, 'barcode'+i);
//                console.log(cntnt);
                $('#toPrintTr').empty().append(cntnt);
                $.each(bars, function(ind, val){
                    generateBarcode(etTrEntrega, val);
                });
                $('#etTransfModal').modal('hide');
                printDiv('toPrintTr');
            }
            
            function impEtUb(){
                printDiv('toPrintUbi');
            }
            function impEtReEx(){
                printDiv('toPrint');
            }
            function printEtEanBlk(){
                printDiv('toPrintEanBlk');
            }
            
            function impEtEan(){
                printDiv('toPrintEan');
            }
            function impEtPed(){
                var pedido = $("#pedID").val();
//                console.log(pedido);
                printDivEx('toPrintPed',pedido);
            }
            function impEtiqueta(){
                var etCantidad = $('#etCantidad').val();
                var bultos = parseInt(etCantidad);
                var etEntrega = $('#etEntrega').val();
                var etCliente = $('#etCliente').val();
                var etCodCliente = $('#etCodCliente').val();
                var etLocalidad = $('#etLocalidad').val();
                var etZonaEnvio = $('#etZonaEnvio').val();
                var etTransporte = $('#etTransporte').val();
                var etEmpresa = $('#etEmpresa').val();
                var cntnt = '';
                var bars = [];
                for(var i = 1; i <= bultos; i++){
                    cntnt += '<div class="saltoP" style="width:90% !important" id="prEtiqueta'+i+'">'+
                                    '<div style="width:360px !important; height: 160px !important; display:inline-block !important; margin-left: 10px !important; overflow:hidden; "id="vwEtiqueta'+i+'">'+
                                        '<div id="etPrev'+i+'">' +
                                            '<div class="vcard v1">'+
                                                '<ul>'+
                                                    '<li class="v-heading" id="etpvCliente'+i+'">'+etCliente+'</li>'+
                                                    '<li>'+
                                                    '        <span class="item-key">TRANSP.</span>'+
                                                    '        <div class="vcard-item" id="etpvTransp'+i+'">'+etTransporte+'</div>'+
                                                    '</li>'+
                                            '        <li>'+
                                            '                <span class="item-key">ZONA E.</span>'+
                                            '                <div class="vcard-item" id="etpvZona'+i+'">'+etZonaEnvio+'</div>'+
                                            '        </li>'+
                                                    '<li>'+
                                                            '<span class="item-key">CIUDAD</span>'+
                                                            '<div class="vcard-item" id="etpvCiudad'+i+'">'+etLocalidad+'</div>'+
                                                    '</li>'+
                                            '        <li>'+
                                            '                <span class="item-key">ENTREGA</span>'+
                                            '                <div class="vcard-item" id="etpvEntrega'+i+'">'+etEntrega+'</div>'+
                                            '        </li>'+
                                            '        <li>'+
                                            '                <span class="item-key">COD.CLI</span>'+
                                            '                <div class="vcard-item" id="etpvCodCLi'+i+'">'+etCodCliente+'</div>'+
                                            '        </li>'+
                                            '        <li>'+
                                            '                <span class="item-key">FECHA</span>'+
                                            '                <div class="vcard-item" id="etpvFecha'+i+'">'+getDateTime()+'</div>'+
                                            '        </li>'+
                                            '        <li>'+
                                            '                <span class="item-key">C.BULTOS</span>'+
                                            '                <div class="vcard-item" id="etpvBulto'+i+'">'+i+'/'+etCantidad+'</div>'+
                                            '        </li>'+
                                           '     </ul> '+
                                            '</div>'+
                                            '<div id="barcode'+i+'" class="barcode"></div>'+
                                            '<div class="vcard v3">'+
                                            '    <ul>'+
                                            '        <li class="v-heading" id="etpvEmpresa'+i+'">'+
                                            '            '+etEmpresa+''+
                                            '        </li>'+
                                            '    </ul>                                            '+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                               ' </div>';
                    bars.push('barcode'+i);
                    
                }
                
//                generateBarcode(etEntrega, 'barcode'+i);
//                console.log(cntnt);
                $('#toPrint').empty().append(cntnt);
                $.each(bars, function(ind, val){
                    generateBarcode(etEntrega, val);
                });
                $('#etiquetaModal').modal('hide');
                printDiv('toPrint');
            }
            function printDiv(id){
                var printContents = document.getElementById(id).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload();
            }
            
            function printDivEx(id, doc){
                var printContents = document.getElementById(id).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                
                $.ajax({ 
                    url: 'requests/savePrinted.php', 
                    type: 'POST',
                    data: {
                        pedrefer:doc
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        $("#toPrintPed").empty();
                        
                    }
                });
                location.reload();
            }
            function printDivMn(id){
                var printContents = document.getElementById(id).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
            function impManifiesto(){
//                console.log(entsMn);
                
                var mnNroManifiesto = $('#mnNroManifiesto').val();
                var mnChofer = $('#mnChofer').val();
                var mnChapa = $('#mnChapa').val();
                var mnObs  = $('#mnObs').val();
                var user = '<?php echo $_SESSION["user"] ?>';
//                
//                
                var cnnt = '';
                    cnnt += '<div class="saltoP2 col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 12px;padding: 12px;border: 1px dashed #dcdcdc;" id="mnHdr">';
                        cnnt += '<table style="width: 100% !important;" class="table table-striped">';
                            cnnt += '<thead>';
                                cnnt += '<tr>';
                                    cnnt += '<th colspan="6"  style="font-size: 18px;text-align:center;"><b>Manifiesto #'+mnNroManifiesto+' </b></th>';
                                cnnt += '</tr>';
                                cnnt += '<tr>';
                                    cnnt += '<th colspan="2"  style="text-align:left;font-size: 18px;"> <b>Chofer: </b>'+mnChofer+'</th>';
                                    cnnt += '<th colspan="2"  style="text-align:center;font-size: 18px;"> <b>Chapa: </b>'+mnChapa+'</th>';
                                    cnnt += '<th colspan="2"  style="text-align:right;font-size: 18px;"> <b>Fecha: </b>'+getDateTime()+'</th>';
                                cnnt += '</tr>';
                                cnnt += '<tr>';
                                    cnnt += '<th colspan="6"  style="text-align:left;font-size: 18px;"><b>Observaciones: </b>'+mnObs+'</th>';
                                cnnt += '</tr>';
                            cnnt += '</thead>';
                        cnnt += '</table>';
                    cnnt += '</div>';
                    cnnt += '<div style="clear:both;"></div>';
                    
                    $('#prMnArea').empty().append(cnnt);
                    var printContents = document.getElementById('mncontent').innerHTML;
                    $('#mnHdr').append(printContents);
                    setLevel(entsMn,mnChofer,mnChapa,mnObs,mnChofer);
                    printDiv('prMnArea');
                    
                    $("#clsBtnMn").prop('disabled', false);
            }
            function setLevel(entsMn,mnChofer,mnChapa,mnObs,msUser){
                $.ajax({ 
                    url: 'save_mn_lvlfr.php', 
                    type: 'POST',
                    data: {
                        codigos:entsMn,
                        mnChofer:mnChofer,
                        mnChapa:mnChapa,
                        mnObs:mnObs,
                        usuario:msUser
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 0){
                            $('#mnNroManifiesto').val(dt.dat);
                            $("#printMn").prop('disabled', false);
                            $("#clsBtnMn").prop('disabled', true);                                    
                        }else{
                            alert(dt.msg);
                        }
                    }
                });
            }
//            function printDivMn(id){
//                var printHeader = document.getElementById('mnheader').innerHTML;
//                var printContents = document.getElementById('mncontent').innerHTML;
//                var originalContents = document.body.innerHTML;
//                document.body.innerHTML = printHeader+printContents;
//                window.print();
//                document.body.innerHTML = originalContents;
//                location.reload();
//            }
            function viewEtiquetTra(){
                var etTrEntrega = $('#etTrEntrega2').val();               
                var etTrCant = $('#etTrCant').val();               

                var etTrTANSP = $('#etTrTANSP').val();
                var etTrCIUDAD = $('#etTrCIUDAD').val();
                var etTrLGORT = $('#etTrLGORT').val();
                var etTrRESLO = $('#etTrRESLO').val();
                var etTrBUKRS = $('#etTrBUKRS').val();
                var etTrLGOBE = $('#etTrLGOBE').val();
                var etTrBULKS = $('#etTrBULKS').val();
                if(!etTrCant){
                    bultos = parseInt(etTrBULKS);
                }else{
                    bultos = parseInt(etTrCant);
                }
                var etTrEMP = $('#etTrEMP').val();
                
                $("#etTrpvCliente").empty().append(etTrEMP+' - '+etTrLGORT);
                $("#etTrpvTANSP").empty().append(etTrTANSP);
                $("#etTrpvCIUDAD").empty().append(etTrCIUDAD);
                $("#etTrpvRESLO").empty().append(etTrRESLO);
                $("#etTrpvBUKRS").empty().append(etTrEntrega);
                $("#etTrpvLGOBE").empty().append(etTrLGOBE);
                $("#etTrpvFecha").empty().append(getDateTime());
                $("#etTrpvBulto").empty().append(bultos+'/'+bultos);
//                $("#etTrpvEmpresa").empty().append(etTrEMP);
//                if(parseInt(etTrCantidad) > 0){
                    $("#printEtTr").prop('disabled', false);  
//                }                
                generateBarcode(etTrEntrega, 'barcodeTr');
                if($('div[id="vwEtiquetTra"]').hasClass('hddn')){
                    $('div[id="vwEtiquetTra"]').removeClass('hddn')
                }
            }
            function viewEtiqueta(){
                var etEntrega = $('#etEntrega').val();
                var etCantidad = $('#etCantidad').val();
                var etCliente = $('#etCliente').val();
                var etCodCliente = $('#etCodCliente').val();
                var etLocalidad = $('#etLocalidad').val();
                var etZonaEnvio = $('#etZonaEnvio').val();
                var etTransporte = $('#etTransporte').val();
                var etEmpresa = $('#etEmpresa').val();
                
                $("#etpvCliente").empty().append(etCliente);
                $("#etpvCiudad").empty().append(etLocalidad);
                $("#etpvTransp").empty().append(etTransporte);
                $("#etpvEntrega").empty().append(etEntrega);
                $("#etpvZona").empty().append(etZonaEnvio);
                $("#etpvCodCLi").empty().append(etCodCliente);
                $("#etpvFecha").empty().append(getDateTime());
                $("#etpvBulto").empty().append(etCantidad+"/"+etCantidad);
                $("#etpvEmpresa").empty().append(etEmpresa);
                if(parseInt(etCantidad) > 0){
                    $("#printEt").prop('disabled', false);  
                }                
                generateBarcode(etEntrega, 'barcode');
                if($('div[id="vwEtiqueta"]').hasClass('hddn')){
                    $('div[id="vwEtiqueta"]').removeClass('hddn')
                }
            }
            function removeEntBl(Ent){
                if(jQuery.inArray( Ent, entsBl ) > -1){
                    entsBl.splice( $.inArray(Ent, entsBl), 1 );
                    $("#collapsePar"+Ent).remove();
                    $("#collapse"+Ent).remove();
                    var liEl = '';
                    $.each(entsBl, function(ind, val){
                        liEl += '<li>'+val+'<small onclick="removeEntBl('+"'"+val+"'"+')" class="blInClose"><i class="splashy-remove"></i></small></li>'
                    });
                    if(entsBl.length == 0){
                        $("#previewBl").prop('disabled', true);
                        $("#accordion1").remove();
                    }
                    $(".blEntTbl").empty().append(liEl);                    
                } 
            }
            
            $('input[id="btTransf"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
//                    console.log(entsBl);
                    var input = $('#btTransf').val();
                    if(input.length > 10){
                        alert('El dato ingresado no es válido');
                    }else{
                        $.ajax({ 
                            url: 'get_bultos.php', 
                            type: 'POST',
                            data: {
                                codigo:input,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                var cntnt = '';
                                if(dt.err == 0){
                                    cntnt = '<table class="table table-striped table-responsive">';
                                    cntnt += '<thead>';
                                    cntnt += '<tr>';
                                    cntnt += '<th>Entrega</th>';
                                    cntnt += '<th>Caja</th>';
                                    cntnt += '<th>Peso</th>';
                                    cntnt += '<th>Ubicación</th>';
                                    cntnt += '<th>Bultos</th>';
                                    cntnt += '<th>Descripcion</th>';
                                    cntnt += '<th>Accion</th>';
                                    cntnt += '</tr>';
                                    cntnt += '</thead>';
                                    cntnt += '</tbody>';
                                    cntnt += '<tbody>';
                                    $.each(dt.dat, function(ind, vals){
                                        cntnt += '<tr id="'+vals.ca_id+'">';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" disabled="disabled" class="form-control" type="text" value="'+vals.ca_emp+'" id="'+vals.ca_id+'_ica_emp"/></td>';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" disabled="disabled" class="form-control" type="text" value="'+vals.ca_caja+'" id="'+vals.ca_id+'_ica_caja"/></td>';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" class="form-control" type="text" value="'+vals.ca_peso+'" id="'+vals.ca_id+'_ica_peso"/></td>';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" class="form-control" type="text" value="'+vals.ca_ubi+'" id="'+vals.ca_id+'_ica_ubi"/></td>';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" class="form-control" type="text" value="'+vals.ca_bulto+'" id="'+vals.ca_id+'_ica_bulto"/></td>';
                                        cntnt += '<td><input autocomplete="on" style="font-size:10px !important" class="form-control" type="text" value="'+vals.ca_desc+'" id="'+vals.ca_id+'_ica_desc"/></td>';
                                        cntnt += '<td><button type="button" onclick="gdBox('+vals.ca_id+",'"+vals.ca_emp+"'"+",'"+vals.ca_caja+"'"+')" class="btn btn-default"><i class="splashy-okay"></i></button></td>';
                                        cntnt += '</tr>';
                                    });
                                    cntnt += '</tbody>';
                                    cntnt += '</table>';
                                    $('#btTransf').val('');
                                    $('#btContents').empty().append(cntnt);
                                }else{
                                    alert(dt.msg);
                                }
                            }
                        });                 
                    }
                    
                }
            });
            function clsBulto(){
                $('#btContents').empty();
            }
            function getTransp(entrega, tipo){
                var result = '';
                $.ajax({ 
                    url: 'view_transp.php', 
                    type: 'POST',
                    data: {
                        codigo:entrega,
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(tipo = 'bl'){
                            $("#blTransporte").val(dt.trs);
                        }else{
                            $("#mnChofer").val(dt.trs);
                        }
                    }
                });
            }
            function gdBox(id){
                
                
                var ica_emp = $("#"+id+"_ica_emp").val();
                var ica_mat = $("#"+id+"_ica_mat").val();
                var ica_caja = $("#"+id+"_ica_caja").val();
                var ica_peso = $("#"+id+"_ica_peso").val();
                var ica_ubi = $("#"+id+"_ica_ubi").val();
                var ica_bulto = $("#"+id+"_ica_bulto").val();
                var ica_desc = $("#"+id+"_ica_desc").val();
                $.ajax({ 
                    url: 'save_bultos.php', 
                    type: 'POST',
                    data: {
                        id:id,
                        ica_emp:ica_emp,
                        ica_caja:ica_caja,
                        ica_peso:ica_peso,
                        ica_ubi:ica_ubi,
                        ica_bulto:ica_bulto,
                        ica_desc:ica_desc,
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                    }
                });  
            }
            $('input[id="blEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
//                    console.log(entsBl);
                    var input = $('#blEntrega').val();
                     
                     
                    if(input.length > 10){
                        alert('El dato ingresado no es válido');
                    }else{
                        if(jQuery.inArray( input, entsBl ) == -1){
                            entsBl.push(padLeft(input,10));
                            getTransp(input,'bl');
                            var liEl = '';
                            $.each(entsBl, function(ind, val){
                                liEl += '<li>'+val+'<small onclick="removeEntBl('+"'"+val+"'"+')" class="blInClose"><i class="splashy-remove"></i></small></li>'
                            });
                            $(".blEntTbl").empty().append(liEl); 
                            $('#blEntrega').val('');
                            
                            if(entsBl.length > 0){
                                
                                $("#svBl").prop('disabled', false);
                            }                            
                        }else{
                            alert('Número de Entrega ya ingresado');
                        }                        
                    }
                    
                }
            });
            $('input[id="inputEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    sndEntrega();
                }
            });
            $('input[id="etEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var ent = $('#etEntrega').val();
                    if(ent != ''){
                        $.ajax({ 
                            url: 'get_dt_entrega.php', 
                            type: 'POST',
                            data: {
                                codigo:ent,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                $("#etCantidad").val(dt.cant);
                                $("#etCodCliente").val(dt.dat.kunnr);
                                $("#etCliente").val(dt.dat.name1);
                                $("#etLocalidad").val(dt.dat.bezei);
                                $("#etZonaEnvio").val(dt.dat.vsbed);
                                $("#etTransporte").val(dt.dat.lifnr); 
                                $("#etEmpresa").val(dt.dat.vkorg);                                 
                                $("#previewEt").prop('disabled', false);  
                                if(!$('div[id="vwEtiqueta"]').hasClass('hddn')){
                                    $('div[id="vwEtiqueta"]').addClass('hddn')
                                }
                            }
                        });
                    }else{
                        alert('Debe de ingresar un número de entrega');
                    }
                }
            });
            $('input[id="etTrEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var ent = $('#etTrEntrega').val();
                    if(ent != ''){
                        $.ajax({ 
                            url: 'getTr_dt_entrega.php', 
                            type: 'POST',
                            data: {
                                codigo:ent,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                if(dt.err == 0){
                                    $("#etTrEntrega").val(dt.dat.ebeln);
                                    $("#etTrEntrega2").val(dt.dat.ebeln); 
                                    $("#etTrCant").val(dt.dat.bultos);
                                    $("#etTrCant2").val(dt.dat.bultos); 
                                    $("#etTrLGORT").val(dt.dat.lgort);
                                    $("#etTrRESLO").val(dt.dat.reslo);
                                    $("#etTrBUKRS").val(dt.dat.bukrs);
                                    $("#etTrLGOBE").val(dt.dat.lgobe);
                                    $("#etTrBULKS").val(dt.dat.bultos);
                                    $("#etTrEMP").val(dt.dat.emp);
                                    $("#previewEtTr").prop('disabled', false);  
                                    if(!$('div[id="previewEtTr"]').hasClass('hddn')){
                                        $('div[id="vwEtiquetTra"]').addClass('hddn')
                                    }
                                }else{
                                    alert(dt.msg);
                                }
                                
                            }
                        });
                    }else{
                        alert('Debe de ingresar un número de entrega');
                    }
                }
            });
            $(document).on('change', 'input[id^="row_pk_"]', function(e) {
                var id = $(this).attr('id');
                var idx = id.split('_');
                if($(this).val() < 0){
                    $(this).val(0);
                }
                if($(this).val() > $('#row_ct_'+idx[2]).val()){
                    $(this).val($('#row_ct_'+idx[2]).val());
                }
                
            });
            $(document).on('change', 'input[id="select_rows_all"]', function(e) {
                var id = $(this).attr('id');
                if($('input[id="select_rows_all"]').is(':checked')){
                    $.each($('input[id^="row_sel_"]'), function(){
    //                    console.log($(this).attr('id'));

                        var dd = $(this).attr('id');
                        var ddx = dd.split('_');
//                        console.log(ddx[0]);
                        $('#row_pk_'+ddx[2]).prop('disabled', true);
                        $(this).prop('checked', true);

                    });
                }else{
                    $.each($('input[id^="row_sel_"]'), function(){
    //                    console.log($(this).attr('id'));
                    
                        var dd = $(this).attr('id');
                        var ddx = dd.split('_');
    //                        console.log(ddx[0]);
                        $('#row_pk_'+ddx[2]).prop('disabled', false);
                        $(this).prop('checked', false);

                    });
                }
            });
            $(document).on('change', 'input[id^="row_sel_"]', function(e) {
                var id = $(this).attr('id');
                var idx = id.split('_');
                if($('#row_pk_'+idx[2]).is(':disabled')){
                    $('input[id="select_rows_all"]').prop('checked', false);
                    $('#row_pk_'+idx[2]).prop('disabled', false);
                }else{ 
                    $('#row_pk_'+idx[2]).prop('disabled', true);
                }
            });
            $('input[id="vlnEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    btnVln();
                }
            });
            $('input[id="mnEntrega"]').on('keydown', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    var ent = $('#mnEntrega').val();
                    if(ent != ''){
                        
                        $.ajax({ 
                            url: 'get_mn_entrega.php', 
                            type: 'POST',
                            data: {
                                codigo:ent,
                                usuario:'<?php echo $_SESSION["user"] ?>'
                            },
                            success: function (data) {
                                var dt = JSON.parse(data);
                                if(dt.err == 0){
                                    var cnnt = '';
                                    var tbul = 0;
                                    cnnt += '<div class="saltoP col-lg-12 col-sm-12 col-xs-12" style="margin-bottom: 12px;padding: 12px;border: 1px dashed #dcdcdc;" id="dv'+dt.cod+'">';
                                    cnnt += '<button type="button" id="lebtn" class="close" onclick="closeDiv('+"'"+'dv'+dt.cod+"'"+','+"'"+dt.cod2+"'"+')"><span aria-hidden="true">&times;</span></button>';
                                        cnnt += '<table style="width: 100% !important;" class="table table-striped">';
                                            cnnt += '<thead  >';
                                                cnnt += '<tr >';
                                                    cnnt += '<th  style="text-align:left; !important" colspan="4">'+dt.dat.name1+'</th>';
                                                    cnnt += '<th  style="text-align:right; !important" colspan="1">'+dt.dat.vkorg+'</th>';
                                                cnnt += '</tr>';
                                                cnnt += '<tr >';
                                                    cnnt += '<th style="text-align:left; !important" >Entrega</th><th style="text-align:left; !important" >Peso</th><th >Descripción</th><th style="text-align:left; !important" >Ubicación</th><th style="text-align:left; !important" >Bultos</th>';
                                                cnnt += '</tr>';
                                            cnnt += '</thead>';
                                            cnnt += '<tbody>';
                                            $.each(dt.cajas, function(id, vl){
                                                tbul += parseInt(vl.ca_bulto);
                                                cnnt += '<tr>';
                                                    cnnt += '<td>'+vl.ca_emp+'</td><td>'+vl.ca_peso+'</td><td>'+vl.ca_desc+'</td><td>'+vl.ca_ubi+'</td><td>'+vl.ca_bulto+'</td>';
                                                cnnt += '<tr>';
                                            });   
                                            cnnt += '<tr>';
                                                cnnt += '<td style="text-align:right;" colspan="4">Total Bultos</td><td>'+tbul+'</td>';
                                            cnnt += '<tr>';
                                            cnnt += '</tbody>';
                                        cnnt += '</table>';
                                    cnnt += '</div><br/>';
                                    cnnt += '<div style="clear:both;"></div>';
                                    getTransp(ent, 'tr');
//                                    $('#mnChofer').val(trans);
                                    $('#mncontent').append(cnnt);
                                    $('#mnEntrega').val('');
                                    entsMn.push(padLeft(ent,10));
                                }else{
                                    alert(dt.msg);
                                }
                            }
                        });
                    }else{
                        alert('Debe de ingresar un número de entrega');
                    }
                }
            });
            function closeDiv(id, entrega){
                $('#mnEntrega').val('');
                $("#mnChofer").val('');
                $("#mnChapa").val('');
                $("#mnObs").val('');
                $("#"+id).remove();
                entsMn.splice($.inArray(entrega, entsMn), 1);
                $("#printMn").prop('disabled', true);
            }
            function clsEtiquetTra(){
                $('#etTrEntrega').val('');
                $("#etTrCantidad").val('');
                $("#etTrCliente").val('');
                $("#etTrLocalidad").val('');
                $("#etTrZonaEnvio").val('');
                $("#etTrTransporte").val('');
                if(!$('div[id="vwEtiquetTra"]').hasClass('hddn')){
                    $('div[id="vwEtiquetTra"]').addClass('hddn')
                }
                $("#previewEt").prop('disabled', true);
                $("#printEt").prop('disabled', true);
            }
            function clsEtiqueta(){
                $('#etEntrega').val('');
                $("#etCantidad").val('');
                $("#etCliente").val('');
                $("#etLocalidad").val('');
                $("#etZonaEnvio").val('');
                $("#etTransporte").val('');
                if(!$('div[id="vwEtiqueta"]').hasClass('hddn')){
                    $('div[id="vwEtiqueta"]').addClass('hddn')
                }
                $("#previewEt").prop('disabled', true);
                $("#printEt").prop('disabled', true);
            }
            
            $(document).ready(function() {
//                $('#mped').addClass('active');
//                $('#inputEntrega').focus();
//                loadHCPie();
//                loadHCPieEntrega();
//                loadHCPieTransf();
//                loadHCColumns()
//                setInterval(function(){
//                    loadHCPie();
//                    loadHCColumns()
//                    loadHCPieEntrega();
//                    loadHCPieTransf();
//                },300000);
                
            });
            function loadProdUsr(){
//                console.log('llamado');
                var usr = '<?php echo $_SESSION["user"] ?>';
               $.ajax({ 
                    url: 'productividad.php', 
                    type: 'POST',
                    data: {
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
//                        console.log('aqui '+dt.dat[usr].Pedidos);
                        $("#lblPed2").empty().append(dt.dat[usr].Pedidos);
                        $("#lblMat2").empty().append(dt.dat[usr].Materiales);
                        $("#lblCant2").empty().append(dt.dat[usr].Cantidad);
//                        ____________________________________________________________________________                        

                    }
                });
//                
            }
            function loadHCColumns(busqueda){
//                console.log('llamado');
               $.ajax({ 
                    url: `productividad.php${busqueda}`, 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
                        console.log(dt);
//                        ____________________________________________________________________________                        

                        var x_cat = [];
                        var x_values3 = [];
                        var x_totales = [];
                        for(var i=0; i < dt.usr.length; i++){
//                                    console.log('ext '+dt.extensiones[i]);
                             x_cat.push(dt.usr[i]);
                        }

                        $.each(dt.series, function(key, value){
                            var identi = 0;
                            $.map(dt.totales, function(v,k){
                                if(key == k){
                                    identi = v;
                                }
                            });
//                            x_totales.push({'name':key,'data':value});
                            if(key != 'Cantidad'){
                                x_values3.push({'name':key +' ['+identi+']','data':value});
                            }else{
                                x_values3.push({'name':key +' ['+identi+']','data':value, 'visible': false});
                            }
                            
                        });
//                        ____________________________________________________________________________
                        $('#gr_20').highcharts({
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'Productividad por Usuario'
                            },
                            subtitle: {
                                text: '<?php echo date('d-m-Y', strtotime('now')) ?>'
                            },
                            xAxis: {
                                categories: x_cat,
                                crosshair: true
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Cantidad'
                                }
                            },
                            tooltip: {
                                headerFormat: '<div style="width: 150px; white-space:normal;"><span style="font-size:10px">{point.key}</span><table>',
                                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                                    '<td style="padding:0"><b>{point.y:,.0f}</b></td></tr>',
                                footerFormat: '</table></div>',
                                shared: true,
                                useHTML: true
                            },
                            plotOptions: {
                                column: {
                                    dataLabels: {
                                        enabled: true
                                    },
                                    pointPadding: 0.2,
                                    borderWidth: 0
                                }
                            },
                            series: x_values3
                        });
                    }
                });
//                
            }
            function loadHCPie(){
//            #7cb5ec
//               $.ajax({ 
//                    url: 'adaia_pedidos.php', 
//                    type: 'POST',
//                    
//                    success: function (data) {
//                        var dt = JSON.parse(data);
//                        console.log(dt);
//                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
//                        $('#gr_1').highcharts({
//                            chart: {
//                                plotBackgroundColor: null,
//                                plotBorderWidth: null,
//                                plotShadow: false,
//                                type: 'pie'
//                            },
//                            title: {text:  'Total de pedidos: '+dt.total},
//                            tooltip: {
//                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
//                            },
//                            plotOptions: {
//                                pie: {
//                                    allowPointSelect: true,
//                                    cursor: 'pointer',
//                                    dataLabels: {
//                                        color:'black',
//                                        distance: -20,
//                                        formatter: function () {
//                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';
//
//                                        }
//                                    },
//                                    showInLegend: true
//                                }
//                            },
//                            series: [{
//                                name: 'Estado',
//                                colorByPoint: true,
//                                data: [{
//                                    name: 'Controlados ['+ dt.totalp+']',
//                                    y: dt.totalp
//                                }, {
//                                    name: 'Pendientes ['+ dif +']',
//                                    y: dif
//                                }]
//                            }],
//                            responsive: {
//                                rules: [{
//                                    condition: {
//                                        maxWidth: 500
//                                    },
//                                    chartOptions: {
//                                        legend: {
//                                            align: 'center',
//                                            verticalAlign: 'bottom',
//                                            layout: 'vertical'
//                                        },
//                                        yAxis: {
//                                            labels: {
//                                                align: 'bottom',
//                                                x: 0,
//                                                y: -5
//                                            },
//                                            title: {
//                                                text: null
//                                            }
//                                        },
//                                        subtitle: {
//                                            text: null
//                                        },
//                                        credits: {
//                                            enabled: false
//                                        }
//                                    }
//                                }]
//                            }
//                        });
//                    }
//                });
//                
            }
            function loadHCPieEntrega(){
//                console.log('llamado');
//               $.ajax({ 
//                    url: 'adaia_pedidos_ent.php', 
//                    type: 'POST',
//                    
//                    success: function (data) {
//                        var dt = JSON.parse(data);
//                        console.log(dt);
//                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
//                        $('#gr_4').highcharts({
//                            chart: {
//                                plotBackgroundColor: null,
//                                plotBorderWidth: null,
//                                plotShadow: false,
//                                type: 'pie'
//                            },
//                            title: {text:  'Total de Entregas: '+dt.total},
//                            tooltip: {
//                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
//                            },
//                            legend: {
//                                align: 'right',
//                                verticalAlign: 'bottom',
//                                layout: 'vertical'
//                            },
//                            plotOptions: {
//                                pie: {
//                                    allowPointSelect: true,
//                                    cursor: 'pointer',
//                                    dataLabels: {
//                                        color:'black',
//                                        distance: -20,
//                                        formatter: function () {
//                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';
//
//                                        }
//                                    },
//                                    showInLegend: true
//                                }
//                            },
//                            series: [{
//                                name: 'Estado',
//                                colorByPoint: true,
//                                data: [{
//                                    name: 'Controlados ['+ dt.totalp+']',
//                                    y: dt.totalp
//                                }, {
//                                    name: 'Pendientes ['+ dif +']',
//                                    y: dif
//                                }]
//                            }],
//                            responsive: {
//                                rules: [{
//                                    condition: {
//                                        maxWidth: 500
//                                    },
//                                    chartOptions: {
//                                        legend: {
//                                            align: 'center',
//                                            verticalAlign: 'bottom',
//                                            layout: 'vertical'
//                                        },
//                                        yAxis: {
//                                            labels: {
//                                                align: 'bottom',
//                                                x: 0,
//                                                y: -5
//                                            },
//                                            title: {
//                                                text: null
//                                            }
//                                        },
//                                        subtitle: {
//                                            text: null
//                                        },
//                                        credits: {
//                                            enabled: false
//                                        }
//                                    }
//                                }]
//                            }
//                        });
//                    }
//                });
//                
            }
            function loadHCPieTransf(){
//                console.log('llamado');
//               $.ajax({ 
//                    url: 'adaia_pedidos_tra.php', 
//                    type: 'POST',
//                    
//                    success: function (data) {
//                        var dt = JSON.parse(data);
//                        console.log(dt);
//                        var dif = parseInt(dt.total) - parseInt(dt.totalp);
//                        $('#gr_5').highcharts({
//                            chart: {
//                                plotBackgroundColor: null,
//                                plotBorderWidth: null,
//                                plotShadow: false,
//                                type: 'pie'
//                            },
//                            title: {text:  'Total de Transferencias: '+dt.total},
//                            tooltip: {
//                                pointFormat: '{series.name}: <b>{point.percentage:,.0f}%</b>'
//                            },
//                            legend: {
//                                align: 'right',
//                                verticalAlign: 'bottom',
//                                layout: 'vertical'
//                            },
//                            plotOptions: {
//                                pie: {
//                                    allowPointSelect: true,
//                                    cursor: 'pointer',
//                                    dataLabels: {
//                                        color:'black',
//                                        distance: -20,
//                                        formatter: function () {
//                                            if(this.percentage!=0)  return Math.round(this.percentage)  + '%';
//
//                                        }
//                                    },
//                                    showInLegend: true
//                                }
//                            },
//                            series: [{
//                                name: 'Estado',
//                                colorByPoint: true,
//                                data: [{
//                                    name: 'Controlados ['+ dt.totalp+']',
//                                    y: dt.totalp
//                                }, {
//                                    name: 'Pendientes ['+ dif +']',
//                                    y: dif
//                                }]
//                            }],
//                            responsive: {
//                                rules: [{
//                                    condition: {
//                                        maxWidth: 500
//                                    },
//                                    chartOptions: {
//                                        legend: {
//                                            align: 'center',
//                                            verticalAlign: 'bottom',
//                                            layout: 'vertical'
//                                        },
//                                        yAxis: {
//                                            labels: {
//                                                align: 'bottom',
//                                                x: 0,
//                                                y: -5
//                                            },
//                                            title: {
//                                                text: null
//                                            }
//                                        },
//                                        subtitle: {
//                                            text: null
//                                        },
//                                        credits: {
//                                            enabled: false
//                                        }
//                                    }
//                                }]
//                            }
//                        });
//                    }
//                });
//                
            }
            $(document).on('shown.bs.modal', function(e) {
                $('input:visible:enabled:first', e.target).focus();
            });
            function swModal(id){
                $('#'+id).modal('show');
//                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
            }
            function swModalEx(id, doc = ''){
                $("#pedID").val(doc);
                $('#'+id).modal('show');
//                $( ".modal.in > .modal-dialog > .modal-content  > .modal-body .form input:first-of-type" ).focus();
            }
            function sndEntrega(){
                var entrega = $("#inputEntrega").val();
//                alert("aprobar "+entrega);
                $.ajax({ 
                    url: 'get_entrega.php', 
                    type: 'POST',
                    data: {
                        codigo:entrega,
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        if(dt.err == 1){
                            alert(dt.msg);
                        }else{             
                            window.location = 'pedidos.php?pd='+dt.ped;
                        }  
                    }
                });
            }
            
            function btnVln(){
                var entrega = $("#vlnEntrega").val();
//                alert("aprobar "+entrega);
                $.ajax({ 
                    url: 'vln_entrega.php', 
                    type: 'POST',
                    data: {
                        codigo:entrega,
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
//                        alert(dt.msg);
                        if(dt.err == 0){
                            $('#vlnHdnEntrega').attr('value',entrega);
                            var cntnt = '';
                            $.each(dt.dat, function(idx, val){
                                cntnt += '<tr>';
                                cntnt += '        <td><input autocomplete="on" id="row_sel_'+val.pos+'" name="row_sel" class="form-control select_row" type="checkbox"></td>';
                                cntnt += '        <td>'+val.pos+'</td>';
                                cntnt += '        <td>'+val.mat+'</td>';
                                cntnt += '        <td>'+val.desc+'</td>';
                                cntnt += '        <td><input autocomplete="on" style="width:150px !important;" id="row_ct_'+val.pos+'" disabled="disabled" class="form-control select_row" value="'+val.cant+'" type="number"></td>';
                                cntnt += '        <td><input autocomplete="on" style="width:150px !important;"  id="row_pk_'+val.pos+'" name="row_picking" class="form-control select_row" value="'+val.cantpk+'" type="number"></td>';
                                cntnt += '</tr>';
                            });
                            $('#smpl_tbl tbody').empty().append(cntnt);
                            if(!$('#tbl_cnt').is(':visible')){
                                $('#tbl_cnt').toggle("fast","linear");
                            }                            
                            $('#btnPk').prop('disabled', false);
                            $('#btnAnl').prop('disabled', false);
                        }
                    }
                });
            }
            function btnContab(){
                var entrega = $("#inEntrega").val();
//                alert("aprobar "+entrega);
                $.ajax({ 
                    url: 'cont_entrega.php', 
                    type: 'POST',
                    data: {
                        codigo:entrega,
                        usuario:'<?php echo $_SESSION["user"] ?>'
                    },
                    success: function (data) {
                        var dt = JSON.parse(data);
                        alert(dt.msg);
                        if(dt.err == 0){
                            $("#inEntrega").val('');
                        }
                    }
                });
            }
            function btnTimes(){
                var entrega = $("#inTimeEntrega").val();
                var centro = $("#inTimeCentro").val();
                if(entrega != '' && centro != ''){
                    $.ajax({ 
                        url: 'tiempo_entrega.php', 
                        type: 'POST',
                        data: {
                            codigo:entrega,
                            centro:centro,
                            usuario:'<?php echo $_SESSION["user"] ?>'
                        },
                        success: function (data) {
                            var dt = JSON.parse(data);
                            alert(dt.msg);
                            if(dt.err == 0){
                                $("#inTimeEntrega").val('');
                                $("#inTimeCentro").val('');
                            }
                        }
                    });
                }else{
                alert("Los campos son obligatorios");
                }
            }
    </script>
    
