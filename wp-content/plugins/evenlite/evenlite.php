<?php

/**
 * Plugin Name: Evenlite
 * Description: Booking plugin with Angular frontend.
 * Version: 1.0
 * Author: Jan Michalik
 */

function evenlite_get_hashed_file($dir, $prefix, $ext)
{
    foreach (glob($dir . "/$prefix.*.$ext") as $file) {
        return basename($file);
    }
    return null;
}

add_action('admin_menu', function () {
    add_menu_page(
        'Wydarzenia',
        'Wydarzenia',
        'manage_options',
        'evenlite-events',
        'evenlite_events_admin_page',
        'dashicons-calendar-alt',
        25
    );
});

function evenlite_events_admin_page()
{
    echo '<els-root></els-root>';
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'toplevel_page_evenlite-events') {
        $plugin_dir = plugin_dir_path(__FILE__) . 'settings-app';
        $plugin_url = plugins_url('settings-app', __FILE__);
        $main_js = evenlite_get_hashed_file($plugin_dir, 'main', 'js');
        $styles_css = evenlite_get_hashed_file($plugin_dir, 'styles', 'css');
        $runtime_js = evenlite_get_hashed_file($plugin_dir, 'runtime', 'js');
        $polyfills_js = evenlite_get_hashed_file($plugin_dir, 'polyfills', 'js');
        $main_js = evenlite_get_hashed_file($plugin_dir, 'main', 'js');
        if ($styles_css) {
            wp_enqueue_style('settings-app-style', "$plugin_url/$styles_css");
        }
        if ($runtime_js) {
            wp_enqueue_script('settings-runtime', "$plugin_url/$runtime_js", [], null, true);
        }
        if ($polyfills_js) {
            wp_enqueue_script('settings-polyfills', "$plugin_url/$polyfills_js", [], null, true);
        }
        if ($main_js) {
            wp_enqueue_script('settings-main', "$plugin_url/$main_js", [], null, true);
        }
    }
});

add_action('rest_api_init', function () {
  register_rest_route('evenlite/v1', '/session', [
    'methods' => 'GET',
    'callback' => function () {
      if (is_user_logged_in()) {
        $user = wp_get_current_user();
        return [
          'logged_in' => true,
          'username' => $user->user_login,
          'roles' => $user->roles,
          'is_admin' => in_array('administrator', $user->roles)
        ];
      }
      return ['logged_in' => false];
    },
    'permission_callback' => '__return_true'
  ]);
});
