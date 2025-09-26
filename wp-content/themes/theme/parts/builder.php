<?php
$sections = carbon_get_the_post_meta('crb_sections');
foreach ($sections as $section) {
  $template = get_template_directory() . '/parts/' . $section['_type'] . '.php';
  if (file_exists($template)) {
    include $template;
  }
}
?>