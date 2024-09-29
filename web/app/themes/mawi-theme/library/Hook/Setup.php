<?php

declare(strict_types=1);

namespace MaWi\Hook;

class Setup
{
    public function __construct()
    {
        add_action('init', [$this, 'setThemeSupport']);
        add_action('init', [$this, 'setThemeSupportGutenberg']);
        add_action('init', [$this, 'removeThumbnailSupport']);
        add_filter('big_image_size_threshold', '__return_false');
        add_action('init', [$this, 'setImageSizes']);
        add_filter('jpeg_quality', [$this, 'setImageCompression']);
        add_filter('intermediate_image_sizes', [$this, 'removeDefaultImagesSizes']);
        add_filter('image_size_names_choose', [$this, 'displayImageSizes']);
        add_action('init', [$this, 'registerNavigation']);

        remove_theme_support('core-block-patterns');
    }

    public function setThemeSupport()
    {
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('html5', [
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
        ]);
    }

    public function setThemeSupportGutenberg()
    {
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
        add_theme_support('disable-custom-colors');
        add_theme_support('editor-color-palette', []);

        /* TODO: add dark mode depending on custom fields */
        /*
        add_theme_support('editor-styles');
        add_theme_support('dark-editor-style');
        */
    }

    public function removeThumbnailSupport()
    {
        remove_post_type_support('page', 'thumbnail');
    }

    public function removeDefaultImagesSizes($default_image_sizes)
    {
        return array_diff($default_image_sizes, ['medium_large', 'large']);
    }

    public function displayImageSizes($sizes)
    {
        return [
            'S' => __('S', 'made-identity'),
            'M' => __('M', 'made-identity'),
            'L' => __('L', 'made-identity'),
            'XL' => __('XL', 'made-identity'),
        ];
    }

    public function setImageCompression()
    {
        return 100;
    }

    public function setImageSizes()
    {
        foreach ([
            'small' => 0.5,
            'default' => 1,
            'large' => 2,
        ] as $key => $size) {
            $add = '';
            if ($key !== 'default') {
                $add = '_' . $key;
            }

            add_image_size(
                'S' . $add,
                round(600 * $size),
                round(600 * $size),
                false
            );

            add_image_size(
                'M' . $add,
                round(900 * $size),
                round(900 * $size),
                false
            );

            add_image_size(
                'L' . $add,
                round(1400 * $size),
                round(1400 * $size),
                false
            );

            add_image_size(
                'XL' . $add,
                round(1800 * $size),
                round(1800 * $size),
                false
            );

            add_image_size(
                'square' . $add,
                round(520 * $size),
                round(520 * $size),
                true
            );
        }
    }

    public function registerNavigation()
    {
        register_nav_menus([
            'main_menu' => esc_html__('Hauptmenü', 'mawi-theme'),
        ]);
    }
}
