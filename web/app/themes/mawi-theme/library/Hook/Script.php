<?php

declare(strict_types=1);

namespace MaWi\Hook;

use MaWi\MaWi;

class Script
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'addScripts'], 1);
        add_filter('script_loader_tag', [$this, 'addAsyncAttribute'], 10, 2);
        add_filter('script_loader_tag', [$this, 'addDeferAttribute'], 10, 2);
        add_action('wp_head', [$this, 'addPreloadHeaders'], 0, 0);
    }

    public function addScripts()
    {
        // wp_localize_script('mawi-theme-scripts', 'mawiThemeData', $this->getJsData());

        $isViteDevServer = get_theme_file_path('/assets/hot');
        if (MaWi::getDeploymentEnvironment() === 'DEVELOPMENT' && file_exists($isViteDevServer)) {
            $viteDevServer = getenv('APP_URL') . ':5173';
            wp_enqueue_script_module('vite-client', $viteDevServer . '/@vite/client', [], null);
            wp_enqueue_script_module('mawi-theme-scripts', $viteDevServer . '/web/app/themes/mawi-theme/assets/scripts/main.js', ['vite-client'], null);
        } else {
            $manifestPath = get_theme_file_path('/assets/build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $mainJsEntry = 'web/app/themes/mawi-theme/assets/scripts/main.js';
                if (isset($manifest[$mainJsEntry])) {
                    $mainJs = $manifest[$mainJsEntry]['file'];
                    wp_register_script('mawi-theme-scripts', get_stylesheet_directory_uri() . '/assets/build/' . $mainJs, [], false, false);
                    wp_enqueue_script('mawi-theme-scripts');

                    if (isset($manifest[$mainJsEntry]['css'])) {
                        foreach ($manifest[$mainJsEntry]['css'] as $cssFile) {
                            wp_enqueue_style('mawi-theme-style', get_stylesheet_directory_uri() . '/assets/build/' . $cssFile, [], '');

                        }
                    }

                    wp_add_inline_script('mawi-theme-scripts', 'const mawiThemeData = ' . json_encode($this->getJsData()), 'before');
                }
            }
        }
    }

    public function addAsyncAttribute($tag, $handle)
    {
        $scripts_to_async = ['mawi-theme-style'];

        foreach ($scripts_to_async as $async_script) {
            if ($async_script === $handle) {
                return str_replace(' src', ' async src', $tag);
            }
        }
        return $tag;
    }

    public function addDeferAttribute($tag, $handle)
    {
        $scripts_to_async = ['mawi-theme-scripts'];

        foreach ($scripts_to_async as $async_script) {
            if ($async_script === $handle) {
                return str_replace(' src', ' defer src', $tag);
            }
        }
        return $tag;
    }

    /**
     * preloads CSS, TTF, ...
     */
    public function addPreloadHeaders()
    {
        //
        // CSS
        //
        // echo '<link rel="preload" href="' . mix('/build/styles.css') . '" as="style">' . PHP_EOL;

        //
        // JavaScript
        //
        // echo '<link rel="preload" href="' . mix('/build/scripts.js') . '" as="script">' . PHP_EOL;
    }

    private function getJsData()
    {
        return [
            'deploymentEnvironment' => MaWi::getDeploymentEnvironment(),
            'jsonEndpoint' => get_rest_url(),
        ];
    }
}
