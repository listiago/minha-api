<?php
/**
 * api web hook functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package api_web_hook
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function api_web_hook_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on api web hook, use a find and replace
		* to change 'api-web-hook' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'api-web-hook', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'api-web-hook' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'api_web_hook_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'api_web_hook_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function api_web_hook_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'api_web_hook_content_width', 640 );
}
add_action( 'after_setup_theme', 'api_web_hook_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function api_web_hook_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'api-web-hook' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'api-web-hook' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'api_web_hook_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function api_web_hook_scripts() {
	wp_enqueue_style( 'api-web-hook-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'api-web-hook-style', 'rtl', 'replace' );

	wp_enqueue_script( 'api-web-hook-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'api_web_hook_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}







// FUNÇOES  PERSONALIZADAS A PARTIR DAQUI 












function criar_tabela_webhooks() {
    global $wpdb;

    $tabela = $wpdb->prefix . 'webhooks';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $tabela (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        status VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
        webhook_url VARCHAR(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function criar_tabela_webhooks_keed() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'webhooks_keed';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        event VARCHAR(255) NOT NULL,
        webhook_url TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'criar_tabela_webhooks_keed');



// Aciona a criação das tabelas ao ativar o tema
function ao_ativar_tema() {
    criar_tabela_webhooks(); // Tabela para os webhooks da Logzz
    criar_tabela_webhooks_keed(); // Nova tabela para os webhooks da Keed Paym
}
add_action('after_switch_theme', 'ao_ativar_tema');



// Função para adicionar os scripts e localiza o AJAX
// Função para adicionar os scripts e localizar o AJAX
function adicionar_scripts_webhook() {
    // Verifica se o usuário está logado e se tem um token
    $user_id = get_current_user_id();
    $token = get_user_meta($user_id, 'token', true); // Supondo que o token seja armazenado como 'token' no meta do usuário

    // Enfileira o script
    wp_enqueue_script('webhook-script', get_template_directory_uri() . '/js/webhook-script.js', array('jquery'), null, true);

    // Passa o ajaxurl, user_id e token para o JavaScript
    wp_localize_script('webhook-script', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'user_id' => $user_id,
        'token' => $token
    ));
}
add_action('wp_enqueue_scripts', 'adicionar_scripts_webhook');


// Função para adicionar um novo webhook
function adicionar_webhook() {
    global $wpdb;

    // Verifica se os dados do webhook foram enviados via POST
    if (!isset($_POST['status']) || !isset($_POST['link'])) {
        wp_send_json_error(['message' => 'Parâmetros ausentes'], 400);
        exit;
    }

    // Sanitiza os dados
    $status = sanitize_text_field($_POST['status']);
    $link = esc_url_raw($_POST['link']);
    $user_id = get_current_user_id(); // Obtém o ID do usuário atual

    // Insere no banco de dados com o ID do usuário
    $result = $wpdb->insert(
        $wpdb->prefix . 'webhooks',
        [
            'user_id' => $user_id,
            'status' => $status,
            'webhook_url' => $link
        ]
    );

    if ($result) {
        wp_send_json_success(['message' => 'Webhook adicionado com sucesso']);
    } else {
        wp_send_json_error(['message' => 'Erro ao inserir no banco de dados'], 500);
    }

    exit;
}

add_action('wp_ajax_adicionar_webhook', 'adicionar_webhook');
add_action('wp_ajax_nopriv_adicionar_webhook', 'adicionar_webhook'); // Permite requisições de usuários não logados

function obter_webhooks() {
    global $wpdb;

    $user_id = get_current_user_id(); // Obtém o ID do usuário atual

    // Busca os webhooks no banco de dados filtrando pelo user_id
    $webhooks = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM {$wpdb->prefix}webhooks WHERE user_id = %d", $user_id),
        ARRAY_A
    );

    if ($webhooks === null) { // Garante que retorna um array mesmo se não houver webhooks
        $webhooks = [];
    }

    wp_send_json_success(['webhooks' => $webhooks]);
    exit;
}


add_action('wp_ajax_obter_webhooks', 'obter_webhooks');
add_action('wp_ajax_nopriv_obter_webhooks', 'obter_webhooks'); // Permite requisições de usuários não logados



function editar_webhook() {
    global $wpdb;

    // Verifica se os parâmetros necessários foram enviados
    if (!isset($_POST['id']) || !isset($_POST['status']) || !isset($_POST['link'])) {
        wp_send_json_error(['message' => 'Parâmetros ausentes'], 400);
    }

    $id = intval($_POST['id']);
    $status = sanitize_text_field($_POST['status']);
    $link = esc_url_raw($_POST['link']);
    $user_id = get_current_user_id(); // Obtém o ID do usuário atual

    // Verifica se o webhook pertence ao usuário
    $webhook = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}webhooks WHERE id = %d AND user_id = %d", $id, $user_id));

    if ($webhook) {
        // Atualiza o webhook
        $updated = $wpdb->update(
            $wpdb->prefix . 'webhooks',
            ['status' => $status, 'webhook_url' => $link],
            ['id' => $id],
            ['%s', '%s'],
            ['%d']
        );

        if ($updated !== false) {
            wp_send_json_success(['message' => 'Webhook atualizado com sucesso!']);
        } else {
            wp_send_json_error(['message' => 'Erro ao atualizar webhook.'], 500);
        }
    } else {
        wp_send_json_error(['message' => 'Webhook não encontrado ou você não tem permissão para editá-lo.'], 403);
    }

    exit;
}


// Registra a ação AJAX para editar webhooks
add_action('wp_ajax_editar_webhook', 'editar_webhook');
add_action('wp_ajax_nopriv_editar_webhook', 'editar_webhook'); // Permite para usuários não logados


// Função para excluir um webhook
function excluir_webhook() {
    global $wpdb;

    // Verifica se o ID foi enviado corretamente
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        wp_send_json_error(['message' => 'ID inválido'], 400);
    }

    $id = intval($_POST['id']);
    $user_id = get_current_user_id(); // Obtém o ID do usuário atual

    // Verifica se o webhook pertence ao usuário
    $webhook = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}webhooks WHERE id = %d AND user_id = %d", $id, $user_id));

    if ($webhook) {
        // Exclui o webhook
        $deleted = $wpdb->delete("{$wpdb->prefix}webhooks", ['id' => $id], ['%d']);

        if ($deleted) {
            wp_send_json_success(['message' => 'Webhook excluído com sucesso']);
        } else {
            wp_send_json_error(['message' => 'Erro ao excluir webhook'], 500);
        }
    } else {
        wp_send_json_error(['message' => 'Webhook não encontrado ou você não tem permissão para excluí-lo.'], 403);
    }

    exit;
}

add_action('wp_ajax_excluir_webhook', 'excluir_webhook');
add_action('wp_ajax_nopriv_excluir_webhook', 'excluir_webhook'); // Permite para usuários não logados

// Função para adicionar a URL do AJAX no frontend
function adicionar_classe_page_webhook($classes) {
    if (is_page_template('template-webhook.php')) {
        $classes[] = 'page-webhook-template';
    }
    return $classes;
}
add_filter('body_class', 'adicionar_classe_page_webhook');


// Função para adicionar a URL do AJAX
function adicionar_ajax_url() {
    ?>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
    <?php
}
add_action('wp_head', 'adicionar_ajax_url');


function redirecionar_para_login_se_nao_logado() {
    if (!is_user_logged_in() && !is_page(array('login', 'registro'))) {
        wp_redirect(home_url('/login'));
        exit;
    }
}
add_action('template_redirect', 'redirecionar_para_login_se_nao_logado');



function criar_paginas_tema() {
    $paginas = [
        [
            'titulo'   => 'Login',
            'slug'     => 'login',
            'template' => 'template-login.php'
        ],
        [
            'titulo'   => 'Gerenciamento de Webhooks',
            'slug'     => 'webhook',
            'template' => 'page-webhooks.php'
        ],
        [
            'titulo'   => 'Cadastro',
            'slug'     => 'registro',  // Slug da página de registro
            'template' => 'template-registro.php'  // Template de registro
        ]
    ];

    foreach ($paginas as $pagina) {
        // Verifica se a página já existe pelo slug
        if (!get_page_by_path($pagina['slug'])) {
            // Cria a página automaticamente
            $nova_pagina = [
                'post_title'    => $pagina['titulo'],
                'post_name'     => $pagina['slug'],
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_author'   => 1,
                'page_template' => $pagina['template'],
            ];

            // Insere a página no banco de dados
            wp_insert_post($nova_pagina);
        }
    }
}

// Executa a função na ativação do tema
add_action('after_switch_theme', 'criar_paginas_tema');



// Desabilitar a barra de ferramentas para usuários 'subscriber'
add_action('after_setup_theme', function() {
    if (current_user_can('subscriber')) {
        show_admin_bar(false);
    }
});

// Desabilitar o editor visual para usuários 'subscriber'
add_filter('user_can_richedit', function($can_edit) {
    if (current_user_can('subscriber')) {
        return false;
    }
    return $can_edit;
});


function salvar_whatsapp_usuario() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Usuário não autenticado.');
    }

    $user_id = get_current_user_id();
    $whatsapp = isset($_POST['whatsapp']) ? preg_replace('/\D/', '', $_POST['whatsapp']) : '';

    if (strlen($whatsapp) !== 11) {
        wp_send_json_error('Número inválido.');
    }

    update_user_meta($user_id, 'whatsapp_teste', $whatsapp);
    wp_send_json_success('Número salvo com sucesso!');
}
add_action('wp_ajax_salvar_whatsapp', 'salvar_whatsapp_usuario');




// Função para obter o WhatsApp salvo
function obter_whatsapp() {
    $user_id = get_current_user_id();
    $whatsapp = get_user_meta($user_id, 'whatsapp_teste', true);

    wp_send_json_success(['whatsapp' => $whatsapp]);
}
add_action('wp_ajax_obter_whatsapp', 'obter_whatsapp');

// Função para salvar o WhatsApp
function salvar_whatsapp() {
    $user_id = get_current_user_id();
    $whatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : '';

    if (!preg_match('/^\d{11}$/', $whatsapp)) {
        wp_send_json_error(['message' => 'Número inválido! Use DDD + 9 dígitos.']);
    }

    update_user_meta($user_id, 'whatsapp_teste', $whatsapp);
    wp_send_json_success(['message' => 'WhatsApp salvo com sucesso!']);
}
add_action('wp_ajax_salvar_whatsapp', 'salvar_whatsapp');

// Função para excluir o WhatsApp
function excluir_whatsapp() {
    $user_id = get_current_user_id();
    delete_user_meta($user_id, 'whatsapp_teste');

    wp_send_json_success(['message' => 'WhatsApp excluído com sucesso!']);
}
add_action('wp_ajax_excluir_whatsapp', 'excluir_whatsapp');


