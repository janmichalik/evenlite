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
    $plugin_dir = plugin_dir_path(file: __FILE__) . 'settings-app';
    $main_js = evenlite_get_hashed_file($plugin_dir, 'main', 'js') ?? '';
    $plugin_url = plugins_url('settings-app', __FILE__);
    if ($hook === 'toplevel_page_evenlite-events') {
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
    if ($main_js && $plugin_url) {
        wp_enqueue_script('settings-main', "$plugin_url/$main_js", [], null, true);

        wp_localize_script('settings-main', 'EvenliteAPI', [
            'nonce' => wp_create_nonce('wp_rest'),
            'restUrl' => esc_url_raw(rest_url()),
            'userId' => get_current_user_id(),
            'userRole' => wp_get_current_user()->roles,
        ]);
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

function evenlite_expose_nonce()
{
    wp_localize_script('evenlite-angular', 'EvenliteAPI', [
        'nonce' => wp_create_nonce('wp_rest'),
        'restUrl' => esc_url_raw(rest_url())
    ]);
}
add_action('wp_enqueue_scripts', 'evenlite_expose_nonce');


function evenlite_create_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $tables = [];

    // Bookings
    $tables[] = "CREATE TABLE {$wpdb->prefix}evenlite_bookings (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        event_id BIGINT UNSIGNED NOT NULL,
        user_id BIGINT UNSIGNED NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Events
    $tables[] = "CREATE TABLE {$wpdb->prefix}evenlite_events (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        start DATETIME NOT NULL,
        end DATETIME NOT NULL,
        location_id BIGINT UNSIGNED,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Invoices
    $tables[] = "CREATE TABLE {$wpdb->prefix}evenlite_invoices (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        booking_id BIGINT UNSIGNED NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        paid BOOLEAN DEFAULT FALSE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Locations
    $tables[] = "CREATE TABLE {$wpdb->prefix}evenlite_locations (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        address TEXT,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Rooms
    $tables[] = "CREATE TABLE {$wpdb->prefix}evenlite_rooms (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        location_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(255) NOT NULL,
        capacity INT DEFAULT 0,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    foreach ($tables as $sql) {
        dbDelta($sql);
    }
}
