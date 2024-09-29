<?php

declare(strict_types=1);

namespace MaWi\Helper;

use MaWi\PostType;

class QueryHelper
{
    /**
     * @param null $post
     */
    public static function getPost($post = null): ?object
    {
        if ($post === null) {
            $post = get_post();
        }

        if (is_numeric($post)) {
            $post = get_post($post);
        }

        if ($post instanceof \WP_Post) {
            if ($post->post_type) {
                $class = self::getPostClassFromString($post->post_type);
                if (class_exists($class, false)) {
                    return new $class($post);
                }
            }
            return new PostType\Page($post);
        }
        return null;
    }

    public static function get404Page()
    {
        return self::getPost(1);
    }

    private static function getPostClassFromString($str)
    {
        $strings = explode('-', $str);
        $strings = array_map('ucfirst', $strings);
        $str = implode($strings);
        return 'MADE\\PostType\\' . $str;
    }
}
