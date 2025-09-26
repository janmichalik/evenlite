<?php
if (!empty($section['block_post'][0]['id'])) {
  echo '<div class="section-gutenberg-post">';
  echo do_blocks(get_post_field('post_content', $section['block_post'][0]['id']));
  echo '</div>';
}