<?php
function cf_detect_section_diff($old, $new)
{
  $old_types = is_array($old) ? array_map(fn($s) => $s['_type'] ?? '', $old) : [];
  $new_types = is_array($new) ? array_map(fn($s) => $s['_type'] ?? '', $new) : [];
  $added = array_diff($new_types, $old_types);
  $removed = array_diff($old_types, $new_types);
  $labels = [];
  foreach ($added as $type) {
    $labels[] = '+ ' . cf_section_type_label($type);
  }
  foreach ($removed as $type) {
    $labels[] = '– ' . cf_section_type_label($type);
  }
  return implode(', ', $labels);
}
function cf_extract_all_text_fields($value)
{
  if (!is_array($value)) return '';
  $output = '';
  foreach ($value as $index => $section) {
    if (is_array($section) && ($section['_type'] ?? '') === 'text') {
      $text = $section['text'] ?? '';
      $output .= '<div style="margin-bottom:1em;border-bottom:1px dashed #ccc;padding-bottom:0.5em;">';
      $output .= '<strong>#' . ($index + 1) . ':</strong><br>';
      $output .= wp_kses_post($text);
      $output .= '</div>';
    }
  }
  return $output ?: '<em>Brak sekcji tekstowych</em>';
}

function cf_field_labels()
{
  return [
    'crb_sections' => 'Sekcje',
    'company_nip' => 'NIP firmy',
    'company_nip_label' => 'Etykieta NIP',
    'company_address_label' => 'Etykieta Adres',
    'bottom_cols' => 'Kolumny dolne',
  ];
}

function cf_section_type_label($type)
{
  return [
    'text' => 'Tekst',
    'file_list' => 'Lista plików',
    'related_posts' => 'Powiązane wpisy',
    'gutenberg_post' => 'Blok Gutenberg',
  ][$type] ?? ucfirst($type);
}

function cf_get_field_label($field_key)
{
  $containers = \Carbon_Fields\Container\Container::get_all();

  foreach ($containers as $container) {
    $fields = $container->get_fields();
    if (!is_array($fields)) continue;

    foreach ($fields as $field) {
      if (method_exists($field, 'get_name') && $field->get_name() === $field_key) {
        return method_exists($field, 'get_label') ? $field->get_label() : $field_key;
      }
    }
  }

  return $field_key;
}

function get_cf_snapshot($post_id)
{
  $keys = ['crb_sections'];
  $snapshot = [];
  foreach ($keys as $key) {
    $snapshot[$key] = carbon_get_post_meta($post_id, $key);
  }
  file_put_contents(
    json_encode([
      'snapshot' => $snapshot,
      'post_id' => $post_id,
      'timestamp' => current_time('mysql'),
      'source' => 'get_cf_snapshot',
      'raw_crb_sections' => carbon_get_post_meta($post_id, 'crb_sections')
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . ",\n",
    FILE_APPEND
  );


  return $snapshot;
}
function cf_create_change_log_table()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'cf_change_log';

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") !== $table_name) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
            CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT UNSIGNED NOT NULL,
                field_key VARCHAR(255) NOT NULL,
                old_value TEXT,
                new_value TEXT,
                changed_at DATETIME NOT NULL,
                user_id BIGINT UNSIGNED,
                INDEX(post_id),
                INDEX(changed_at)
            ) $charset_collate;
        ";

    dbDelta($sql);
  }
}
add_action('after_switch_theme', 'cf_create_change_log_table');

