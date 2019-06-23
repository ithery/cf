<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:09:44 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTracker_RepositoryManager_LanguageTrait {

    /**
     *
     * @var CTracker_Repository_Language
     */
    protected $languageRepository;
    protected $languageDetect;

    protected function bootLanguageTrait() {
        $this->languageRepository = new CTracker_Repository_Language();
        $this->languageDetect = new CTracker_Detect_LanguageDetect();
    }

    public function findOrCreateLanguage($data) {
        return $this->languageRepository->findOrCreate($data, ['preference', 'language-range']);
    }

    public function getCurrentLanguage() {
        if ($languages = $this->getLanguage()) {
            $languages['preference'] = $this->languageDetect->getLanguagePreference();
            $languages['language-range'] = $this->languageDetect->getLanguageRange();
        }
        return $languages;
    }

    private function getLanguage() {
        try {
            return $this->languageDetect->detectLanguage();
        } catch (\Exception $e) {
            return;
        }
    }

}
