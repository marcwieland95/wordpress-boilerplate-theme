<?php

declare(strict_types=1);

/*
 * Constant
 */

use MaWi\MaWi;

define('THEME_DIR', __DIR__);

/*
 * Autoloader
 */
spl_autoload_register(function ($class) {
    if (strpos($class, 'MaWi\\') === 0) {
        $path = preg_replace('/^MaWi/', __DIR__ . DIRECTORY_SEPARATOR . 'library', $class, 1);
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path) . '.php';
        if (file_exists($path)) {
            require_once($path);
        }
    }
});

/*
 * Theme init
 */
add_action('after_setup_theme', function () {
    MaWi::getInstance();
});
