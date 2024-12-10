<?php
// header('Location:http://192.168.12.50/empaque/login.php');
//exit();
//session_destroy();
if (isset($_SESSION['user'])) {
    header('Location:dashboard.php?almacen=CD11');
    exit();
} ?>
<!DOCTYPE html>
<html lang="en" class="login_page">
<?php include 'head.php' ?>

<body>
    <img style="width: 250px; display: block; margin: 6% auto -20%; position: relative;" src="img/logo.png" />
    <div class="login_box">

        <form method="post" id="login_form">
            <div class="top_b">Gestión de Almacenes - Iniciar sesión</div>
            <!--				<div class="alert alert-info alert-login">
                    Clear username and password field to see validation.
                </div>-->
            <div class="cnt_b">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm"><i class="glyphicon glyphicon-user"></i></span>
                        <input class="form-control input-sm" type="text" id="username" name="username"
                            placeholder="Usuario" value="" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm"><i class="glyphicon glyphicon-lock"></i></span>
                        <input class="form-control input-sm" type="password" id="password" name="password"
                            placeholder="Contraseña" value="" />
                        <input type="hidden" id="action" name="action" value="login" />
                    </div>
                </div>
                <!--					<div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" /> Recordarme</label></div>
                    </div>-->
            </div>
            <div class="btm_b clearfix">
                <button class="btn btn-default btn-sm pull-right" type="submit">Ingresar</button>
                <!--<span class="link_reg"><a href="#reg_form">Registrarse</a></span>-->
            </div>
        </form>

        <form method="post" id="pass_form" style="display:none">
            <div class="top_b">¿No puede iniciar sesión?</div>
            <div class="alert alert-info alert-login">
                Por favor ingrese su dirección de correo.<br />
            </div>
            <div class="cnt_b">
                <div class="formRow clearfix">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon input-sm">@</span>
                            <input type="text" placeholder="Dirección de correo" id="email" name="email"
                                class="form-control input-sm" />
                            <input type="hidden" id="action2" name="action" value="recover" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="btm_b tac">
                <button class="btn btn-default" type="submit">Solicitar</button>
            </div>
        </form>

        <form method="post" id="reg_form" style="display:none">
            <div class="top_b">Registrarse </div>
            <!--				<div class="alert alert-warning alert-login">
                    By filling in the form bellow and clicking the "Sign Up" button, you accept and agree to <a data-toggle="modal" href="#terms">Terms of Service</a>.
                </div>-->
            <!--				<div id="terms" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title">Terms and Conditions</h3>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Nulla sollicitudin pulvinar enim, vitae mattis velit venenatis vel. Nullam dapibus est quis lacus tristique consectetur. Morbi posuere vestibulum neque, quis dictum odio facilisis placerat. Sed vel diam ultricies tortor egestas vulputate. Aliquam lobortis felis at ligula elementum volutpat. Ut accumsan sollicitudin neque vitae bibendum. Suspendisse id ullamcorper tellus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum at augue lorem, at sagittis dolor. Curabitur lobortis justo ut urna gravida scelerisque. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam vitae ligula elit.
                                    Pellentesque tincidunt mollis erat ac iaculis. Morbi odio quam, suscipit at sagittis eget, commodo ut justo. Vestibulum auctor nibh id diam placerat dapibus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse vel nunc sed tellus rhoncus consectetur nec quis nunc. Donec ultricies aliquam turpis in rhoncus. Maecenas convallis lorem ut nisl posuere tristique. Suspendisse auctor nibh in velit hendrerit rhoncus. Fusce at libero velit. Integer eleifend sem a orci blandit id condimentum ipsum vehicula. Quisque vehicula erat non diam pellentesque sed volutpat purus congue. Duis feugiat, nisl in scelerisque congue, odio ipsum cursus erat, sit amet blandit risus enim quis ante. Pellentesque sollicitudin consectetur risus, sed rutrum ipsum vulputate id. Sed sed blandit sem. Integer eleifend pretium metus, id mattis lorem tincidunt vitae. Donec aliquam lorem eu odio facilisis eu tempus augue volutpat.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>-->
            <div class="cnt_b">
                <div class="alert alert-warning">
                    En caso de no contar con su <b>Código de Proveedor</b><br /> Favor contactar con el Departamento de
                    <i>Adquisición y Suministros</i> <br />(021) 518 0000
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm"><i class="glyphicon glyphicon-user"></i></span>
                        <input class="form-control input-sm" type="text" id="r_username" name="field1"
                            placeholder="Código" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm"><i class="glyphicon glyphicon-lock"></i></span>
                        <input class="form-control input-sm" type="password" id="r_password" name="field2"
                            placeholder="Contraseña" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm"><i class="glyphicon glyphicon-lock"></i></span>
                        <input class="form-control input-sm" type="password" id="r_password" name="field3"
                            placeholder="Repetir contraseña" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon input-sm">@</span>
                        <input class="form-control input-sm" type="text" id="r_email" name="field4" placeholder="Email"
                            value="">
                    </div>
                    <!--<span class="help-block">The e-mail address is not made public and will only be used if you wish to receive a new password.</span>-->
                </div>
            </div>
            <div class="btm_b tac">
                <button class="btn btn-default" type="button" onclick="chkFlds()">Registrarse</button>
            </div>
        </form>

        <div class="links_b links_btm clearfix">
            <!--<span class="linkform"><a href="#pass_form">Olvidó su contraseña?</a></span>-->
            <span class="linkform" style="display:none">No importa, <a href="#login_form">volver a la pantalla de inicio
                    de sesión</a></span>
        </div>

    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery.actual.min.js"></script>
    <script src="lib/validation/jquery.validate.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script>
        function chkFlds() {
            var name = $('input[name="field1"]').val();
            var pass1 = $('input[name="field2"]').val();
            var pass2 = $('input[name="field3"]').val();
            var email = $('input[name="field4"]').val();

            if (name != "") {
                if (email != '') {
                    var sp = email.split("@");
                    if (pass1 != "" && pass2 != "") {
                        if (pass1 == pass2) {
                            $.ajax({
                                type: 'POST',
                                url: 'sendUser.php',
                                data: {
                                    action: 'login',
                                    pass: pass,
                                    name: user
                                }, success: function (data) {
                                    var dt = JSON.parse(data);
                                    if (dt.rt == 1) {
                                        alert(dt.msg);
                                        window.location = 'login.php';
                                    } else {
                                        alert('Bienvenido');
                                    }
                                }
                            });
                        } else {
                            alert("Las contraseñas no coinciden.");
                        }
                    } else {
                        alert("Debe ingresar una contraseña.");
                    }
                } else {
                    alert("Debe ingresar una dirección de correo válida.");
                }
            } else {
                alert("Favor ingresar Nombre de Usuario.");
            }
        }
        $(document).ready(function () {

            //* boxes animation
            form_wrapper = $('.login_box');
            function boxHeight() {
                form_wrapper.animate({ marginTop: (- (form_wrapper.height() / 2) - 24) }, 400);
            };
            form_wrapper.css({ marginTop: (- (form_wrapper.height() / 2) - 24) });
            $('.linkform a,.link_reg a').on('click', function (e) {
                var target = $(this).attr('href'),
                    target_height = $(target).actual('height');
                $(form_wrapper).css({
                    'height': form_wrapper.height()
                });
                $(form_wrapper.find('form:visible')).fadeOut(400, function () {
                    form_wrapper.stop().animate({
                        height: target_height,
                        marginTop: (- (target_height / 2) - 24)
                    }, 500, function () {
                        $(target).fadeIn(400);
                        $('.links_btm .linkform').toggle();
                        $(form_wrapper).css({
                            'height': ''
                        });
                    });
                });
                e.preventDefault();
            });

            //* validation
            $('#login_form').validate({
                onkeyup: false,
                errorClass: 'error',
                validClass: 'valid',
                rules: {
                    username: { required: true, minlength: 3 },
                    password: { required: true, minlength: 3 }
                },
                messages: {
                    username: {
                        required: 'Este campo es requerido',
                        number: 'Favor ingresar un código válido'
                    },
                    password: {
                        required: 'Este campo es requerido'
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').addClass("f_error");
                    setTimeout(function () {
                        boxHeight()
                    }, 200)
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass("f_error");
                    setTimeout(function () {
                        boxHeight()
                    }, 200)
                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').append(error);
                },
                submitHandler: function (form) {
                    // do other things for a valid form
                    $.ajax({
                        url: 'sendUser.php',
                        type: form.method,
                        data: $(form).serialize(),
                        success: function (response) {
                            var dt = JSON.parse(response);
                            if (dt.err == 0) {
                                window.location.replace("dashboard.php?almacen=CD11");
                            } else {
                                alert(dt.msg);
                            }

                        }, error: function (error) {
                            console.log(error);
                        }
                    });
                }
            });
            $('#pass_form').validate({
                debug: true,
                onkeyup: false,
                errorClass: 'error',
                validClass: 'valid',
                rules: {
                    email: { required: true, minlength: 3 }
                },
                messages: {
                    email: {
                        required: 'Este campo es requerido'
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').addClass("f_error");
                    setTimeout(function () {
                        boxHeight()
                    }, 200)
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass("f_error");
                    setTimeout(function () {
                        boxHeight()
                    }, 200)
                },
                errorPlacement: function (error, element) {
                    $(element).closest('.form-group').append(error);
                },
                submitHandler: function (form) {
                    // do other things for a valid form
                    $.ajax({
                        url: 'sendUser.php',
                        type: form.method,
                        data: $(form).serialize(),
                        success: function (response) {
                            var dt = JSON.parse(response);
                            if (dt.err == 0) {
                                alert(dt.msg);
                                window.location.replace("login.php");
                            } else {
                                alert(dt.msg);
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>