<?php declare(strict_types=1);

namespace MaWi\Hook;

use MaWi\MaWi;

class Admin
{
    public function __construct()
    {
        add_filter('show_admin_bar', '__return_false');

        add_filter('auto_update_plugin', '__return_false');
        add_filter('auto_update_theme', '__return_false');

        add_filter('wp_check_filetype_and_ext', [$this, 'registerMimeTypes'], 10, 4);
        add_filter('upload_mimes', [$this, 'addMimeTypes']);
        //add_action('admin_head', [$this, 'fixSvgAdmin']);
        add_filter('admin_footer_text', [$this, 'changeAdminFooter']);
        add_filter('update_footer', [$this, 'addFooterThemeVersion'], 99);

        add_action('admin_bar_menu', [$this, 'removeAdminbarItem'], 99);

        add_action('admin_enqueue_scripts', [$this, 'addAdminStyle']);

        if (MaWi::getDeploymentEnvironment() !== 'DEVELOPMENT') {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    public function registerMimeTypes($types, $file, $filename, $mimes)
    {
        if (strpos($filename, '.svg') !== false) {
            $types['ext'] = 'svg';
            $types['type'] = 'image/svg+xml';
        }

        return $types;
    }

    public function addMimeTypes($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    /*
    public function fixSvgAdmin()
    {
        ?>
        <style type="text/css">
            td.media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail {
                width: 100% !important;
                height: auto !important;
            }
            .acf-image-uploader img {
                max-width: 200px;
            }
        </style>
        <?php
    }
    */

    public function addFooterThemeVersion($string)
    {
        $theme = wp_get_theme('mawi-theme');
        return sprintf('%s / Marc Wieland (%s)', $string, $theme->get('Version'));
    }

    public function changeAdminFooter()
    {
        return '<span id="footer-thankyou">Developed with &hearts; by <a href="https://marcwieland.name" target="_blank">Marc Wieland</a></span> in Bern.';
    }

    public function removeAdminbarItem($wp_admin_bar)
    {
        $wp_admin_bar->remove_menu('customize');
    }

    public function addAdminStyle()
    {
        wp_enqueue_style('admin-styles', '', [], ''); //  mix('/build/admin.css')
    }
}
