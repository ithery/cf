<?php

trait CConsole_Prompt_Concerns_Colors {
    /**
     * Reset all colors and styles.
     *
     * @param string $text
     *
     * @return string
     */
    public function reset($text) {
        return "\e[0m{$text}\e[0m";
    }

    /**
     * Make the text bold.
     *
     * @param string $text
     *
     * @return string
     */
    public function bold($text) {
        return "\e[1m{$text}\e[22m";
    }

    /**
     * Make the text dim.
     *
     * @param string $text
     *
     * @return string
     */
    public function dim($text) {
        return "\e[2m{$text}\e[22m";
    }

    /**
     * Make the text italic.
     *
     * @param string $text
     *
     * @return string
     */
    public function italic($text) {
        return "\e[3m{$text}\e[23m";
    }

    /**
     * Underline the text.
     *
     * @param string $text
     *
     * @return string
     */
    public function underline($text) {
        return "\e[4m{$text}\e[24m";
    }

    /**
     * Invert the text and background colors.
     *
     * @param string $text
     *
     * @return string
     */
    public function inverse($text) {
        return "\e[7m{$text}\e[27m";
    }

    /**
     * Hide the text.
     *
     * @param string $text
     *
     * @return string
     */
    public function hidden($text) {
        return "\e[8m{$text}\e[28m";
    }

    /**
     * Strike through the text.
     *
     * @param string $text
     *
     * @return string
     */
    public function strikethrough($text) {
        return "\e[9m{$text}\e[29m";
    }

    /**
     * Set the text color to black.
     *
     * @param string $text
     *
     * @return string
     */
    public function black($text) {
        return "\e[30m{$text}\e[39m";
    }

    /**
     * Set the text color to red.
     *
     * @param string $text
     *
     * @return string
     */
    public function red($text) {
        return "\e[31m{$text}\e[39m";
    }

    /**
     * Set the text color to green.
     *
     * @param string $text
     *
     * @return string
     */
    public function green($text) {
        return "\e[32m{$text}\e[39m";
    }

    /**
     * Set the text color to yellow.
     *
     * @param string $text
     *
     * @return string
     */
    public function yellow($text) {
        return "\e[33m{$text}\e[39m";
    }

    /**
     * Set the text color to blue.
     *
     * @param string $text
     *
     * @return string
     */
    public function blue($text) {
        return "\e[34m{$text}\e[39m";
    }

    /**
     * Set the text color to magenta.
     *
     * @param string $text
     *
     * @return string
     */
    public function magenta($text) {
        return "\e[35m{$text}\e[39m";
    }

    /**
     * Set the text color to cyan.
     *
     * @param string $text
     *
     * @return string
     */
    public function cyan($text) {
        return "\e[36m{$text}\e[39m";
    }

    /**
     * Set the text color to white.
     *
     * @param string $text
     *
     * @return string
     */
    public function white($text) {
        return "\e[37m{$text}\e[39m";
    }

    /**
     * Set the text background to black.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgBlack($text) {
        return "\e[40m{$text}\e[49m";
    }

    /**
     * Set the text background to red.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgRed($text) {
        return "\e[41m{$text}\e[49m";
    }

    /**
     * Set the text background to green.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgGreen($text) {
        return "\e[42m{$text}\e[49m";
    }

    /**
     * Set the text background to yellow.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgYellow($text) {
        return "\e[43m{$text}\e[49m";
    }

    /**
     * Set the text background to blue.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgBlue($text) {
        return "\e[44m{$text}\e[49m";
    }

    /**
     * Set the text background to magenta.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgMagenta($text) {
        return "\e[45m{$text}\e[49m";
    }

    /**
     * Set the text background to cyan.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgCyan($text) {
        return "\e[46m{$text}\e[49m";
    }

    /**
     * Set the text background to white.
     *
     * @param string $text
     *
     * @return string
     */
    public function bgWhite($text) {
        return "\e[47m{$text}\e[49m";
    }

    /**
     * Set the text color to gray.
     *
     * @param string $text
     *
     * @return string
     */
    public function gray($text) {
        return "\e[90m{$text}\e[39m";
    }
}
