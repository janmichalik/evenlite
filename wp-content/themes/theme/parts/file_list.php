<?php if (!empty($section['files'])): ?>
  <div class="section-file-list">
    <h2>Pliki:</h2>
    <ul>
      <?php foreach ($section['files'] as $file_item): 
        $file_id = isset($file_item['file']) ? $file_item['file'] : null;
        if ($file_id) {
          $file_url = wp_get_attachment_url($file_id);
          $file_title = get_the_title($file_id);
        }
        ?>
        <li>
          <?php if ($file_id && $file_url): ?>
            <a href="<?= esc_url($file_url) ?>" target="_blank">
              <?= esc_html($file_title) ?>
            </a>
          <?php else: ?>
            <span>Brak pliku</span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>