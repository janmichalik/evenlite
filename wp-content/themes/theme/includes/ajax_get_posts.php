<?php
add_action('wp_ajax_nopriv_get_posts_list', 'get_posts_list');
add_action('wp_ajax_get_posts_list', 'get_posts_list');

function get_posts_list()
{
  $posts = get_posts([
    'numberposts' => 5,
    'post_status' => 'publish',
  ]);

  $result = array_map(function ($post) {
    return [
      'id' => $post->ID,
      'title' => $post->post_title,
      'link' => get_permalink($post->ID),
    ];
  }, $posts);

  wp_send_json($result);
}