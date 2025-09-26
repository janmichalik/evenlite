<?php if (!empty($section['files'])): ?>
  <div class="section-file-list">
    <h2>Pliki:</h2>
    <ul>
      <?php foreach ($section['files'] as $file_item): ?>
        <?php
          $file_id = $file_item['file'];
          $file_url = wp_get_attachment_url($file_id);
          $file_title = get_the_title($file_id);
        ?>
        <li>
          <a href="<?php echo esc_url($file_url); ?>" target="_blank">
            <?php echo esc_html($file_title); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>