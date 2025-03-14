<?php
/**
 * Template Name: Login Personalizado
 */

// Permite o acesso à página de login mesmo que o usuário esteja logado
if (is_user_logged_in() && !is_page('/registro')) {
    wp_redirect(home_url('/webhook')); // Redireciona usuário logado para a página de webhooks
    exit;
}

$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    $credentials = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    ];

    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
        $error_message = 'Usuário ou senha inválidos!';
    } else {
        wp_redirect(home_url('/webhook'));
        exit;
    }
}


get_header();
?>

<div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($error_message)) : ?>
        <p class="error-message"><?php echo esc_html($error_message); ?></p>
    <?php endif; ?>

    <form method="post" class="login-form">
        <label for="username">Usuário</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Senha</label>
        <input type="password" id="password" name="password" required>

        <label class="remember-me">
            <input type="checkbox" name="remember"> Lembrar-me
        </label>

        <button type="submit">Entrar</button>
    </form>

    <p class="register-link">Não tem registro? <a href="<?php echo home_url('/registro'); ?>">Registre-se aqui</a></p>

</div>

<style>
    .login-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .login-form {
        display: flex;
        flex-direction: column;
    }
    .login-form input {
        padding: 10px;
        margin: 5px 0;
        width: 100%;
    }
    .login-form button {
        background-color: #0073aa;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
    }
    .login-form button:hover {
        background-color: #005a87;
    }
    .error-message {
        color: red;
    }
    .register-link {
        margin-top: 10px;
    }
</style>

<?php
get_footer();
?>
