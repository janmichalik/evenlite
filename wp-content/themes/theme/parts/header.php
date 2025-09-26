<div class="language-switcher">
    <?php
    $languages = cf_get_languages();
    $current_lang = substr(get_locale(), 0, 2);
    foreach ($languages as $lang_code => $lang_name) {
        $url = add_query_arg('lang', $lang_code, home_url('/'));
        $active = ($lang_code === $current_lang) ? ' active' : '';
        echo '<a href="' . esc_url($url) . '" class="language-link' . $active . '">' . esc_html($lang_name) . '</a> | ';
    }
    ?>
</div>
<header class="site-header">
    <h1 class="site-title">
        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
    </h1>
    <p class="site-description"><?php bloginfo('description'); ?></p>
    <?php
    wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_id' => 'primary-menu',
        'container' => 'nav',
        'container_class' => 'main-nav',
    ));
    ?>
    <br>
    <h2>Custom fields multilang test:</h2>
    <div class="index-page">
        <?php
        $label = _t('company_nip_label', 'NIP firmy');
        $nip = _o('company_nip'); ?>
        <?= $label . ': ' . $nip; ?>
    </div>
    <br>
    <h2>AJAX test:</h2>
    <div id="post-container"></div>
</header>