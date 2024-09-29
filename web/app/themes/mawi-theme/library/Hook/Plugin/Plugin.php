<?php

declare(strict_types=1);

namespace MaWi\Hook\Plugin;

class Plugin
{
    public function __construct()
    {
        //add_filter('cachify_skip_cache', [$this, 'skipCache']);
        //add_filter('cachify_flush_cache_hooks', [$this, 'addFlushHook']);
    }

    /*
    public function skipCache()
    {
        // Remove showcase single since we have dynamic "more cases" there
        return is_singular('showcase');
    }

    public function addFlushHook($flush_cache_hooks)
    {
        $customHooks = [
            'save_post' => 10,
        ];

        return array_merge($flush_cache_hooks, $customHooks);
    }
    */
}