add_action('carbon_fields_post_meta_container_saved', function ($post_id) {
  $new_fields = get_cf_snapshot($post_id);
  $old_fields = get_post_meta($post_id, '_cf_last_snapshot', true) ?: [];

  foreach ($new_fields['crb_sections'] as $index => $new_section) {
    $old_section = $old_fields['crb_sections'][$index] ?? null;

    if (json_encode($new_section) !== json_encode($old_section)) {
      global $wpdb;
      $wpdb->insert($wpdb->prefix . 'cf_change_log', [
        'post_id' => $post_id,
        'field_key' => 'crb_sections[' . $index . ']',
        'old_value' => maybe_serialize($old_section),
        'new_value' => maybe_serialize($new_section),
        'changed_at' => current_time('mysql'),
        'user_id' => get_current_user_id(),
      ]);
    }
  }

  update_post_meta($post_id, '_cf_last_snapshot', $new_fields);

  global $wpdb;
  $wpdb->query("
        DELETE FROM {$wpdb->prefix}cf_change_log
        WHERE id NOT IN (
            SELECT id FROM (
                SELECT id FROM {$wpdb->prefix}cf_change_log
                WHERE post_id = {$post_id}
                ORDER BY changed_at DESC
                LIMIT 20
            ) AS temp
        ) AND post_id = {$post_id}
    ");
});
add_action('add_meta_boxes', function () {
  add_meta_box('cf_history', 'Historia zmian', function ($post) {
    global $wpdb;
    $rows = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM {$wpdb->prefix}cf_change_log
            WHERE post_id = %d
            ORDER BY changed_at DESC
            LIMIT 20
        ", $post->ID));

    echo '<style>
            #cf-history-table th, #cf-history-table td {
                padding: 5px;
                border: 1px solid #ddd;
                vertical-align: top;
            }
            #cf-history-table th {
                background-color: #f9f9f9;
                text-align: left;
            }
            #cf-history-table tr:nth-child(even) {
                background-color: #f6f6f6;
            }
            #cf-history-table pre {
                margin: 0;
                font-size: 13px;
                line-height: 1.4;
                white-space: pre-wrap;
                word-break: break-word;
            }
            #poststuff .inside {
                margin: 0;
                padding: 10px;
            }
            #cf-history-table  td {
                    overflow-x: auto;
                    line-break: anywhere;
                }
            #cf-history-table  td:nth-child(1) {
                    white-space:nowrap;
                }
                           #cf-history-table  td:nth-child(2) {
                    white-space:nowrap;
                }
                            #cf-history-table  td:nth-child(3) {
                    white-space:nowrap;
                }  
        </style>';

    echo '<table id="cf-history-table" style="width:100%; border-collapse:collapse;">';
    echo '<thead><tr><th>Data</th><th>Pole</th><th>Użytkownik</th><th>Stara wartość</th><th>Nowa wartość</th></tr></thead><tbody>';

    foreach ($rows as $row) {
      $old = maybe_unserialize($row->old_value);
      $new = maybe_unserialize($row->new_value);
      echo '<tr>';
      echo '<td>' . esc_html($row->changed_at) . '</td>';
      $label = $row->field_key;
      if (preg_match('/crb_sections\[(\d+)\]/', $label, $matches)) {
        $index = (int)$matches[1];
        $type = $new['_type'] ?? ($old['_type'] ?? '');
        $label = cf_section_type_label($type) . ' #' . ($index + 1);
      }
      echo '<td>' . esc_html($label) . '</td>';
      $user = get_userdata($row->user_id);
      echo '<td>' . esc_html($user->display_name ?? '—') . '</td>';
      echo '<td>' . cf_extract_text_field($old) . '</td>';
      echo '<td>' . cf_extract_text_field($new) . '</td>';
      echo '</tr>';
    }
    echo '</tbody></table>';
  }, 'page', 'normal', 'default');
});


function cf_extract_text_field($section)
{
  if (!is_array($section)) return '<em>Brak danych</em>';

  if (($section['_type'] ?? '') === 'text') {
    $text = $section['text'] ?? '';
    return $text ? wp_kses_post($text) : '<em>Pusty tekst</em>';
  }

  return '<em>Nie jest sekcją tekstową</em>';
}

add_action('init', 'cf_ensure_change_log_table');

function cf_ensure_change_log_table()
{
  global $wpdb;

  $table_name = $wpdb->prefix . 'cf_change_log';

  if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
    return;
  }

  if (!function_exists('dbDelta')) {
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  }

  $charset_collate = $wpdb->get_charset_collate();

  $sql = "
        CREATE TABLE {$table_name} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            post_id BIGINT UNSIGNED NOT NULL,
            field_key VARCHAR(255) NOT NULL,
            old_value LONGTEXT,
            new_value LONGTEXT,
            changed_at DATETIME NOT NULL,
            user_id BIGINT UNSIGNED,
            INDEX(post_id),
            INDEX(changed_at)
        ) $charset_collate;
    ";

  dbDelta($sql);
}
