<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!isset($data['claseId']) || !isset($data['timestamp'])) {
    http_response_code(400);
    echo json_encode(["message" => "Datos incompletos"]);
    exit;
}

$claseId = htmlspecialchars($data['claseId']);
$timestamp = intval($data['timestamp']);
$fecha = date("Y-m-d H:i:s", $timestamp / 1000);
$ip = $_SERVER['REMOTE_ADDR'];

$dbhost = "dpg-d0sc97s9c44c739on6o0-a";
$dbport = "5432";
$dbname = "asistencia_db_pgyx";
$dbuser = "asistencia_db_pgyx_user";
$dbpass = "9SiUDr6GYslbOpqeFL8F6EcCksoVIuyp";

$dsn = "pgsql:host=$dbhost;port=$dbport;dbname=$dbname";

try {
    $pdo = new PDO($dsn, $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "INSERT INTO asistencias (clase_id, fecha, ip_estudiante) 
            VALUES (:claseId, :fecha, :ip)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':claseId' => $claseId,
        ':fecha' => $fecha,
        ':ip' => $ip
    ]);

    echo json_encode(["message" => "Asistencia guardada correctamente"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Error en base de datos", "error" => $e->getMessage()]);
}
