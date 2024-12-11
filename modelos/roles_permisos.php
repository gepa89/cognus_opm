<?php
require_once(__DIR__ . "/../modelos/bd.php");
class RolesPermisos
{
    public int $id;
    protected string $tabla = "roles_permisos";
    private $conn = null;
    public function __construct()
    {
        /*if (isset($GLOBALS['conection']) && !empty($GLOBALS['conection'])) {
            $this->conn = $GLOBALS['conection'];
        } else {
            die("Error");
        }*/
        $this->conn = BaseDeDatos::obtenerInstancia();
    }
    public function tiene_permiso($accion, $seccion, $cod_usuario)
    {
        $sql = "SELECT
                    COUNT(*) as cantidad 
                FROM
                    roles_permisos
                    INNER JOIN roles ON roles.id = roles_permisos.id_rol
                    INNER JOIN secciones ON secciones.id = roles_permisos.id_seccion
                WHERE
                    roles_permisos.accion = '$accion'
                    AND secciones.subseccion = '$seccion'
                    AND roles_permisos.id_rol IN (
                        SELECT
                        usuario_roles.id_rol
                        FROM
                            usuario_roles
                            INNER JOIN usuarios ON usuarios.pr_id = usuario_roles.id_usuario
                        WHERE
                        usuarios.pr_user = '$cod_usuario'
                    )";
        $query = $this->conn->query($sql);
        if (!$query) {
            print_r($this->conn->error);

            die("Error");
        }
        return true;
    }
    public function obtener_accesos_modulos(string $cod_usuario)
    {
        $sql = "SELECT
                DISTINCT secciones.seccion 
                FROM
                    roles_permisos
                INNER JOIN roles ON
                    roles.id = roles_permisos.id_rol
                INNER JOIN secciones ON secciones.id = roles_permisos.id_seccion 
                WHERE
                    roles_permisos.accion = 'leer'
                    AND roles_permisos.id_rol IN (
                    SELECT
                        usuario_roles.id_rol
                    FROM
                        usuario_roles
                    INNER JOIN usuarios ON
                        usuarios.pr_id = usuario_roles.id_usuario
                    WHERE
                        usuarios.pr_user = '$cod_usuario'
                )";

        $query = $this->conn->query($sql);
        if (!$query) {
            die("Error");
        }
        return $query->fetch_all(MYSQLI_ASSOC);
    }
    public function obtener_permisos($cod_usuario)
    {
        $sql = "SELECT
                    DISTINCT secciones.seccion,
                    secciones.subseccion,
                    roles_permisos.accion
                FROM
                    roles_permisos
                INNER JOIN roles ON
                    roles.id = roles_permisos.id_rol
                INNER JOIN secciones ON
                    secciones.id = roles_permisos.id_seccion
                WHERE
                    roles_permisos.id_rol IN (
                    SELECT
                        usuario_roles.id_rol
                    FROM
                        usuario_roles
                    INNER JOIN usuarios ON
                        usuarios.pr_id = usuario_roles.id_usuario
                    WHERE
                        usuarios.pr_user = '$cod_usuario')";
        $query = $this->conn->query($sql);
        if (!$query) {
            print_r($this->conn->error);

            die("Error");
        }
        $datos = $query->fetch_all(MYSQLI_ASSOC);
        return $datos;
    }

    public function obtener_rol($cod_usuario)
    {
        $sql = "SELECT
                    DISTINCT roles.codigo
                from
                    usuario_roles
                inner join usuarios on
                    usuarios.pr_id = usuario_roles.id_usuario
                inner join roles on
                    roles.id = usuario_roles.id_rol
                where
                    usuarios.pr_user = '$cod_usuario'
                limit 1
                ";
        $query = $this->conn->query($sql);
        if (!$query) {
            print_r($this->conn->error);

            die("Error");
        }
        $datos = $query->fetch_assoc();
        return $datos['codigo'];
    }
    public function tiene_permiso_lectura($seccion, $cod_usuario)
    {
        return true;
    }
    public function tiene_permiso_escritura($seccion, $cod_usuario)
    {

        return true;
    }
    public function cerrar()
    {
        $this->conn->close();
    }

    public function obtenerPermisos($id_usuario)
    {
        $permisos = array();
        $sql = "SELECT
            usuario_roles.id_rol,
            usuario_roles.id_usuario,
            roles.codigo,
            secciones.seccion,
            secciones.subseccion,
            roles_permisos.accion
        from
            usuario_roles
        inner join roles on roles.id = usuario_roles.id_rol
        inner join roles_permisos on roles_permisos.id_rol = roles.id
        inner join secciones on secciones.id = roles_permisos.id_seccion
        inner join usuarios on usuarios.pr_id = usuario_roles.id_usuario
        WHERE
        id_usuario = '$id_usuario'";
        $res =  $this->conn->query($sql);
        while ($fila = $res->fetch_object()) {
            $permisos[$fila->id_usuario][$fila->seccion][$fila->subseccion][] = $fila->accion;
        }
        return $permisos;
    }
}
