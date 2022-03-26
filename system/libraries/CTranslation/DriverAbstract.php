<?php

abstract class CTranslation_DriverAbstract {
    /**
     * Find all of the translations in the app without translation for a given language.
     *
     * @param string $language
     *
     * @return array
     */
    public function findMissingTranslations($language) {
        return c::arrayDiffAssocRecursive(
            $this->scanner->findTranslations(),
            $this->allTranslationsFor($language)
        );
    }

    /**
     * Save all of the translations in the app without translation for a given language.
     *
     * @param string $language
     *
     * @return void
     */
    public function saveMissingTranslations($language = false) {
        $languages = $language ? [$language => $language] : $this->allLanguages();

        foreach ($languages as $language => $name) {
            $missingTranslations = $this->findMissingTranslations($language);

            foreach ($missingTranslations as $type => $groups) {
                foreach ($groups as $group => $translations) {
                    foreach ($translations as $key => $value) {
                        if (cstr::contains($group, 'single')) {
                            $this->addSingleTranslation($language, $group, $key);
                        } else {
                            $this->addGroupTranslation($language, $group, $key);
                        }
                    }
                }
            }
        }
    }

    /**
     * Get all translations for a given language merged with the source language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function getSourceLanguageTranslationsWith($language) {
        $sourceTranslations = $this->allTranslationsFor($this->sourceLanguage);
        $languageTranslations = $this->allTranslationsFor($language);

        return $sourceTranslations->map(function ($groups, $type) use ($language, $languageTranslations) {
            return $groups->map(function ($translations, $group) use ($type, $language, $languageTranslations) {
                $translations = $translations->toArray();
                array_walk($translations, function (&$value, &$key) use ($type, $group, $language, $languageTranslations) {
                    $value = [
                        $this->sourceLanguage => $value,
                        $language => $languageTranslations->get($type, c::collect())->get($group, c::collect())->get($key),
                    ];
                });

                return $translations;
            });
        });
    }

    /**
     * Filter all keys and translations for a given language and string.
     *
     * @param string $language
     * @param string $filter
     *
     * @return Collection
     */
    public function filterTranslationsFor($language, $filter) {
        $allTranslations = $this->getSourceLanguageTranslationsWith(($language));
        if (!$filter) {
            return $allTranslations;
        }

        return $allTranslations->map(function ($groups, $type) use ($language, $filter) {
            return $groups->map(function ($keys, $group) use ($language, $filter, $type) {
                return c::collect($keys)->filter(function ($translations, $key) use ($group, $language, $filter, $type) {
                    return CTranslation_Helper::strsContain([$group, $key, $translations[$language], $translations[$this->sourceLanguage]], $filter);
                });
            })->filter(function ($keys) {
                return $keys->isNotEmpty();
            });
        });
    }

    /**
     * Get all translations for a particular language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function allTranslationsFor($language) {
        throw new Exception('This Driver does not implement this allTranslationsFor');
    }

    /**
     * Get all languages from the application.
     *
     * @return CCollection
     */
    public function allLanguages() {
        throw new Exception('This Driver does not implement this allLanguages');
    }

    /**
     * Add a new single type translation.
     *
     * @param string $language
     * @param string $key
     * @param string $value
     * @param mixed  $vendor
     *
     * @return void
     */
    public function addSingleTranslation($language, $vendor, $key, $value = '') {
        throw new Exception('This Driver does not implement this addSingleTranslation');
    }

    /**
     * Add a new group type translation.
     *
     * @param string $language
     * @param string $key
     * @param string $value
     * @param mixed  $group
     *
     * @return void
     */
    public function addGroupTranslation($language, $group, $key, $value = '') {
        throw new Exception('This Driver does not implement this addGroupTranslation');
    }

    /**
     * Get a collection of group names for a given language.
     *
     * @param string $language
     *
     * @return Collection
     */
    public function getGroupsFor($language) {
        throw new Exception('This Driver does not implement this getGroupsFor');
    }
}
