<?php

declare(strict_types=1);

namespace MaWi\Block;

abstract class AbstractBlockController
{
    protected $id;

    protected $contextObject;

    /**
     * @var array
     */
    protected $preloadedData = [];

    /**
     * AbstractPost constructor.
     * @param \MaWi\PostType\AbstractPostType $contextObject
     * @param array $preloadedData
     */
    public function __construct($contextObject = null, $preloadedData = [])
    {
        if ($contextObject !== null) {
            $this->contextObject = $contextObject;
        }

        if (is_array($preloadedData)) {
            $this->preloadedData = $preloadedData;
        }
    }

    public function getContext()
    {
        return $this->contextObject;
    }

    public function getField($selector, $trimmed = false)
    {
        if (! isset($this->preloadedData[$selector])) {
            $this->preloadedData[$selector] = get_field($selector, false, ! $trimmed);
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
}
