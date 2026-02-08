<?php
/**
 * Card widget for home page: title, description, optional image, optional link.
 * Styled like Elementor call-to-action (CTA) cards.
 */
if (! defined('ABSPATH')) {
    exit;
}

class Crosstalk_Card_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'crosstalk_card',
            __('Crosstalk Card (single)', 'crosstalk'),
            [
                'classname'   => 'crosstalk-card-widget',
                'description' => __('Single card only. For multiple cards with Add/Delete, use "Home Page Cards" instead.', 'crosstalk'),
            ]
        );
    }

    public function widget($args, $instance)
    {
        $title       = ! empty($instance['title']) ? $instance['title'] : '';
        $description = ! empty($instance['description']) ? $instance['description'] : '';
        $image_id   = ! empty($instance['image_id']) ? (int) $instance['image_id'] : 0;
        $link_url   = ! empty($instance['link_url']) ? $instance['link_url'] : '';

        if ($title === '' && $description === '' && $image_id === 0) {
            return;
        }

        echo $args['before_widget'];

        $crop_x   = isset($instance['crop_x']) && $instance['crop_x'] !== '' ? $instance['crop_x'] : null;
        $crop_y   = isset($instance['crop_y']) && $instance['crop_y'] !== '' ? $instance['crop_y'] : null;
        $content = $this->render_card_content($title, $description, $image_id, $crop_x, $crop_y);
        if ($content === '') {
            echo $args['after_widget'];
            return;
        }

        if ($link_url !== '') {
            $link_url = esc_url($link_url);
            printf(
                '<a class="crosstalk-cta-card" href="%1$s"%2$s>%3$s</a>',
                $link_url,
                (strpos($link_url, home_url()) !== 0 ? ' target="_blank" rel="noopener noreferrer"' : ''),
                $content
            );
        } else {
            echo '<div class="crosstalk-cta-card">' . $content . '</div>';
        }

        echo $args['after_widget'];
    }

    /**
     * @return string HTML for image + title + description (no wrapper).
     */
    private function render_card_content($title, $description, $image_id, $crop_x = null, $crop_y = null)
    {
        $out = '';

        if ($image_id > 0) {
            $img = wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async', 'class' => 'crosstalk-cta-card__img']);
            if ($img !== '') {
                $style = '';
                if ($crop_x !== null && $crop_y !== null && $crop_x !== '' && $crop_y !== '') {
                    $x = is_numeric($crop_x) ? max(0, min(100, (float) $crop_x)) : 50;
                    $y = is_numeric($crop_y) ? max(0, min(100, (float) $crop_y)) : 50;
                    $style = ' style="--crop-x:' . esc_attr($x) . '%;--crop-y:' . esc_attr($y) . '%;"';
                }
                $out .= '<div class="crosstalk-cta-card__content-item crosstalk-cta-card__image"' . $style . '>' . $img . '</div>';
            }
        }

        if ($title !== '') {
            $out .= '<h2 class="crosstalk-cta-card__title crosstalk-cta-card__content-item">' . esc_html($title) . '</h2>';
        }

        if ($description !== '') {
            $out .= '<div class="crosstalk-cta-card__description crosstalk-cta-card__content-item">' . wp_kses_post(wpautop($description)) . '</div>';
        }

        if ($out === '') {
            return '';
        }

        return '<div class="crosstalk-cta-card__content">' . $out . '</div>';
    }

    public function form($instance)
    {
        $title       = isset($instance['title']) ? $instance['title'] : '';
        $description = isset($instance['description']) ? $instance['description'] : '';
        $image_id   = isset($instance['image_id']) ? (int) $instance['image_id'] : 0;
        $link_url   = isset($instance['link_url']) ? $instance['link_url'] : '';
        $crop_x     = isset($instance['crop_x']) ? $instance['crop_x'] : '';
        $crop_y     = isset($instance['crop_y']) ? $instance['crop_y'] : '';

        $image_url = $image_id > 0 ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $id_input  = $this->get_field_id('image_id');
        $id_name   = $this->get_field_name('image_id');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title', 'crosstalk'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>"><?php esc_html_e('Description', 'crosstalk'); ?></label>
            <textarea class="widefat" rows="4" id="<?php echo esc_attr($this->get_field_id('description')); ?>" name="<?php echo esc_attr($this->get_field_name('description')); ?>"><?php echo esc_textarea($description); ?></textarea>
        </p>
        <p>
            <label><?php esc_html_e('Image', 'crosstalk'); ?></label>
            <span class="description" style="display:block;margin-bottom:4px;"><?php esc_html_e('Cards use a 16:9 image ratio. If your image isn’t 16:9, a crop step will appear below after you select one.', 'crosstalk'); ?></span>
            <span class="crosstalk-card-widget-image-wrap">
                <input type="hidden" id="<?php echo esc_attr($id_input); ?>" name="<?php echo esc_attr($id_name); ?>" value="<?php echo (int) $image_id; ?>">
                <span class="crosstalk-card-widget-preview">
                    <?php if ($image_url) : ?>
                        <?php if ($crop_x !== '' && $crop_y !== '' && is_numeric($crop_x) && is_numeric($crop_y)) : ?>
                            <span style="display:block; overflow:hidden; aspect-ratio:16/9; max-width:100%;">
                                <img src="<?php echo esc_url($image_url); ?>" alt="" style="width:100%; height:100%; object-fit:cover; object-position:<?php echo esc_attr($crop_x); ?>% <?php echo esc_attr($crop_y); ?>%; display:block;">
                            </span>
                        <?php else : ?>
                            <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width:100%;height:auto;display:block;">
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
                <button type="button" class="button crosstalk-card-widget-select-image"><?php esc_html_e('Select image', 'crosstalk'); ?></button>
                <button type="button" class="button crosstalk-card-widget-remove-image" <?php echo $image_id ? '' : ' style="display:none;"'; ?>><?php esc_html_e('Remove image', 'crosstalk'); ?></button>
            </span>
        </p>
        <input type="hidden" class="crosstalk-crop-x" name="<?php echo esc_attr($this->get_field_name('crop_x')); ?>" value="<?php echo esc_attr($crop_x); ?>">
        <input type="hidden" class="crosstalk-crop-y" name="<?php echo esc_attr($this->get_field_name('crop_y')); ?>" value="<?php echo esc_attr($crop_y); ?>">
        <p class="crosstalk-card-crop-panel" style="display:none;">
            <label class="crosstalk-card-crop-panel-title"><?php esc_html_e('This image isn’t 16:9 — drag the box to choose which part to show', 'crosstalk'); ?></label>
            <span class="crosstalk-card-crop-wrap">
                <img class="crosstalk-card-crop-image" src="" alt="" style="max-width:100%;height:auto;display:block;">
                <span class="crosstalk-card-crop-box" role="img" aria-label="<?php esc_attr_e('Crop selection', 'crosstalk'); ?>"></span>
            </span>
            <button type="button" class="button crosstalk-card-apply-crop" style="margin-top:8px;"><?php esc_html_e('Apply crop', 'crosstalk'); ?></button>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('link_url')); ?>"><?php esc_html_e('Link URL (optional)', 'crosstalk'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('link_url')); ?>" name="<?php echo esc_attr($this->get_field_name('link_url')); ?>" type="url" value="<?php echo esc_attr($link_url); ?>" placeholder="https://">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title']       = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['description'] = isset($new_instance['description']) ? wp_kses_post($new_instance['description']) : '';
        $instance['image_id']   = isset($new_instance['image_id']) ? absint($new_instance['image_id']) : 0;
        $instance['link_url']   = isset($new_instance['link_url']) ? esc_url_raw($new_instance['link_url']) : '';
        $instance['crop_x']    = isset($new_instance['crop_x']) && $new_instance['crop_x'] !== '' ? sanitize_text_field($new_instance['crop_x']) : '';
        $instance['crop_y']    = isset($new_instance['crop_y']) && $new_instance['crop_y'] !== '' ? sanitize_text_field($new_instance['crop_y']) : '';
        return $instance;
    }
}
