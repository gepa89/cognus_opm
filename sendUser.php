<?php
//require ('/var/www/html/hanaDB.php');
include('conect.php');
require(__DIR__ . "/modelos/roles_permisos.php");
//require_once("hanaDB.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);
$db->begin_transaction();
$rt = 0;
$rol = '';


//prt_user
//prt_action
//prt_obs
//prt_user_dest
//prt_ts
function saveHistory($user, $action, $obs, $db)
{

    $sql = "insert into history set 
            his_user = '$user',
            his_action = '$action',
            his_obs = '" . utf8_decode($obs) . "',
            his_ts = now()
            ";

    //        echo $sql;
    //$rs = $db->query($sql);
}



if (!isset($_SESSION)) {
    session_start();
}
if (isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case 'suscribe':
            $flg = 0;
            $sql = "select * from usuarios where pr_user = '{$_POST['name']}'";
            $rs = $db->query($sql);
            //            echo "<pre>";var_dump($rs);echo "</pre>";
            $uname = strtoupper($_POST['name']);
            if ($rs->num_rows > 0) {
                $msg = 'Nombre de usuario ya existe.';
                $err = 1;
                $flg = 1;
                $rt = 0;
            } else {
                $oci_qry = "select t1.BNAME, t2.NAME_TEXT from SAPABAP1.USR01 t1 LEFT JOIN SAPABAP1.USER_ADDRP t2 ON t1.BNAME = t2.BNAME where t1.BNAME = '{$uname}'";
                $result = odbc_exec($qas, $oci_qry);
                while ($row = odbc_fetch_object($result)) {
                    $usr[] = $row;
                }
                if (count($usr) == 0) {
                    $msg = 'Usuario no registrado en SAP.';
                    $err = 1;
                    $flg = 1;
                    $rt = 0;
                }
            }
            $sql = "select * from usuarios where pr_email = '{$_POST['email']}'";
            $rs = $db->query($sql);
            if ($rs->num_rows > 0) {
                $msg = 'Direccion de correo ya registrada.';
                $err = 1;
                $flg = 1;
                $rt = 0;
            }
            if ($flg == 0) {

                $sql = "insert into usuarios set pr_user = '{$uname}', pr_nombre = '{$_POST['nombre']}',pr_apellido = '{$_POST['apellido']}', clirefer = '{$_POST['cli']}', pr_rol = 3, pr_email = '{$_POST['email']}', pr_ts = now(), pr_pass = '{$_POST['pass']}' ";
                echo $sql;
                if ($rs = $db->query($sql)) {
                    //                    saveHistory($uname, $_POST["action"], 'Alta de usuario', '',$db);
                    $msg = 'Usuario registrado correctamente.';
                    $err = 0;
                    $rt = 1;
                } else {
                    $msg = 'Error al registrar.';
                    $err = 1;
                    $rt = 1;
                }

                //                echo $db->error;


            }

            break;
        case 'add_evt':
            $sq = "insert into eventos set
                ev_desc = '{$_POST['descripcion']}',
                ev_dist = '{$_POST['distribuidor']}',
                ev_suc = '{$_POST['sucursal']}',
                ev_tipo = {$_POST['tipo']},
                ev_user = {$_SESSION['user_id']},
                ev_inicio = '" . date("Y-m-d", strtotime($_POST['desde'])) . "',
                ev_fin = '" . date("Y-m-d", strtotime($_POST['hasta'])) . "',
                ev_ts = now()
                ";
            //                echo $sq;
            if ($db->query($sq)) {
                $msg = 'Evento guardado satisfactoriamente.';
                saveHistory($_SESSION["user_id"], $_POST["action"], 'Creación de evento ' . $_POST['desc'], $db);
                $err = 0;
                $rt = 2;
            } else {
                $msg = 'Error al guardar evento.';
                $err = 1;
                $rt = 0;
            }
            break;
        case 'add_tipo':
            $sq = "insert into eventos_tipos set
                tev_desc = '{$_POST['desc']}',
                tev_ts = now()
                ";
            //                echo $sq;
            if ($db->query($sq)) {
                $msg = 'Tipo de evento guardado satisfactoriamente.';
                saveHistory($_SESSION["user_id"], $_POST["action"], 'Creación de tipo de evento ' . $_POST['desc'], $db);
                $err = 0;
                $rt = 2;
            } else {
                $msg = 'Error al guardar tipo de evento.';
                $err = 1;
                $rt = 0;
            }
            break;
        case 'add_suc':
            $sq = "insert into sucursales set
                suc_dist = '{$_POST['dist']}',
                suc_desc = '{$_POST['suc']}',
                suc_dir = '" . $_POST['dir'] . "',
                suc_tel = '{$_POST['tel']}',
                suc_ts = now()
                ";
            //                echo $sq;
            if ($db->query($sq)) {
                $msg = 'Sucursal guardada satisfactoriamente.';
                saveHistory($_SESSION["user_id"], $_POST["action"], 'Creación de sucursal ' . $_POST['suc'], $db);
                $err = 0;
                $rt = 2;
            } else {
                $msg = 'Error al guardar sucursal.';
                $err = 1;
                $rt = 0;
            }
            break;
        case 'edit_suc':
            $sq = "update sucursales set
                suc_dist = '{$_POST['dist']}',
                suc_desc = '{$_POST['suc']}',
                suc_dir = '" . $_POST['dir'] . "',
                suc_tel = '{$_POST['tel']}',
                suc_ts = now()
                ";
            //                echo $sq;
            if ($db->query($sq)) {
                $msg = 'Sucursal guardada satisfactoriamente.';
                saveHistory($_SESSION["user_id"], $_POST["action"], 'Actualizacion de sucursal ' . $_POST['suc'], $db);
                $err = 0;
                $rt = 2;
            } else {
                $msg = 'Error al guardar sucursal.';
                $err = 1;
                $rt = 0;
            }
            break;
        case 'del_suc':
            $sq = "select * from sucursales where suc_id = {$_POST['id']}";
            //                echo $sq;
            $rs = $db->query($sq);
            $ax = $rs->fetch_assoc();
            $sq = "delete from sucursales where suc_id = {$_POST['id']}";
            //                echo $sq;
            if ($db->query($sq)) {
                $msg = 'Sucursal eliminada satisfactoriamente.';
                saveHistory($_SESSION["user_id"], $_POST["action"], 'Se elmina sucursal: ' . $ax['suc_desc'] . ' del distribuidor: ' . $ax['suc_dist'], $db);
                $err = 0;
                $rt = 2;
            } else {
                $msg = 'Error al guardar sucursal.';
                $err = 1;
                $rt = 0;
            }
            break;
        case 'login':
            $usr = strtoupper($_POST['username']);
            $sq = "select * from usuarios where pr_user = '{$usr}' and pr_pass = '{$_POST['password']}'";
            $rs = $db->query($sq);
            if ($rs->num_rows > 0) {
                $dat = $rs->fetch_assoc();
                if ($dat['pr_rol'] < 100) {
                    $roles_permisos = new RolesPermisos($db);
                    $_SESSION['user'] = $dat['pr_user'];
                    $_SESSION['user_id'] = $dat['pr_id'];
                    $_SESSION['user_nombre'] = $dat['pr_nombre'];
                    $_SESSION['user_apellido'] = $dat['pr_apellido'];
                    $_SESSION['user_cli'] = $dat['clirefer'];
                    $_SESSION['user_rol'] = $dat['pr_rol'];
                    $_SESSION['session_start'] = time();
                    //$_SESSION['modulos'] = $roles_permisos->obtener_accesos_modulos($dat['pr_user']);
                    $_SESSION['permisos'] = $roles_permisos->obtenerPermisos($dat['pr_id']);
                    $msg = '';
                    $err = 0;
                    $rt = 1;
                    $rol = $dat['pr_rol'];
                    saveHistory($dat['pr_id'], $_POST["action"], 'Inicio de sesión', $db);
                } else {
                    $msg = 'Usuario no autorizado para acceder';
                    $err = 1;
                    $rt = 0;
                }
            } else {
                $msg = 'Usuario no habilitado';
                $err = 1;
                $rt = 0;
            }

            break;
        case 'close':
            saveHistory($_SESSION['user_id'], 'logout', 'Cierre de sesion', $db);
            session_unset();
            session_destroy();

            $msg = '';
            $err = 0;
            $rt = 3;
            break;
        case 'edit_usr':
            //            '.$_POST['user'].''
//            '.$_POST['email'].''
//            '.$_POST['nombre'].''
//            '.$_POST['apellido'].''
//            '.$_POST['pass'].''
//            '.$_POST['rol'].''
            $user = strtoupper($_POST['user']);
            $sq = "update usuarios set 
                    pr_nombre = '{$_POST['nombre']}',
                    clirefer = '{$_POST['cli']}',
                    pr_apellido = '{$_POST['apellido']}',";
            $sq .= "pr_rol = {$_POST['rol']},
                    pr_ts = now(),
                    pr_email = '{$_POST['email']}'
                    where pr_user = '{$_POST['user']}'";
            //                    echo $sq;
            if ($db->query($sq)) {
                $id_rol = $_POST['rol'];
                $id_usuario = $_POST['user'];
                $sql = "UPDATE usuario_roles INNER JOIN usuarios ON usuarios.pr_id = usuario_roles.id_usuario
                 SET id_rol='$id_rol' WHERE usuarios.pr_user='$user'";
                $res = $db->query($sql);
                if (!$res) {
                    $db->rollback();
                    $db->close();
                    exit;
                }
                $msg = 'Datos guardados correctamente.';
                $err = 0;
                $rt = 1;
            } else {
                $msg = 'Error al guardar datos';
                $err = 1;
                $rt = 0;
            }

            break;
        case 'add_user':
            //            '.$_POST['user'].''
//            '.$_POST['email'].''
//            '.$_POST['nombre'].''
//            '.$_POST['apellido'].''
//            '.$_POST['pass'].''
//            '.$_POST['rol'].''
            $user = strtoupper($_POST['user']);
            while ($rw = odbc_fetch_object($rst)) {
                $dt = $rw;
            }
            $sq = "insert into usuarios set 
                    pr_nombre = '{$_POST['nombre']}',
                    pr_apellido = '{$_POST['apellido']}',
                    clirefer = '{$_POST['cli']}',    
                    pr_pass = '{$_POST['pass']}',
                    pr_rol = '{$_POST['rol']}',
                    pr_ts = now(),
                    pr_email = '{$_POST['email']}',
                    pr_user = '{$user}'";

            if ($db->query($sq)) {
                try {
                    $id_usuario = $lastInsertedID = $db->insert_id;
                    $id_rol = $_POST['rol'];
                    $sql = "Insert INTO usuario_roles SET id_usuario='$id_usuario', id_rol='$id_rol'";
                    $res = $db->query($sql);
                    if (!$res) {
                        $db->rollback();
                        $db->close();
                        exit;
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
                $msg = 'Datos guardados correctamente.';
                $err = 0;
                $rt = 1;
            } else {
                $msg = $db->error;
                $err = 1;
                $rt = 0;
            }
            break;
        case 'assign':
            $msg = 'Comentario guardado satisfactoriamente.';
            $err = 0;
            $rt = 10;
            break;
        case 'change_passw':
            $msg = 'Comentario guardado satisfactoriamente.';
            $err = 0;
            $rt = 10;
            break;

    }
}
$db->commit();
echo json_encode(array('msg' => $msg, 'err' => $err, 'rt' => $rt, 'rol' => $rol));

//include '/var/www/html/closeconn.php';

exit();

?>