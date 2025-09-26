<?php
function enqueue_theme_styles() {
    if ($GLOBALS['pagenow'] !== 'wp-login.php' && !is_admin()) {
        $theme_public_path = get_template_directory_uri() . '/public';
        wp_enqueue_style(
            'jmic-style',
            $theme_public_path . '/bundle.css',
            [],
            filemtime(get_template_directory() . '/public/bundle.css')
        );
    }
}

function enqueue_theme_scripts() {
    if ($GLOBALS['pagenow'] !== 'wp-login.php' && !is_admin()) {
        $theme_public_path = get_template_directory_uri() . '/public';
        wp_enqueue_script(
            'jmic-script',
            $theme_public_path . '/bundle.js',
            [],
            filemtime(get_template_directory() . '/public/bundle.js'),
            true
        );
    }
}

add_action('wp_enqueue_scripts', 'enqueue_theme_styles');
add_action('wp_enqueue_scripts', 'enqueue_theme_scripts');
