<?php

declare(strict_types=1);

namespace MaWi\Helper;

class ImageHelper
{
    public static function printImage($data, $size = null, $attributes = [], $lazy = true, $responsive = true)
    {
        echo static::getImage($data, $size, $attributes, $lazy, $responsive);
    }

    public static function printImageBackground($imageUrl, $attributes = [], $lazy = true, $responsive = true)
    {
        echo static::getImageBackground($imageUrl, $attributes, $lazy, $responsive);
    }

    public static function getImageUrl($data, $size, $dimension = '')
    {
        return $data['sizes'][sprintf('%s%s', $size, ($dimension ? '_' . $dimension : ''))];
    }

    public static function getImageHeight($data, $size, $dimension = '')
    {
        return $data['sizes'][sprintf('%s%s-height', $size, ($dimension ? '_' . $dimension : ''))];
    }

    public static function getImageBackground($imageUrl, $attributes = [], $lazy = null, $responsive = true)
    {
        // Native lazyloading
        $attributes['loading'] = static::getLazyloadingAttribute($lazy);

        $attributes['data-bg'] = 'url(' . $imageUrl . ')';

        if ($attributes['loading'] === 'lazy') {
            $attributes = static::setLazyloadingClass($attributes);
        } else {
            $attributes['style'] = 'background-image:' . $attributes['data-bg'];
        }

        return '<div ' . self::composeAttributes($attributes) . '></div>';
    }

    public static function getImage($data, $size, $attributes = [], $lazy = true, $responsive = true)
    {
        // create acf object from attachment id
        if (is_numeric($data) && function_exists('acf_get_attachment')) {
            $data = acf_get_attachment($data);
        }

        if (is_string($data)) {
            $image = [
                'src' => $data,
            ];
            $responsive = false;
        } elseif (is_array($data)) {
            $image = [
                'src' => $data['url'],
            ];
            if (preg_match('/\.svg$/', $data['filename'])) {
                $responsive = false;
            } else {
                $image = static::getImageSrcSet($data, $size);
                if (! $image) {
                    $responsive = false;
                    $image = [
                        'src' => $data['url'],
                    ];
                }
            }
        } else {
            /*
            if ($size === 'square') {
                $image = [
                    'src' => 'https://dummyimage.com/320x320/eee/aaa',
                ];
            } else {
                $image = [
                    'src' => 'https://dummyimage.com/640x420/eee/aaa',
                ];
            }
            $responsive = false;
            */
            return null;
        }

        // Alt attribute
        if (! isset($attributes['alt']) && isset($data['alt'])) {
            $attributes['alt'] = $data['alt'];
        }

        // Native lazyloading
        $attributes['loading'] = static::getLazyloadingAttribute($lazy);

        // Responsive images
        if ($responsive) {
            $attributes['srcset'] = $image['srcset'];

            if (! isset($attributes['sizes'])) {
                $attributes['sizes'] = '100w';
            }
        }

        $attributes['src'] = $image['src'];

        $attributes['height'] = $data['height'];
        $attributes['width'] = $data['width'];

        if (! isset($attributes['src'])) {
            return '';
        }

        return '<img ' . static::composeAttributes($attributes) . '>';
    }

    public static function getImageAlt($imageObject)
    {
        return $imageObject['alt'];
    }

    public static function getImageCaption($imageObject)
    {
        return $imageObject['caption'];
    }

    public static function getRatio($imageObject)
    {
        if (! $imageObject) {
            return false;
        }

        if ($imageObject['width'] && $imageObject['height']) {
            return round(
                $imageObject['height'] / $imageObject['width'] * 100,
                0
            ) . '%';
        }

        return 56;
    }

    public static function printSvg($filename, $attributes = [])
    {
        echo static::getSvg($filename, $attributes);
    }

    public static function getSvg($filename, $attributes = [])
    {
        $html = '';

        if (! isset($attributes['alt'])) {
            $attributes['alt'] = '';
        }

        foreach ($attributes as $attribute => $value) {
            $html .= sprintf('%s="%s" ', $attribute, $value);
        }

        $filename = self::getSvgUrl($filename);

        return '<img ' . $html . 'src="' . $filename . '">';
    }

    public static function getSvgUrl($filename)
    {
        return get_template_directory_uri() . '/build/resources/svg/' . $filename . '.svg';
    }

    public static function getCopyright($attachmentID)
    {
        return get_field('image-copyright', $attachmentID);
    }

    private static function setLazyloadingClass($attributes)
    {
        // Only run lazyloading when its activated
        if (isset($attributes['class'])) {
            $attributes['class'] = trim($attributes['class']) . ' lazyload';
        } else {
            $attributes['class'] = 'lazyload';
        }

        return $attributes;
    }

    private static function getLazyloadingAttribute($lazy)
    {
        if ($lazy === false) {
            $lazy = 'eager';
        } elseif ($lazy === true) {
            $lazy = 'lazy';
        } else {
            $lazy = 'auto';
        }

        return $lazy;
    }

    private static function composeAttributes($attributes)
    {
        $html = '';

        foreach ($attributes as $attribute => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $html .= sprintf("%s='%s' ", $attribute, $value);
        }

        return trim($html);
    }

    private static function getImageSrcSet($data, $size)
    {
        if (! in_array($size, get_intermediate_image_sizes(), true)) {
            return false;
        }

        $srcset = '';
        foreach (['small', 'default', 'large'] as $sizeKey) {
            $sizeEl = sprintf('%s_%s', $size, $sizeKey);
            if ($sizeKey === 'default') {
                $sizeEl = $size;
            }

            if (! isset($data['sizes'][$sizeEl])) {
                continue;
            }

            $srcset .= sprintf('%s %sw, ', $data['sizes'][$sizeEl], $data['sizes'][sprintf('%s-width', $sizeEl)]);
        }

        if (! array_key_exists('sizes', $data)) {
            return false;
        }

        return [
            'srcset' => rtrim($srcset, ' ,'),
            'src' => $data['sizes'][sprintf('%s', $size)],
        ];
    }

    private static function getImagePlaceholder($data)
    {
        if (is_string($data) || preg_match('/\.svg$/', $data['filename'])) {
            return get_template_directory_uri() . '/build/resources/images/pixel.png';
        }

        // Use default image size which keeps ratio
        return $data['sizes']['medium'];
    }
}
