<?php
echo '<div class="section-related-posts"><h2>Powiązane wpisy:</h2><ul>';
foreach ($section['posts'] as $post_item) {
  echo '<li><a href="' . get_the_permalink($post_item['id']) . '">'
    . get_the_title($post_item['id']) . '</a></li>';
}
echo '</ul></div>';