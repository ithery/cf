<?php

class CConsole_Prompt_Elements_Element {
    /**
     * @param string $text
     *
     * @return CConsole_Prompt_Elements_Heading
     */
    public static function heading($text) {
        return new CConsole_Prompt_Elements_Heading($text);
    }

    /**
     * @param array<int, string> $items
     * @param bool                $spaced
     *
     * @return CConsole_Prompt_Elements_BulletedList
     */
    public static function bulletedList(array $items, $spaced = false) {
        return new CConsole_Prompt_Elements_BulletedList($items, $spaced);
    }

    /**
     * @param array<int, string> $items
     * @param bool                $spaced
     *
     * @return CConsole_Prompt_Elements_NumberedList
     */
    public static function numberedList(array $items, $spaced = false) {
        return new CConsole_Prompt_Elements_NumberedList($items, $spaced);
    }

    /**
     * @param array<string, string> $items
     *
     * @return CConsole_Prompt_Elements_KeyValueList
     */
    public static function keyValueList(array $items) {
        return new CConsole_Prompt_Elements_KeyValueList($items);
    }

    /**
     * @param string      $url
     * @param null|string $label
     * @param bool        $underline
     *
     * @return CConsole_Prompt_Elements_Link
     */
    public static function link($url, $label = null, $underline = true) {
        return new CConsole_Prompt_Elements_Link($url, $label, $underline);
    }
}
