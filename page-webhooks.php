<?php
/*
Template Name: Gerenciamento de Webhooks
*/

get_header(); 

if (!is_user_logged_in()) {
    echo '<div class="webhook-container"><p>🔒 Você precisa estar logado para acessar esta página.</p>';
    echo '<a href="' . wp_login_url(get_permalink()) . '" class="login-link">Clique aqui para fazer login</a></div>';
    get_footer();
    exit;
}

// Obtém o usuário atual e gera o token fixo
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Use uma chave secreta definida no wp-config.php, se disponível
$secret_key = defined('MY_WEBHOOK_SECRET') ? MY_WEBHOOK_SECRET : 'minha-chave-secreta';
$token = md5($user_id . $secret_key);

// Gera a URL única para o usuário
$webhook_url = home_url('/wp-content/themes/api-web-hook/webhook-handler.php') . '?user_id=' . $user_id . '&token=' . $token;

global $wpdb;
?>

<div class="webhook-container">
    <h2>Gerenciamento de Webhooks</h2>

    <!-- Link para a Logzz -->
    <div class="logzz-link-container">
        <span class="logzz-title">🔗 Link para a Logzz:</span>
        <div class="logzz-link-box">
            <span class="logzz-link"><?php echo esc_url($webhook_url); ?></span>
            <button class="copy-button" onclick="copyToClipboard('<?php echo esc_js($webhook_url); ?>')">📋 Copiar</button>
        </div>
    </div>

    <form id="webhook-form" method="post">
        <label for="status">Status do Webhook:</label>
        <input type="text" id="status" name="status" required>

        <label for="link">URL do Webhook:</label>
        <input type="url" id="link" name="link" required>

        <button type="submit">Adicionar Webhook</button>
    </form>

        <!-- Campo para definir o número de WhatsApp para teste -->
   <?php
        // Verifica se o usuário está logado
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $whatsapp = get_user_meta($user_id, 'whatsapp_teste', true); // Pega o WhatsApp salvo
        } else {
            $whatsapp = ''; // Se não estiver logado, deixa em branco
        }
        ?>
        
<div id="whatsapp-container">
    <strong>Whatsapp para teste:</strong> <span id="whatsapp-display"><?php echo esc_html($whatsapp); ?></span>
    <div id="whatsapp-actions">
        <button id="editar-whatsapp" style="display: <?php echo ($whatsapp ? 'inline-block' : 'none'); ?>;">✏️ Editar</button>
        <button id="excluir-whatsapp" style="display: <?php echo ($whatsapp ? 'inline-block' : 'none'); ?>;">🗑️ Excluir</button>
        <button id="adicionar-whatsapp" style="display: <?php echo ($whatsapp ? 'none' : 'inline-block'); ?>;">➕ Adicionar</button>
    </div>
    <!-- Campo oculto para editar/adicionar com placeholder e pattern -->  
    <div id="whatsapp-edicao" style="display:none; margin-top:10px;">  
        <input type="text" id="whatsapp-input" placeholder="00 9 9999-9999" pattern="^\\d{2}\\s9\\s\\d{4}-\\d{4}$" style="width:150px;">  
        <button id="salvar-whatsapp-input">Salvar</button>  
        <button id="cancelar-whatsapp-input">Cancelar</button>  
    </div>
</div>

    <h2>Webhooks Cadastrados</h2>
    <ul id="webhook-list" class="webhook-list">
        <?php
        // Filtra os webhooks para o usuário atual
        $webhooks = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}webhooks WHERE user_id = {$user_id}");

        if ($webhooks) {
            foreach ($webhooks as $webhook) {
                // Armazena o status em um atributo data-status
                echo "<li data-id='{$webhook->id}' class='webhook-item' data-status='" . esc_attr($webhook->status) . "'>
                    <div class='webhook-info'>
                        <strong>Status:</strong> <span class='status'>{$webhook->status}</span>
                        | <strong>Link:</strong> 
                        <div class='webhook-link'>{$webhook->webhook_url}</div>
                    </div>
                    <div class='webhook-actions'>
                        <button class='edit-webhook'>✏️ Editar</button>
                        <button class='delete-webhook'>🗑️ Excluir</button>
                        <button class='test-webhook'>Testar</button>
                    </div>
                </li>";
            }
        } else {
            echo "<li>Nenhum webhook cadastrado.</li>";
        }
        ?>
    </ul>
</div>

<style>
    .logzz-link-container {
        margin-bottom: 20px;
        padding: 10px;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .logzz-title {
        font-weight: bold;
    }

    .logzz-link-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        max-width: 100%;
    }

    .logzz-link {
        flex-grow: 1;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 5px;
    }

    .copy-button {
        background: #0073aa;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
    }

    .copy-button:hover {
        background: #005a87;
    }

    .webhook-link {
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        max-width: 100%;
        padding: 5px;
        background: #f1f1f1;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    .webhook-link-display {
    width: 100%;
    max-width: 100%; /* Limita a largura do campo */
    white-space: nowrap; /* Evita quebra de linha */
    overflow: hidden; /* Esconde o texto que ultrapassar */
    text-overflow: ellipsis; /* Adiciona '...' ao final do texto longo */
    padding: 5px;
    margin-top: 5px;
}
</style>

<script>
function copyToClipboard(link) {
    navigator.clipboard.writeText(link).then(() => {
        alert("Link copiado!");
    }).catch(err => {
        console.error("Erro ao copiar link: ", err);
    });
}
</script>

<?php get_footer(); ?>
