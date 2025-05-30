<?php
ini_set('display_errors', 0);
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header("Content-Type: application/json");

// Conexión
$dbhost = "dpg-d0sc97s9c44c739on6o0-a";
$dbport = "5432";
$dbname = "asistencia_db_pgyx";
$dbuser = "asistencia_db_pgyx_user";
$dbpass = "9SiUDr6GYslbOpqeFL8F6EcCksoVIuyp";
$dsn    = "pgsql:host=$dbhost;port=$dbport;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error de conexión a la base de datos"]);
    exit;
}

// 🧾 Si es GET, mostrar lista de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['claseId'])) {
    $claseId = htmlspecialchars($_GET['claseId'], ENT_QUOTES, 'UTF-8');

    try {
        $sql = "SELECT fecha, ip_estudiante FROM asistencias WHERE clase_id = :claseId ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':claseId' => $claseId]);
        $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["asistencias" => $asistencias]);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["message" => "Error al obtener asistencias"]);
        exit;
    }
}

// 📝 Si es POST, registrar asistencia
$input = file_get_contents("php://input");
$data  = json_decode($input, true);

if (!isset($data['claseId'], $data['timestamp'])) {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos"]);
    exit;
}

$claseId  = htmlspecialchars($data['claseId'], ENT_QUOTES, 'UTF-8');
$timestamp = (int) ($data['timestamp'] / 1000);
$fecha     = date("Y-m-d H:i:s", $timestamp);
$ip        = $_SERVER['REMOTE_ADDR'];

try {
    $sql = "INSERT INTO asistencias (clase_id, fecha, ip_estudiante)
            VALUES (:claseId, :fecha, :ip)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':claseId' => $claseId,
        ':fecha'   => $fecha,
        ':ip'      => $ip
    ]);

    echo json_encode(["message" => "Asistencia guardada correctamente"]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error en base de datos"]);
    exit;
}
