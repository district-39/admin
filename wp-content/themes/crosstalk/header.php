<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="site-header" role="banner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="site-icon-link" id="site-header-title" aria-label="<?php echo esc_attr(__('Home', 'crosstalk')); ?>">
        <?php
        $site_icon_url = get_site_icon_url(48);
        if ($site_icon_url) :
            ?>
            <img src="<?php echo esc_url($site_icon_url); ?>" alt="" width="48" height="48" class="site-icon" fetchpriority="high">
            <?php
        endif;
        ?>
        <span class="site-header-title"><?php echo esc_html(get_bloginfo('name')); ?></span>
    </a>
    <?php
    if (is_post_type_archive('tsml_meeting') && has_nav_menu('header')) {
        ?>
        <div class="header-nav-group">
            <div class="header-ctas">
                <a href="https://www.aa.org/aa-meeting-finder-app" class="header-app-cta" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(__('Try the official AA Meetings app (opens in new tab)', 'crosstalk')); ?>">Try the official AA Meetings app</a>
                <a href="https://codeforrecovery.org" class="header-builtby-cta" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(__('Built by Code for Recovery (opens in new tab)', 'crosstalk')); ?>">Built By Code For Recovery</a>
            </div>
            <?php
            wp_nav_menu(array(
                'theme_location'      => 'header',
                'menu_id'             => 'header-menu',
                'container'           => 'nav',
                'container_aria_label' => __('Header navigation', 'crosstalk'),
                'fallback_cb'         => false,
            ));
            ?>
        </div>
        <?php
    } elseif (has_nav_menu('header')) {
        wp_nav_menu(array(
            'theme_location'      => 'header',
            'menu_id'             => 'header-menu',
            'container'           => 'nav',
            'container_aria_label' => __('Header navigation', 'crosstalk'),
            'fallback_cb'         => false,
        ));
    }
    ?>
</header>
