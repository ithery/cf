<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 10:19:54 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CEmoji_IndexInterface {

    /**
     * @param string $unicode
     *
     * @return array
     */
    public function findByUnicode($unicode);

    /**
     * @param string $name
     * @param array
     */
    public function findByName($name);

    /**
     * @return string
     */
    public function getEmojiUnicodeRegex();

    /**
     * @return string
     */
    public function getEmojiNameRegex();

    /**
     * @return array
     */
    public function getEmojis();
}
