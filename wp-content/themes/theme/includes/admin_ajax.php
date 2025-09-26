<?php


function enqueue_my_scripts()
{
  wp_enqueue_script('my-script', get_template_directory_uri() . '/js/fetch-posts.js', [], null, true);

  wp_localize_script('my-script', 'wpData', [
    'ajaxurl' => admin_url('admin-ajax.php'),
  ]);
}
add_action('wp_enqueue_scripts', 'enqueue_my_scripts');
