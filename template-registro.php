<?php
/**
 * Template Name: Registro Personalizado
 */

if (is_user_logged_in()) {
    wp_redirect(home_url('/webhook')); // Redireciona usuário logado
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica nonce para evitar CSRF
    if (!isset($_POST['register_nonce']) || !wp_verify_nonce($_POST['register_nonce'], 'registro_usuario')) {
        $error_message = 'Erro de segurança. Tente novamente.';
    } else {
        // Recupera os dados do formulário
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Valida os dados
        if (trim($username) === '' || trim($email) === '' || trim($password) === '' || trim($confirm_password) === '') {
            $error_message = 'Por favor, preencha todos os campos.';
        } elseif (!validate_username($username)) {
            $error_message = 'Nome de usuário inválido.';
        } elseif (!is_email($email)) {
            $error_message = 'E-mail inválido.';
        } elseif ($password !== $confirm_password) {
            $error_message = 'As senhas não coincidem.';
        } elseif (username_exists($username) || email_exists($email)) {
            $error_message = 'O nome de usuário ou e-mail já estão registrados.';
        } else {
            // Cria o novo usuário
            $user_id = wp_create_user($username, $password, $email);

            // Verifica se houve erro na criação
            if (is_wp_error($user_id)) {
                $error_message = $user_id->get_error_message();
            } else {
                // Atribui a função 'subscriber'
                $user = new WP_User($user_id);
                $user->set_role('subscriber');

                // Desativa o editor visual e a barra de ferramentas
                update_user_meta($user_id, 'rich_editing', 'false');
                update_user_meta($user_id, 'show_admin_bar_front', 'false');

                $success_message = 'Cadastro realizado com sucesso! Redirecionando para login...';
                echo '<meta http-equiv="refresh" content="3;url=' . home_url('/login') . '">';
            }
        }
    }
}

get_header();
?>

<div class="register-container">
    <h2>Cadastro</h2>

    <?php if (!empty($error_message)) : ?>
        <p class="error-message"><?php echo esc_html($error_message); ?></p>
    <?php endif; ?>

    <?php if (!empty($success_message)) : ?>
        <p class="success-message"><?php echo esc_html($success_message); ?></p>
    <?php endif; ?>

    <form method="post" class="register-form">
        <input type="hidden" name="register_nonce" value="<?php echo wp_create_nonce('registro_usuario'); ?>">

        <label for="username">Nome de usuário</label>
        <input type="text" id="username" name="username" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirmar senha</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Cadastrar</button>
    </form>

    <p class="login-link">Já tem uma conta? <a href="<?php echo home_url('/login'); ?>">Faça login aqui</a></p>
</div>

<style>
    .register-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .register-form {
        display: flex;
        flex-direction: column;
    }
    .register-form input {
        padding: 10px;
        margin: 5px 0;
        width: 100%;
    }
    .register-form button {
        background-color: #0073aa;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
    }
    .register-form button:hover {
        background-color: #005a87;
    }
    .error-message {
        color: red;
    }
    .success-message {
        color: green;
    }
    .login-link {
        margin-top: 20px;
    }
    .login-link a {
        color: #0073aa;
        text-decoration: none;
    }
    .login-link a:hover {
        text-decoration: underline;
    }
</style>

<?php
get_footer();
?>
