<?php get_header(); ?>

<main id="primary" class="content-area">
    <?php
    if (is_front_page() && get_option('show_on_front') === 'page') {
        while (have_posts()) {
            the_post();
            the_title('<h1 class="screen-reader-text">', '</h1>');
            the_content();
        }
        rewind_posts();
    }
    ?>

    <?php if (is_active_sidebar('home_cards')) : ?>
        <div class="crosstalk-cards-wrap">
            <?php dynamic_sidebar('home_cards'); ?>
        </div>
    <?php endif; ?>

    <?php
    if (is_front_page() && get_option('show_on_front') !== 'page' && have_posts()) :
        while (have_posts()) : the_post();
            the_title('<h1>', '</h1>');
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
