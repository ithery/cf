<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 16, 2019, 9:56:58 PM
 */
class CEmoji {
    /**
     * @param int $iconSize
     *
     * @return \CEmoji_Adapter_TwemojiAdapter
     */
    public static function twemoji($iconSize = 16) {
        return new CEmoji_Adapter_TwemojiAdapter($iconSize);
    }
}
