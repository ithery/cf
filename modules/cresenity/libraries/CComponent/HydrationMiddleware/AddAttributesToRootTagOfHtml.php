<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_HydrationMiddleware_AddAttributesToRootTagOfHtml {
    public function __invoke($dom, $data) {
        $attributesFormattedForHtmlElement = c::collect($data)
            ->mapWithKeys(function ($value, $key) {
                return ["cres:{$key}" => $this->escapeStringForHtml($value)];
            })->map(function ($value, $key) {
                return sprintf('%s="%s"', $key, $value);
            })->implode(' ');

        preg_match('/<([a-zA-Z0-9\-]*)/', $dom, $matches, PREG_OFFSET_CAPTURE);

        c::throwUnless(
            count($matches),
            new CComponent_Exception_RootTagMissingFromViewException
        );

        $tagName = $matches[1][0];
        $lengthOfTagName = strlen($tagName);
        $positionOfFirstCharacterInTagName = $matches[1][1];

        return substr_replace(
            $dom,
            ' ' . $attributesFormattedForHtmlElement,
            $positionOfFirstCharacterInTagName + $lengthOfTagName,
            0
        );
    }

    protected function escapeStringForHtml($subject) {
        if (is_string($subject) || is_numeric($subject)) {
            return htmlspecialchars($subject);
        }

        return htmlspecialchars(json_encode($subject));
    }
}
