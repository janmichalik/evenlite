<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php wp_title(''); ?><?php if (wp_title('', false)) echo ' : '; ?><?php bloginfo('name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="Jan Michalik">
    <meta property="og:title" content="<?php wp_title(''); ?>">
    <meta property="og:description" content="<?php bloginfo('description'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo esc_url(get_theme_file_uri('favicon.ico')); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php _p('header.php') ?>