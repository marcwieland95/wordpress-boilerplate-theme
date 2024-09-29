<?php

declare(strict_types=1);

namespace MaWi\Hook;

class Cache
{
    public function __construct()
    {
        add_action('save_post', [$this, 'purgeCachedData'], 999, 3);
    }

    public function purgeCachedData($postId, $post, $update)
    {

        //\Cachify::flush_total_cache();

        if (! $update) {
            return;
        }

        /*
        $postObject = QueryHelper::getPost($postId);

        if (!$postObject) {
            return;
        }

        if ($postObject instanceof CacheableInterface) {
            $postObject->flushCache();
        }
        */
    }
}
