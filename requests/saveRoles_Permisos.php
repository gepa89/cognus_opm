<?php
require('../conect.php');
require_once(__DIR__ . "/../utils/respuesta.php");
$db = new mysqli($SERVER, $USER, $PASS, $DB);

$table = $_POST['table'];
$fields = $_POST['fields'];
$descr = strtoupper($descr);
$fecha = date('Y-m-d');
$id_seccion = $_POST['id_seccion'];
$id_rol = $_POST['id_rol'];
$accion = $_POST['accion'];
switch ($_POST['action']) {
    case 'upd':
        $sq = "SELECT * FROM roles_permisos WHERE id_rol='$id_rol' AND id_seccion='$id_seccion' AND accion='$accion'";
        $res = $db->query($sq);
        if ($res->num_rows > 0) {
            $err = 1;
            $msg = 'Ya existe registro.';
            break;
        }
        $id = $_POST['id_permiso'];
        $sq = "UPDATE roles_permisos SET id_rol='$id_rol', id_seccion='$id_seccion', accion='$accion' WHERE id='$id'";
        if ($db->query($sq)) {
            $err = 0;
            $msg = 'Datos guardados.';
        } else {
            $err = 1;
            print_r($sq);
            $msg = 'No se pudo guardar registro.';
        }
        break;
    case 'add':

        $sq = "SELECT * FROM roles_permisos WHERE id_rol='$id_rol' AND id_seccion='$id_seccion' AND accion='$accion'";
        $res = $db->query($sq);
        if ($res->num_rows == 0) {
            $hoy = date("Y-m-d");
            $sq = "insert into roles_permisos (id_rol,id_seccion,accion,fecre) values ('$id_rol','$id_seccion','$accion','$hoy')";
            if ($db->query($sq)) {
                $err = 0;
                $msg = 'Datos guardados.';
            } else {
                //            echo $db->error;
                $err = 1;
                $msg = 'No se pudo guardar registro.';
            }
        } else {
            $err = 1;
            $msg = 'Ya existe registro.';
        }

        break;
}
$db->close();
echo json_encode(array('err' => $err, 'msg' => $msg));
