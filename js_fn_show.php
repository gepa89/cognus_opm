

    <script>
            
            $(document).ready(function() {
//                $('#mped').addClass('active');
//                $('#inputEntrega').focus();
//                loadHCPie();
//                loadHCPieEntrega();
//                loadHCPieTransf();
                loadHCColumns()
                setInterval(function(){
//                    loadHCPie();
                    loadHCColumns()
//                    loadHCPieEntrega();
//                    loadHCPieTransf();
                },300000);
                
            });
            function loadHCColumns(){
//                console.log('llamado');
               $.ajax({ 
                    url: 'productividad.php', 
                    type: 'POST',
                    
                    success: function (data) {
                        var dt = JSON.parse(data);
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
                        console.log(x_totales);
//                        ____________________________________________________________________________
                        $('#gr_2').highcharts({
                            chart: {
                                type: 'column'
                            },
                            title: {
                                text: 'Productividad por Usuario'
                            },
                            subtitle: {
                                text: '<?php echo date('d-m-Y', strtotime('now'))?>'
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
    </script>
    
