<?php
// Carrega o ambiente do WordPress para usar suas funções
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método inválido']);
    exit;
}

if (!isset($_POST['webhook_url']) || !isset($_POST['webhook_status'])) {
    echo json_encode(['error' => 'Parâmetros inválidos']);
    exit;
}

$webhook_status = sanitize_text_field($_POST['webhook_status']);

// Obter usuário logado e gerar token conforme a lógica do template
$user_id = get_current_user_id();
if (!$user_id) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Recupera o WhatsApp salvo no usermeta
$client_phone = get_user_meta($user_id, 'whatsapp_teste', true);

// Caso não tenha um número salvo, retorna erro
if (!$client_phone) {
    echo json_encode(['error' => 'Nenhum número de WhatsApp salvo.']);
    exit;
}

// Garante o formato correto com +55 (Brasil)
$client_phone = "+55" . preg_replace('/\D/', '', $client_phone);

$secret_key = defined('MY_WEBHOOK_SECRET') ? MY_WEBHOOK_SECRET : 'minha-chave-secreta';
$token = md5($user_id . $secret_key);

// Montar os dados para o envio do webhook
$date_order = date("Y-m-d H:i:s");
$days_of_week = [
    "Sunday" => "domingo",
    "Monday" => "segunda-feira",
    "Tuesday" => "terça-feira",
    "Wednesday" => "quarta-feira",
    "Thursday" => "quinta-feira",
    "Friday" => "sexta-feira",
    "Saturday" => "sábado"
];
$date_order_day = $days_of_week[date("l")];

$data = [
    "integration" => [
        "turn" => "1/1",
        "name" => "Fake Integration",
        "link" => "https://fakeintegration.com",
        "level" => "standard"
    ],
    "client_name" => "Tiago Lins",
    "client_email" => "johndoe@example.com",
    "client_document" => "123.456.789-00",
    "client_phone" => $client_phone,
    "client_zip_code" => "12345-678",
    "client_address" => "Rua dos sonhos",
    "client_address_number" => "42",
    "client_address_district" => "Imaginary District",
    "client_address_comp" => "Apt 101",
    "client_address_city" => "Fantasy City",
    "client_address_state" => "MG",
    "client_address_country" => "Brasil",
    "date_order" => $date_order,
    "date_order_day" => $date_order_day,
    "date_delivery" => "2025-02-22 12:28:19",
    "date_delivery_day" => "quarta-feira",
    "delivery_estimate" => "12:28",
    "order_number" => "1000S0123P1000",
    "order_status" => $webhook_status,
    "order_status_description" => "Status atualizado dinamicamente para: $webhook_status",
    "order_quantity" => "3",
    "order_final_price" => "150.00",
    "second_order" => false,
    "first_order" => true,
    "products" => [
        "main" => [
            "product_name" => "Produto do Amor",
            "product_code" => "FP12345",
            "quantity" => "3",
            "variations" => [
                [
                    "product_name" => "Fictional Product - V",
                    "product_code" => "FP123451",
                    "quantity" => "1"
                ],
                [
                    "product_name" => "Fictional Product - W",
                    "product_code" => "FP123452",
                    "quantity" => "2"
                ]
            ]
        ]
    ],
    "logistic_operator" => "Logistics Inc.",
    "delivery_man" => "John Courier",
    "producer_name" => "Producer Corp.",
    "producer_email" => "contact@producercorp.com",
    "affiliate_name" => "Affiliate Partner",
    "affiliate_email" => "partner@affiliate.com",
    "affiliate_phone" => "11-1234-5678",
    "utm" => [
        "utm_source" => "google",
        "utm_medium" => "cpc",
        "utm_campaign" => "summer-sale",
        "utm_term" => "beachwear",
        "utm_content" => "banner-1",
        "utm_id" => "1"
    ],
    "commission" => 10
];

// Montar a URL do webhook handler com o domínio atual e os parâmetros user_id e token
$domain = $_SERVER['HTTP_HOST'];
$webhook_handler_url = "https://$domain/wp-content/themes/api-web-hook/webhook-handler.php?user_id=$user_id&token=$token";

// Enviar os dados para o webhook handler usando wp_remote_post (método recomendado pelo WordPress)
$response = wp_remote_post($webhook_handler_url, array(
    'headers' => array('Content-Type' => 'application/json'),
    'body'    => json_encode($data),
    'timeout' => 15,
));

if (is_wp_error($response)) {
    echo json_encode(['error' => 'Falha ao enviar requisição: ' . $response->get_error_message()]);
} else {
    echo json_encode(['success' => 'Webhook testado com sucesso!', 'response' => wp_remote_retrieve_body($response)]);
}
?>
