<?php
// Configurar cabeceras para recibir JSON y responder JSON
header("Access-Control-Allow-Origin: *"); // Permite cualquier origen, puedes poner un dominio específico
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

require_once('../wp-load.php');

// Obtener los datos enviados por POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email']) || !isset($data['orderid']) || !isset($data['clientdata'])) {
    echo json_encode(["error" => "Faltan parámetros"]);
    http_response_code(400);
    exit;
}

$to = $data['email'];
$subject = 'Pago completado desde fuera de Chile';
$message = '
    Hola '.$data['email'].', has completado el pago mediante tarjeta de crédito o débito desde fuera de Chile.<br><br>
    <b>Datos del pedido:</b><br>
    <b>ID:</b> '.$data['orderid'].'<br><br>
    Pago con Tarjeta de Crédito o Débito de otros países • '.$data['clientdata']['amount'].' '.$data['clientdata']['currency_code'].'<br><br>
    <b>Dirección de envío:</b><br>
    <b>Nombre: </b>'.$data['clientdata']['given_name'].' '.$data['clientdata']['surname'].'<br>
    <b>Dirección: </b>'.$data['clientdata']['address'].'<br>
    <b>Ciudad: </b>'.$data['clientdata']['address1'].'<br>
    <b>Estado: </b>'.$data['clientdata']['address2'].'<br>
    <b>Código postal: </b>'.$data['clientdata']['postal_code'].'<br>
    <b>País: </b>'.$data['clientdata']['country_code'].'<br>
';

// Cabeceras para contenido HTML y de origen
$headers = array('Content-Type: text/html; charset=UTF-8');

if (wp_mail($to, $subject, $message, $headers)) {
    echo json_encode(["success" => "Email enviado correctamente"]);
} else {
    echo json_encode(["error" => "Error al enviar el email"]);
    http_response_code(500);
}