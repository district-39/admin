<footer id="site-footer" role="contentinfo">
    <?php
    if (has_nav_menu('footer')) {
        wp_nav_menu(array(
            'theme_location'       => 'footer',
            'menu_id'              => 'footer-menu',
            'container'            => 'nav',
            'container_aria_label' => __('Footer navigation', 'crosstalk'),
            'fallback_cb'          => false,
        ));
    }
    ?>
</footer>