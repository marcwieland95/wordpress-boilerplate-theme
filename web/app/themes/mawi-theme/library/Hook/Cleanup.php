<?php

declare(strict_types=1);

namespace MaWi\Hook;

class Cleanup
{
    public function __construct()
    {
        add_action('init', [$this, 'cleanupHeader']);
        add_action('wp_enqueue_scripts', [$this, 'removePluginFiles'], 999);
        //add_action('wp_enqueue_scripts', [$this, 'dequeueWpStyles'], 100);
        add_filter('wp_footer', [$this, 'deregisterEmbedScript'], 9);
        add_action('admin_menu', [$this, 'removeWidgets']);
        add_filter('style_loader_src', [$this, 'removeVersionQueryString'], 99);
        add_filter('script_loader_src', [$this, 'removeVersionQueryString'], 99);
        add_filter('the_generator', [$this, 'removeGenerator'], 99);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
    }

    public function cleanupHeader()
    {
        remove_action('wp_head', 'wp_resource_hints', 2);
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('template_redirect', 'rest_output_link_header', 11);
    }

    public function removePluginFiles()
    {
        wp_dequeue_style('bodhi-svgs-attachment');
    }

    public function deregisterEmbedScript()
    {
        wp_deregister_script('wp-embed');
    }

    public function dequeueWpStyles()
    {
        wp_dequeue_style('wp-block-library');
    }

    public function removeWidgets()
    {
        // WordPress Core Widgets
        remove_meta_box('dashboard_right_now', 'dashboard', 'core'); // Right Now Widget
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'core'); // Comments Widget
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'core'); // Incoming Links Widget
        remove_meta_box('dashboard_plugins', 'dashboard', 'core'); // Plugins Widget

        remove_meta_box('dashboard_quick_press', 'dashboard', 'core'); // Quick Press Widget
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'core'); // Recent Drafts Widget
        remove_meta_box('dashboard_primary', 'dashboard', 'core');
        remove_meta_box('dashboard_secondary', 'dashboard', 'core');

        // WordPress Welcome Screen
        remove_action('welcome_panel', 'wp_welcome_panel');
    }

    public function removeVersionQueryString($src)
    {
        if ($src && strpos($src, 'ver=')) {
            $src = remove_query_arg('ver', $src);
        }

        return $src;
    }

    public function removeGenerator()
    {
        return '';
    }
}
