<?php
  add_action('admin_menu', function () {
    add_menu_page(
        'Wydarzenia',               // Tytuł strony
        'Wydarzenia',               // Tytuł w menu
        'manage_options',           // Uprawnienia (np. tylko administrator)
        'evenlite-events',          // Slug
        'evenlite_events_admin',    // Callback do renderowania
        'dashicons-calendar-alt',   // Ikona
        25                          // Pozycja w menu
    );
});

function evenlite_events_admin() {
    echo '<div id="events-admin-app"></div>';
}
