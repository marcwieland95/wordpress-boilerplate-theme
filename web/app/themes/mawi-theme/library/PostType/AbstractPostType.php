<?php

declare(strict_types=1);

namespace MaWi\PostType;

use MaWi\Helper\QueryHelper;

abstract class AbstractPostType
{
    protected $id;

    /**
     * @var \WP_Post
     */
    protected $post = null;

    /**
     * @var array
     */
    protected $preloadedData = [];

    /**
     * AbstractPost constructor.
     * @param \WP_Post|int $post
     * @param array $preloadedData
     */
    public function __construct($post = null, $preloadedData = [])
    {
        if ($post instanceof \WP_Post) {
            $this->post = $post;
            $this->id = $post->ID;
        } elseif (is_int($post)) {
            $this->id = $post;
        } else {
            $post = (int) $post;
            if ($post > 0) {
                $this->id = $post;
            }
        }

        if (is_array($preloadedData)) {
            $this->preloadedData = $preloadedData;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSlug()
    {
        if (! isset($this->preloadedData['post_name'])) {
            $this->preloadedData['post_name'] = $this->getPost()->post_name;
        }

        return $this->preloadedData['post_name'];
    }

    public function getContent()
    {
        // Make sure to regenerate blocks based on the current post
        global $post;
        $post = $this->getPost();
        setup_postdata($post);
        $content = do_blocks($post->post_content);
        wp_reset_postdata();

        return $content;
    }

    public function getTitle()
    {
        if (! isset($this->preloadedData['post_title'])) {
            $this->preloadedData['post_title'] = $this->getPost()->post_title;
        }

        return str_replace('&shy;', '', $this->preloadedData['post_title']);
    }

    public function getField($selector, $trimmed = false)
    {
        if (! isset($this->preloadedData[$selector])) {
            $this->preloadedData[$selector] = get_field($selector, $this->getId());
        }

        $data = $this->preloadedData[$selector];

        if (is_serialized($data)) {
            $data = unserialize($data);
        }

        if ($trimmed) {
            $data = trim($data);
        }

        if (is_string($data)) {
            $data = str_replace('&shy;', '', $data);
        }

        return $data;
    }

    public function getPost()
    {
        if (! $this->post) {
            $this->post = QueryHelper::getPost($this->id);
        }

        return $this->post;
    }

    public function getPostType()
    {
        return get_post_type($this->getId());
    }

    public function getPermalink()
    {
        return get_permalink($this->getId());
    }

    public function getThumbnail()
    {
        return get_post_thumbnail_id($this->getId());
    }

    public function getDate($format)
    {
        return get_the_date($format, $this->getId());
    }

    /*
    public function getPublishedDate($format)
    {
        return get_the_date($format, $this->getId());
    }

    public function getModifiedDate($format)
    {
        return get_the_date($format, $this->getId());
    }

    public function reload()
    {
        $this->post = get_post($this->getId());
    }
    */

    /*
     * Magic Methods um alle Calls auf das eigentliche WP_Post Objekt ebenfalls anzubieten,
     * z.B. funktioniert $post->ID ebenfalls, wenn $post ein made_PostClass_Contribution-Objekt ist
     */
    /*
    public function __get($name)
    {
        return $this->getPost()->{$name};
    }

    public function __set($name, $value)
    {
        $this->getPost()->{$name} = $value;
    }

    public function __call($name, $arguments)
    {
        return call_user_func([$this->getPost(), $name], $arguments);
    }
    */
}
