<?php 
add_action('init', function () {
    register_post_type('section', [
        'label' => 'Sekcje blokowe',
        'public' => false,
        'show_ui' => true,
        'supports' => ['title', 'editor'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-layout'
    ]);
});
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
    return $post_type === 'section' ? true : false;
}, 10, 2);
add_action('admin_init', function () {
    remove_post_type_support('page', 'editor');
});