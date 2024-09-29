<?php

declare(strict_types=1);

namespace MaWi\Helper;

use PHP_Typography\DOM;
use PHP_Typography\PHP_Typography;
use PHP_Typography\Settings;

class PhpTypography extends PHP_Typography
{
    /**
     * Applies specific fixes to all textnodes of the HTML fragment.
     *
     * @since 6.0.0 Parameter $body_classes added.
     *
     * @param string   $html         A HTML fragment.
     * @param callable $fixer        A callback that applies typography fixes to a single textnode.
     * @param Settings $settings     A settings object.
     * @param bool     $is_title     Optional. If the HTML fragment is a title. Default false.
     * @param string[] $body_classes Optional. CSS classes added to the virtual
     *                               <body> element used for processing. Default [].
     *
     * @return string The processed $html.
     */
    public function process_textnodes($html, callable $fixer, Settings $settings, $is_title = false, array $body_classes = [])
    {
        if (isset($settings['ignoreTags']) && $is_title && (\in_array('h1', /** Array. @scrutinizer ignore-type */ $settings['ignoreTags'], true) || \in_array('h2', /** Array. @scrutinizer ignore-type */ $settings['ignoreTags'], true))) {
            return $html;
        }

        // Lazy-load our parser (the text parser is not needed for feeds).
        $html5_parser = $this->get_html5_parser();

        // Parse the HTML.
        $dom = $this->parse_html($html5_parser, $html, $settings, $body_classes);

        // Abort if there were parsing errors.
        if (! $dom instanceof \DOMDocument || ! $dom->hasChildNodes()) {
            return $html;
        }

        // Query some nodes in the DOM.
        $xpath = new \DOMXPath($dom);
        $body_node = $xpath->query('/html/body')->item(0);

        // Abort if we could not retrieve the body node.
        // This should be refactored to use exceptions in a future version.
        if (! $body_node instanceof \DOMNode) {
            return $html;
        }

        // Get the list of tags that should be ignored.
        $tags_to_ignore = $this->query_tags_to_ignore($xpath, $body_node, $settings);

        // Start processing.
        foreach ($xpath->query('//text()', $body_node) as $textnode) {
            if (
                // One of the ancestors should be ignored.
                self::arrays_intersect(DOM::get_ancestors($textnode), $tags_to_ignore) ||
                // The node contains only whitespace.
                $textnode->isWhitespaceInElementContent()
            ) {
                continue;
            }

            // Store original content.
            $original = $textnode->data;

            // Apply fixes.
            $fixer($textnode, $settings, $is_title);

            // Until now, we've only been working on a textnode: HTMLify result.
            $new = $textnode->data;

            // If old text was only a space for a newline (see made-identity/made-identity#180) {
            if ($textnode->isWhitespaceInElementContent()) {
                $new = $original;
                $this->replace_node_with_html($textnode, $settings->apply_character_mapping($new));
            }

            // Replace original node (if anthing was changed).
            if ($new !== $original) {
                $this->replace_node_with_html($textnode, $settings->apply_character_mapping($new));
            }
        }

        return $html5_parser->saveHTML($body_node->childNodes);
    }
}
