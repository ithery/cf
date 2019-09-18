<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:15:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Detect_LanguageDetect extends CTracker_Detect_MobileDetect {

    /**
     * Detect preference and language-range.
     *
     * @return array
     */
    public function detectLanguage() {
        return [
            'preference' => $this->getLanguagePreference(),
            'language_range' => $this->getLanguageRange(),
        ];
    }

    /**
     * Get language prefernece.
     *
     * @return string
     */
    public function getLanguagePreference() {
        $languages = $this->languages();
        return count($languages) ? $languages[0] : 'en';
    }

    /**
     * Get languages ranges.
     *
     * @return string
     */
    public function getLanguageRange() {
        return implode(',', $this->languages());
    }

}
