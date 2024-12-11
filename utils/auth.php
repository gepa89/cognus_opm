<?php
require_once(__DIR__ . '/../modelos/bd.php');
require_once(__DIR__ . '/../modelos/roles_permisos.php');
require_once(__DIR__ . '/../utils/respuesta.php');
include_once(__DIR__ . '/../auth/permisos.php');

function tieneAccesoAModulo($modulo)
{
    $id_usuario = $_SESSION['user_id'];
    $permisos = $_SESSION['permisos'];
    return isset($permisos[$id_usuario][$modulo]);
    //return true;
}


function verificarUsuarioLogueado()
{
    if (!isset($_SESSION['user'])) {
        header('Location:login.php');
        exit();
    }
    $roles_permisos = new RolesPermisos();
    $_SESSION['permisos'] = $roles_permisos->obtenerPermisos($_SESSION['user_id']);
    $roles_permisos->cerrar();
}
function verificarUsuarioLogueadoJSON()
{
    if (!isset($_SESSION['user'])) {
        retorna_resultado(401, ['mensaje' => "Usuario no autenticado"]);
        exit();
    }
    $roles_permisos = new RolesPermisos();
    $_SESSION['permisos'] = $roles_permisos->obtenerPermisos($_SESSION['user_id']);
    $roles_permisos->cerrar();
}
function obtenerUsuario()
{
    return @$_SESSION['user'];
}

function tienePermiso($modulo, $subseccion, $permiso)
{
    $id_usuario = $_SESSION['user_id'];
    $permisos = $_SESSION['permisos'];
    if (!isset($permisos[$id_usuario][$modulo][$subseccion])) {
        return false;
    }
    return in_array($permiso, $permisos[$id_usuario][$modulo][$subseccion] ?? []);
    //return true;
}

function tienePermisoLectura($modulo, $seccion)
{

    return tienePermiso($modulo, $seccion, "leer");
}

function verificarPermisoLectura($modulo, $seccion)
{
    if (!tienePermiso($modulo, $seccion, "leer")) {
        exit;
    }
}

function verificarPermisoEscritura($modulo, $seccion)
{
    if (!tienePermiso($modulo, $seccion, "escribir")) {
        exit;
    }
}
