<?php

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

if (!function_exists('_o')) {
  function _o($option_key, $default = null)
  {
    return carbon_get_theme_option($option_key, $default);
  }
}

add_filter('gettext', function ($translated_text, $text, $domain) {
  return $translated_text;
}, 10, 3);


function cf_get_languages()
{
  return [
    'pl' => 'polski',
    'en' => 'angielski',
    'de' => 'niemiecki',
  ];
}

function cf_translation_fields()
{
  return [
    Field::make('select', 'lang', 'Język')
      ->set_options(cf_get_languages())
      ->set_width(15),
    Field::make('rich_text', 'label', 'Etykieta WYSIWYG')
      ->set_width(85),
  ];
}

function cf_get_translated_label($items)
{
  $current_lang = substr(get_locale(), 0, 2);
  foreach ($items as $item) {
    if (isset($item['lang']) && $item['lang'] === $current_lang) {
      $label = $item['label'];
      return is_array($label) && isset($label['value']) ? $label['value'] : (string) $label;
    }
  }
  if (!empty($items[0]['label'])) {
    $label = $items[0]['label'];
    return is_array($label) && isset($label['value']) ? $label['value'] : (string) $label;
  }
  return '';
}



function get_cf_translation($option_key, $default_label = '')
{
  $translations = carbon_get_theme_option($option_key);
  $current_lang = substr(get_locale(), 0, 2);

  if (is_array($translations)) {
    $index = array_search($current_lang, array_column($translations, 'lang'));
    if ($index !== false && !empty($translations[$index]['label'])) {
      return wp_kses_post($translations[$index]['label']);
    }
  }

  return wp_kses_post($default_label);
}

function _t($option_key, $default_label = '')
{
  return get_cf_translation($option_key, $default_label);
}

function _p($file = 'builder.php')
{
  $path = get_template_directory() . '/parts/' . $file;
  if (file_exists($path)) {
    include_once $path;
  }
}

add_action('after_setup_theme', function () {
  Carbon_Fields::boot();
});

add_action('carbon_fields_register_fields', function () {
  Container::make('theme_options', 'Opcje firmy')
    ->add_fields([
      Field::make('text', 'company_nip', 'Nip firmy'),
      Field::make('complex', 'company_nip_label', 'Etykieta NIP')
        ->add_fields(cf_translation_fields())
        ->set_max(count(cf_get_languages())),
      Field::make('complex', 'company_address_label', 'Etykieta Adres')
        ->add_fields(cf_translation_fields()),
      Field::make('complex', 'bottom_cols', 'Kolumny dolne')
        ->add_fields([
          Field::make('rich_text', 'label', 'Etykieta WYSIWYG'),
          Field::make('rich_text', 'nr', 'Numer WYSIWYG'),
        ])
        ->set_layout('tabbed-horizontal'),
    ]);

  Container::make('post_meta', __('Sekcje strony'))
    ->where('post_type', '=', 'page')
    ->where('post_template', '=', 'templates/home.php')
    ->add_fields([
      Field::make('complex', 'crb_sections', 'Sekcje')
        ->add_fields('text', 'Sekcja z tekstem', [
          Field::make('complex', 'text', 'Treść')
            ->add_fields(cf_translation_fields())
            ->set_max(count(cf_get_languages())),
        ])

        ->add_fields('file_list', 'Sekcja z listą plików', [
          Field::make('complex', 'files', 'Pliki')
            ->add_fields([
              Field::make('file', 'file', 'Plik'),
            ]),
        ])
        ->add_fields('related_posts', 'Sekcja z powiązanymi wpisami', [
          Field::make('association', 'posts', 'Wpisy')
            ->set_types([
              ['type' => 'post', 'post_type' => 'post'],
            ]),
        ])
        ->add_fields('gutenberg_post', 'Sekcja Gutenberg', [
          Field::make('association', 'block_post', 'Wybierz wpis blokowy')
            ->set_types([
              ['type' => 'post', 'post_type' => 'section'],
            ]),
        ])
    ]);
});

add_filter('locale', function ($locale) {
  if (isset($_GET['lang']) && array_key_exists($_GET['lang'], cf_get_languages())) {
    $locale = $_GET['lang'] . '_' . strtoupper($_GET['lang']);
  }
  return $locale;
});

// auto disable used language options in admin select fields
add_action('admin_footer', function () {
?>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const selectorPattern = '[name^="carbon_fields_compact_input[_company_nip_label]"][name$="[_lang]"]';

      function updateLangSelects() {
        const selects = document.querySelectorAll(selectorPattern);
        if (selects.length === 0) return;
        const allLangs = Array.from(selects[0].options).map(opt => opt.value).filter(v => v);
        const langCount = {};
        selects.forEach(select => {
          if (select.value) {
            langCount[select.value] = (langCount[select.value] || 0) + 1;
          }
        });
        selects.forEach((select, idx) => {
          Array.from(select.options).forEach((option) => {
            if (!option.value) return;
            const usedCount = langCount[option.value] || 0;
            const isCurrent = select.value === option.value;
            option.disabled = usedCount > (isCurrent ? 1 : 0);
          });
        });
        const needsReset = [];
        selects.forEach(select => {
          const current = select.options[select.selectedIndex];
          if (!select.value || (current && current.disabled)) {
            needsReset.push(select);
          }
        });
        if (needsReset.length === 0) return;
        const usedLangs = new Set();
        selects.forEach(select => {
          if (!needsReset.includes(select) && select.value) {
            usedLangs.add(select.value);
          }
        });
        let available = allLangs.filter(l => !usedLangs.has(l));
        available.sort((a, b) => allLangs.indexOf(a) - allLangs.indexOf(b));
        let changesMade = false;
        needsReset.forEach(select => {
          if (available.length > 0) {
            const next = available.shift();
            if (select.value !== next) {
              select.value = next;
              changesMade = true;
            }
          } else {
            if (select.value !== '') {
              select.value = '';
              changesMade = true;
            }
          }
        });
        if (changesMade) {
          setTimeout(updateLangSelects, 100);
        }
      }
      document.body.addEventListener('change', (event) => {
        if (event.target.matches(selectorPattern)) {
          setTimeout(updateLangSelects, 100);
        }
      });
      const observer = new MutationObserver(() => {
        setTimeout(updateLangSelects, 100);
      });
      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
      setTimeout(() => {
        updateLangSelects();
      }, 500);
    });
  </script>
<?php
});
