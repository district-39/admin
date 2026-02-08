<?php
// Prevent direct access
if (! defined('ABSPATH')) exit;

function my_custom_landing_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'gallery', 'caption']);
    // Use classic Widgets screen so Home Page Cards widget form (card list, Delete, Add) displays correctly.
    remove_theme_support('widgets-block-editor');
}
add_action('after_setup_theme', 'my_custom_landing_setup');

function my_custom_landing_scripts()
{
    wp_enqueue_style('custom-style', get_stylesheet_uri(), [], '1.0.0');
    wp_enqueue_style('custom-css', get_template_directory_uri() . '/assets/css/custom.css', [], '1.0.2');
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/main.js', ['jquery'], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'my_custom_landing_scripts');

function mytheme_setup()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'crosstalk'),
        'header'  => __('Header Menu', 'crosstalk'),
        'footer'  => __('Footer Menu', 'crosstalk'),
    ));
}
add_action('after_setup_theme', 'mytheme_setup');

/**
 * Add ARIA and classes to menu links.
 */
function crosstalk_nav_menu_link_attributes($atts, $item, $args, $depth)
{
    $atts['class'] = isset($atts['class']) ? $atts['class'] . ' nav-link' : 'nav-link';

    $is_current = in_array('current-menu-item', $item->classes, true)
        || in_array('current_page_item', $item->classes, true);
    if ($is_current) {
        $atts['aria-current'] = 'page';
    }

    $title = trim($item->title);
    if ($title !== '') {
        $atts['aria-label'] = $is_current
            ? sprintf(/* translators: %s: link text */ __('%s (current page)', 'crosstalk'), $title)
            : sprintf(/* translators: %s: link text */ __('Go to %s', 'crosstalk'), $title);
    }

    if (!empty($item->target) && $item->target === '_blank') {
        $atts['rel'] = isset($atts['rel']) ? trim($atts['rel'] . ' noopener noreferrer') : 'noopener noreferrer';
        if (!empty($atts['aria-label'])) {
            $atts['aria-label'] .= ' ' . __('(opens in new tab)', 'crosstalk');
        }
    }

    return $atts;
}
add_filter('nav_menu_link_attributes', 'crosstalk_nav_menu_link_attributes', 10, 4);

/**
 * Footer menu: add footer-category (no parent) or footer-page (has parent).
 * Also: highlight header nav item when on meetings archive (/meetings/).
 */
function crosstalk_nav_menu_css_class($classes, $item, $args, $depth)
{
    $location = is_object($args) ? ($args->theme_location ?? '') : ($args['theme_location'] ?? '');

    if ($location === 'footer') {
        if ((int) $item->menu_item_parent === 0) {
            $classes[] = 'footer-category';
        } else {
            $classes[] = 'footer-page';
        }
    }

    // Meetings archive: mark menu item as current when URL matches (e.g. /meetings/)
    if (is_post_type_archive('tsml_meeting') && ! empty($item->url)) {
        $archive_url = get_post_type_archive_link('tsml_meeting');
        if ($archive_url && untrailingslashit(parse_url($item->url, PHP_URL_PATH)) === untrailingslashit(parse_url($archive_url, PHP_URL_PATH))) {
            $classes[] = 'current-menu-item';
        }
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'crosstalk_nav_menu_css_class', 10, 4);

/**
 * On the Meetings page only: hide the "Business" header nav item.
 */
function crosstalk_exclude_business_nav_on_meetings($items, $args)
{
    if (! is_post_type_archive('tsml_meeting')) {
        return $items;
    }
    $location = is_object($args) ? ($args->theme_location ?? '') : ($args['theme_location'] ?? '');
    if ($location !== 'header') {
        return $items;
    }
    $filtered = array_filter($items, function ($item) {
        return strcasecmp(trim($item->title), 'Business') !== 0;
    });
    return array_values($filtered);
}
add_filter('wp_nav_menu_objects', 'crosstalk_exclude_business_nav_on_meetings', 10, 2);

/**
 * Home page widget area and Card widgets.
 */
require_once get_template_directory() . '/inc/class-crosstalk-card-widget.php';
require_once get_template_directory() . '/inc/class-crosstalk-cards-widget.php';

function crosstalk_register_sidebar()
{
    register_sidebar([
        'id'            => 'home_cards',
        'name'          => __('Home Page Cards', 'crosstalk'),
        'description'   => __('Expand this area first (click “Home Page Cards” or the arrow). Then use “Add widget” or “Add block” and select “Home Page Cards”. The form with “+ Add another card” opens inside that widget.', 'crosstalk'),
        'before_widget' => '<div class="crosstalk-cards-column">',
        'after_widget'  => '</div>',
        'before_title'  => '',
        'after_title'   => '',
        'class'         => 'crosstalk-sidebar-home-cards',
    ]);
}
add_action('widgets_init', 'crosstalk_register_sidebar');

function crosstalk_register_card_widget()
{
    register_widget('Crosstalk_Card_Widget');
    register_widget('Crosstalk_Cards_Widget');
}
add_action('widgets_init', 'crosstalk_register_card_widget');

function crosstalk_widget_admin_scripts($hook)
{
    if ($hook !== 'widgets.php' && $hook !== 'customize.php') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('media-views');
    wp_enqueue_script('media-models');
    wp_enqueue_style(
        'crosstalk-widget-card-admin',
        get_template_directory_uri() . '/assets/css/widget-card-admin.css',
        [],
        '1.0.2'
    );
    wp_enqueue_script(
        'crosstalk-widget-card',
        get_template_directory_uri() . '/assets/js/widget-card.js',
        ['jquery', 'media-upload', 'media-views', 'media-models'],
        '1.0.4',
        true
    );
    wp_enqueue_script(
        'crosstalk-widget-cards',
        get_template_directory_uri() . '/assets/js/widget-cards.js',
        ['jquery', 'jquery-ui-sortable'],
        '1.0.2',
        true
    );
}
add_action('admin_enqueue_scripts', 'crosstalk_widget_admin_scripts');
