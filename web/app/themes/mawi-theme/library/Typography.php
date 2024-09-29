<?php

declare(strict_types=1);

namespace MaWi;

use MADE\Helper\PhpTypography;
use PHP_Typography\Settings;

class Typography
{
    protected static $settings = null;

    public static function getTypography()
    {
        if (static::$settings === null) {
            static::$settings = new Settings();
            static::$settings->set_hyphenation(true);
            static::$settings->set_hyphenate_all_caps(true);
            static::$settings->set_min_length_hyphenation(10);
            static::$settings->set_min_before_hyphenation(6);
            static::$settings->set_min_after_hyphenation();
            static::$settings->set_hyphenation_language('de');
            static::$settings->set_smart_quotes(false);
            static::$settings->set_smart_diacritics(false);
            static::$settings->set_style_ampersands(false);
            static::$settings->set_style_caps(false);
            static::$settings->set_style_initial_quotes(false);
            static::$settings->set_style_numbers(false);
            static::$settings->set_smart_fractions(false);
        }

        return static::$settings;
    }

    public static function processTypography($text)
    {
        $typo = new PhpTypography();
        return $typo->process($text, @static::getTypography());
    }
}
