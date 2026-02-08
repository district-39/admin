<?php
/**
 * Home Page Cards widget: multiple cards in one widget. Add, remove, reorder.
 */
if (! defined('ABSPATH')) {
    exit;
}

class Crosstalk_Cards_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'crosstalk_cards',
            __('Home Page Cards', 'crosstalk'),
            [
                'classname'   => 'crosstalk-cards-widget',
                'description' => __('Multiple cards: gray bar with ⋮⋮ and "Delete card", plus "+ Add another card" below. Use this for the home page.', 'crosstalk'),
            ]
        );
    }

    public function widget($args, $instance)
    {
        $cards = isset($instance['cards']) && is_array($instance['cards']) ? $instance['cards'] : [];
        $cards = array_values(array_filter($cards, function ($c) {
            return ! empty($c['title']) || ! empty($c['description']) || ! empty($c['image_id']);
        }));

        if (empty($cards)) {
            return;
        }

        echo $args['before_widget'];

        foreach ($cards as $card) {
            $html = $this->render_one_card($card);
            if ($html !== '') {
                echo '<div class="crosstalk-cards-column">' . $html . '</div>';
            }
        }

        echo $args['after_widget'];
    }

    private function render_one_card($card)
    {
        $title       = isset($card['title']) ? $card['title'] : '';
        $description = isset($card['description']) ? $card['description'] : '';
        $image_id   = isset($card['image_id']) ? (int) $card['image_id'] : 0;
        $link_url   = isset($card['link_url']) ? $card['link_url'] : '';
        $crop_x     = isset($card['crop_x']) && $card['crop_x'] !== '' ? $card['crop_x'] : null;
        $crop_y     = isset($card['crop_y']) && $card['crop_y'] !== '' ? $card['crop_y'] : null;

        $content = $this->render_card_content($title, $description, $image_id, $crop_x, $crop_y);
        if ($content === '') {
            return '';
        }

        if ($link_url !== '') {
            $link_url = esc_url($link_url);
            return sprintf(
                '<a class="crosstalk-cta-card" href="%1$s"%2$s>%3$s</a>',
                $link_url,
                (strpos($link_url, home_url()) !== 0 ? ' target="_blank" rel="noopener noreferrer"' : ''),
                $content
            );
        }
        return '<div class="crosstalk-cta-card">' . $content . '</div>';
    }

    private function render_card_content($title, $description, $image_id, $crop_x, $crop_y)
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
        $cards = isset($instance['cards']) && is_array($instance['cards']) ? $instance['cards'] : [];
        if (empty($cards)) {
            $cards = [['title' => '', 'description' => '', 'image_id' => 0, 'crop_x' => '', 'crop_y' => '', 'link_url' => '']];
        }

        $cards_base = $this->get_field_name('cards');
        ?>
        <div style="margin-bottom:12px; padding:10px 12px; background:#e7f5e9; border:1px solid #2e7d32; border-radius:4px;">
            <strong style="display:block; margin-bottom:4px;"><?php esc_html_e('Home Page Cards (multiple)', 'crosstalk'); ?></strong>
            <span style="font-size:13px;"><?php esc_html_e('Below: card blocks with a gray bar (⋮⋮ and "Delete card") and "+ Add another card" button.', 'crosstalk'); ?></span>
        </div>
        <p class="description" style="margin-bottom:12px;"><?php esc_html_e('Drag ⋮⋮ to reorder. Use "Delete card" to remove one.', 'crosstalk'); ?></p>
        <div class="crosstalk-cards-list" data-cards-base="<?php echo esc_attr($cards_base); ?>" style="min-height:60px;">
            <?php
            foreach ($cards as $i => $card) {
                $this->render_card_form($card, $cards_base, $i);
            }
            ?>
            <p class="crosstalk-cards-add-wrap" style="margin:12px 0 0 0; padding:10px 0; border-top:1px solid #c3c4c7;">
                <button type="button" class="button button-primary crosstalk-cards-add"><?php esc_html_e('+ Add another card', 'crosstalk'); ?></button>
            </p>
        </div>
        <script type="text/html" class="crosstalk-cards-template" data-cards-base="<?php echo esc_attr($cards_base); ?>">
            <?php $this->render_card_form(['title' => '', 'description' => '', 'image_id' => 0, 'crop_x' => '', 'crop_y' => '', 'link_url' => ''], $cards_base, '__INDEX__'); ?>
        </script>
        <?php
    }

    private function render_card_form(array $card, $cards_base, $index)
    {
        $title       = isset($card['title']) ? $card['title'] : '';
        $description = isset($card['description']) ? $card['description'] : '';
        $image_id   = isset($card['image_id']) ? (int) $card['image_id'] : 0;
        $link_url   = isset($card['link_url']) ? $card['link_url'] : '';
        $crop_x     = isset($card['crop_x']) ? $card['crop_x'] : '';
        $crop_y     = isset($card['crop_y']) ? $card['crop_y'] : '';

        $image_url = $image_id > 0 ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $name = $cards_base . '[' . $index . ']';
        $is_template = ($index === '__INDEX__');
        ?>
        <div class="crosstalk-cards-card-block" data-index="<?php echo esc_attr((string) $index); ?>" style="margin-bottom:1rem; border:1px solid #c3c4c7; border-radius:4px; background:#fff;">
            <div class="crosstalk-cards-card-head" style="display:flex; align-items:center; gap:8px; padding:8px 10px; background:#f0f0f1; border-bottom:1px solid #c3c4c7;">
                <span class="crosstalk-cards-drag" title="<?php esc_attr_e('Drag to reorder', 'crosstalk'); ?>" style="cursor:move; color:#50575e;">⋮⋮</span>
                <button type="button" class="crosstalk-cards-toggle" aria-expanded="true" aria-label="<?php esc_attr_e('Collapse details', 'crosstalk'); ?>" title="<?php esc_attr_e('Collapse details', 'crosstalk'); ?>"><span class="crosstalk-cards-toggle-icon" aria-hidden="true">▼</span></button>
                <strong class="crosstalk-cards-card-title-preview" style="flex:1; margin:0;"><?php echo $title !== '' ? esc_html($title) : esc_html__('(No title)', 'crosstalk'); ?></strong>
                <button type="button" class="button crosstalk-cards-remove" style="color:#b32d2e; border-color:#b32d2e; background:transparent;" aria-label="<?php esc_attr_e('Delete this card', 'crosstalk'); ?>"><?php esc_html_e('Delete card', 'crosstalk'); ?></button>
            </div>
            <div class="crosstalk-cards-card-body" style="padding:12px;">
                <p>
                    <label><?php esc_html_e('Title', 'crosstalk'); ?></label>
                    <input class="widefat crosstalk-card-field-title" type="text" name="<?php echo esc_attr($name . '[title]'); ?>" value="<?php echo esc_attr($title); ?>">
                </p>
                <p>
                    <label><?php esc_html_e('Description', 'crosstalk'); ?></label>
                    <textarea class="widefat" rows="3" name="<?php echo esc_attr($name . '[description]'); ?>"><?php echo esc_textarea($description); ?></textarea>
                </p>
                <p>
                    <label><?php esc_html_e('Image (16:9)', 'crosstalk'); ?></label>
                    <span class="crosstalk-card-widget-image-wrap">
                        <input type="hidden" name="<?php echo esc_attr($name . '[image_id]'); ?>" value="<?php echo (int) $image_id; ?>">
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
                <input type="hidden" class="crosstalk-crop-x" name="<?php echo esc_attr($name . '[crop_x]'); ?>" value="<?php echo esc_attr($crop_x); ?>">
                <input type="hidden" class="crosstalk-crop-y" name="<?php echo esc_attr($name . '[crop_y]'); ?>" value="<?php echo esc_attr($crop_y); ?>">
                <p class="crosstalk-card-crop-panel" style="display:none;">
                    <label class="crosstalk-card-crop-panel-title"><?php esc_html_e('This image isn’t 16:9 — drag the box to choose which part to show', 'crosstalk'); ?></label>
                    <span class="crosstalk-card-crop-wrap">
                        <img class="crosstalk-card-crop-image" src="" alt="" style="max-width:100%;height:auto;display:block;">
                        <span class="crosstalk-card-crop-box" role="img" aria-label="<?php esc_attr_e('Crop selection', 'crosstalk'); ?>"></span>
                    </span>
                    <button type="button" class="button crosstalk-card-apply-crop" style="margin-top:8px;"><?php esc_html_e('Apply crop', 'crosstalk'); ?></button>
                </p>
                <p>
                    <label><?php esc_html_e('Link URL (optional)', 'crosstalk'); ?></label>
                    <input class="widefat" type="url" name="<?php echo esc_attr($name . '[link_url]'); ?>" value="<?php echo esc_attr($link_url); ?>" placeholder="https://">
                </p>
            </div>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $cards = [];
        if (isset($new_instance['cards']) && is_array($new_instance['cards'])) {
            foreach ($new_instance['cards'] as $card) {
                $cards[] = [
                    'title'       => isset($card['title']) ? sanitize_text_field($card['title']) : '',
                    'description' => isset($card['description']) ? wp_kses_post($card['description']) : '',
                    'image_id'    => isset($card['image_id']) ? absint($card['image_id']) : 0,
                    'crop_x'      => isset($card['crop_x']) && $card['crop_x'] !== '' ? sanitize_text_field($card['crop_x']) : '',
                    'crop_y'      => isset($card['crop_y']) && $card['crop_y'] !== '' ? sanitize_text_field($card['crop_y']) : '',
                    'link_url'    => isset($card['link_url']) ? esc_url_raw($card['link_url']) : '',
                ];
            }
        }
        return ['cards' => $cards];
    }
}
