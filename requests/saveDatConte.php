<?php
require('../conect.php');
require_once(__DIR__ . '/../utils/respuesta.php');

// Conexión a la base de datos
$db = new mysqli($SERVER, $USER, $PASS, $DB);

if ($db->connect_error) {
    echo json_encode(['err' => 1, 'msg' => 'Error de conexión a la base de datos']);
    exit;
}

session_start();
$usuario = $_SESSION['user'] ?? null;

if (!$usuario) {
    echo json_encode(['err' => 1, 'msg' => 'Usuario no autenticado']);
    exit;
}

$action = $_POST['action'] ?? '';
$table = $_POST['table'] ?? '';
$fields = $_POST['fields'] ?? '';
$data = $_POST['data'] ?? [];

$fecha = date('Y-m-d');
$hora = date('H:i:s');

$response = [
    'err' => 1,
    'msg' => 'Operación no válida'
];

if (!in_array($action, ['add', 'upd']) || !$table || !$fields) {
    echo json_encode($response);
    exit;
}

try {
    if ($action === 'add' && is_array($data)) {
        $sql = "INSERT INTO $table ($fields, usuario, fecre, horcre) VALUES ";

        $values = [];
        $params = [];
        $types = '';

        foreach ($data as $row) {
            $values[] = "(?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array_merge($params, [
                $row['docompra'],
                $row['tipconte'],
                $row['numconte'],
                $row['canti'],
                $row['observacion'],
                $usuario,
                $fecha,
                $hora
            ]);
            $types .= 'ssssssss';
        }

        $sql .= implode(',', $values);

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $response = [
                'err' => 0,
                'msg' => 'Datos guardados exitosamente'
            ];
        } else {
            $response = [
                'err' => 1,
                'msg' => 'Error al ejecutar la consulta: ' . $stmt->error
            ];
        }

        $stmt->close();
    } elseif ($action === 'upd') {
        $sql = "UPDATE $table SET 
                docompra = ?,
                tipconte = ?,
                numconte = ?,
                observacion = ?,
                canti = ?,
                usermod = ?,
                fecmod = ?,
                hormod = ?
                WHERE docompra = ?";

        $stmt = $db->prepare($sql);
        $stmt->bind_param(
            'ssssissss',
            $_POST['Doc'],
            $_POST['Tco'],
            $_POST['Nco'],
            $_POST['Obs'],
            $_POST['Can'],
            $usuario,
            $fecha,
            $hora,
            $_POST['Doc']
        );

        if ($stmt->execute()) {
            $response = [
                'err' => 0,
                'msg' => 'Datos actualizados exitosamente'
            ];
        } else {
            $response = [
                'err' => 1,
                'msg' => 'Error al ejecutar la consulta: ' . $stmt->error
            ];
        }

        $stmt->close();
    } else {
        $response = [
            'err' => 1,
            'msg' => 'Acción no válida o datos incompletos'
        ];
    }
} catch (Exception $e) {
    $response = [
        'err' => 1,
        'msg' => 'Error del servidor: ' . $e->getMessage()
    ];
}

$db->close();
echo json_encode($response);
