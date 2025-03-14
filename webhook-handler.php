<?php
// Carrega o WordPress para acessar o banco de dados
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

global $wpdb;

// Valida os parâmetros GET esperados
if (!isset($_GET['user_id']) || !isset($_GET['token'])) {
    http_response_code(400);
    echo json_encode(["error" => "Parâmetros de identificação ausentes."]);
    exit;
}

$user_id = intval($_GET['user_id']);
$provided_token = $_GET['token'];

// Verifica se o token é válido
$secret_key = defined('MY_WEBHOOK_SECRET') ? MY_WEBHOOK_SECRET : 'minha-chave-secreta';
$expected_token = md5($user_id . $secret_key);

if ($provided_token !== $expected_token) {
    http_response_code(403);
    echo json_encode(["error" => "Token inválido."]);
    exit;
}

// O restante do código segue similarmente, mas agora você pode filtrar os webhooks para esse usuário, se for o caso.
// Por exemplo, se os webhooks devem ser vinculados ao usuário que forneceu o token:
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Define o código do país (Brasil: +55)
$country_code = "+55";

// Captura o corpo da requisição recebida da Logzz
$input = file_get_contents("php://input");

// Log para verificar o JSON recebido
file_put_contents('webhook_log.txt', "Recebido (usuário {$user_id}): " . $input . "\n", FILE_APPEND);

$data = json_decode($input, true);

// Valida o JSON
if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido"]);
    exit;
}

// Verifica se o "order_status" foi enviado no JSON
if (!isset($data["order_status"])) {
    http_response_code(400);
    echo json_encode(["error" => "order_status não encontrado no JSON"]);
    exit;
}

$order_status = $data["order_status"];

// Se o sistema deve enviar apenas os webhooks do usuário autenticado pelo token, adicione essa condição:
$webhooks = $wpdb->get_results($wpdb->prepare(
    "SELECT webhook_url FROM {$wpdb->prefix}webhooks WHERE status = %s AND user_id = %d",
    $order_status,
    $user_id
));

if (!$webhooks) {
    file_put_contents('webhook_log.txt', "Nenhum webhook encontrado para status: $order_status (usuário $user_id)\n", FILE_APPEND);
    http_response_code(404);
    echo json_encode(["error" => "Nenhum webhook encontrado para esse status"]);
    exit;
}

// Corrige o telefone, se necessário
if (isset($data["client_phone"])) {
    $phone = $data["client_phone"];
    if (!preg_match("/^\\+\\d+/", $phone)) {
        $phone = $country_code . $phone;
    }
    $data["client_phone"] = $phone;
}

$results = [];
foreach ($webhooks as $webhook) {
    file_put_contents('webhook_log.txt', "Enviando para {$webhook->webhook_url}: " . json_encode($data) . "\n", FILE_APPEND);

    $ch = curl_init($webhook->webhook_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $results[] = [
        "sent_to" => $webhook->webhook_url,
        "response" => $response,
        "response_code" => $http_code
    ];
}

http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Webhook enviado para " . count($results) . " endpoint(s)",
    "results" => $results
]);
