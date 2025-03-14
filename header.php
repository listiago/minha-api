<?php
/**
 * Custom Header for the theme
 *
 * @package api_web_hook
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Container principal -->
<div id="page" class="site">

    <!-- MENU SUPERIOR -->
    <header id="masthead" class="site-header">
        <div class="top-bar">
            <div class="site-branding">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title"><?php bloginfo( 'name' ); ?></a>
            </div>
            <nav id="site-navigation" class="main-navigation">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'menu-1',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'menu_class'     => 'top-menu',
                ) );
                ?>
            </nav>
            <!-- Botão para abrir o menu lateral -->
            <button id="menu-toggle" class="menu-toggle">☰</button>
        </div>

        <!-- Verifica se o usuário está logado e exibe o botão 'Sair' -->
        <?php if (is_user_logged_in()) : ?>
            <div class="user-logout">
                <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="logout-link">Sair</a>
            </div>
        <?php endif; ?>
    </header>

    <!-- MENU LATERAL -->
    <aside id="sidebar-menu" class="sidebar-menu">
        <button id="close-menu" class="close-menu">✖</button>
        <h2>Menu</h2>
        <?php
        wp_nav_menu( array(
            'theme_location' => 'menu-2',
            'menu_id'        => 'sidebar-menu-list',
            'container'      => false,
            'menu_class'     => 'sidebar-menu-items',
        ) );
        ?>
    </aside>

    <!-- Sobreposição do menu lateral -->
    <div id="overlay" class="overlay"></div>

    <!-- Conteúdo principal -->
    <div id="content" class="site-content">

<style>
    .user-logout {
        margin-left: 20px;
        display: inline-block;
    }

    .logout-link {
        background-color: #f44336; /* Vermelho */
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        border-radius: 5px;
    }

    .logout-link:hover {
        background-color: #e53935; /* Vermelho mais escuro */
    }
</style>

</div> <!-- Fechando o div do conteúdo -->
</div> <!-- Fechando o container principal -->

<?php wp_footer(); ?>
</body>
</html>
