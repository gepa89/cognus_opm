<?php
require('../conect.php');
require_once(__DIR__ . '/../utils/respuesta.php');

// Verifica que el método de solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['err' => 1, 'msg' => 'Método no permitido']);
    exit;
}

// Decodifica el JSON enviado en la solicitud
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['err' => 1, 'msg' => 'Datos no válidos o mal formateados']);
    exit;
}

$action = $data['action'] ?? '';
$table = $data['table'] ?? '';
$fields = $data['fields'] ?? '';
$rows = $data['data'] ?? [];

// Validaciones iniciales
if (empty($action) || empty($table) || empty($fields) || !is_array($rows)) {
    echo json_encode(['err' => 1, 'msg' => 'Datos incompletos o inválidos']);
    exit;
}

// Validación de tabla permitida
$allowedTables = ['datconteped']; // Lista de tablas permitidas
if (!in_array($table, $allowedTables)) {
    echo json_encode(['err' => 1, 'msg' => 'Tabla no permitida']);
    exit;
}

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

$fecha = date('Y-m-d');
$hora = date('H:i:s');

$response = [
    'err' => 1,
    'msg' => 'Operación no válida'
];

try {
    if ($action === 'add') {
        // Construcción de la consulta INSERT
        $sql = "INSERT INTO $table ($fields, usuario, fecre, horcre) VALUES ";
        $values = [];
        $params = [];
        $types = '';

        foreach ($rows as $row) {
            // Validar que cada campo requerido exista en cada objeto del array
            if (!isset($row['docompra'], $row['tipconte'], $row['numconte'], $row['canti'], $row['observacion'])) {
                echo json_encode(['err' => 1, 'msg' => 'Datos incompletos en una o más filas']);
                exit;
            }

            // Crear placeholders y agregar valores
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
            $types .= 'ssssssss'; // Tipos de datos para bind_param
        }

        $sql .= implode(',', $values);

        $stmt = $db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $db->error);
        }

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
    } else {
        $response = [
            'err' => 1,
            'msg' => 'Acción no válida'
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
