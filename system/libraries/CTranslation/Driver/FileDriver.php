<?php

class CTranslation_Driver_FileDriver extends CTranslation_DriverAbstract implements CTranslation_DriverInterface {
    protected $sourceLanguage;

    protected $scanner;

    private $languageFilesPath;

    public function __construct($languageFilesPath, $sourceLanguage, $scanner) {
        $this->languageFilesPath = $languageFilesPath;
        $this->sourceLanguage = $sourceLanguage;
        $this->scanner = $scanner;
    }

    /**
     * Get all languages from the application.
     *
     * @return CCollection
     */
    public function allLanguages() {

        // As per the docs, there should be a subdirectory within the
        // languages path so we can return these directory names as a collection
        $directories = CCollection::make(CFile::directories($this->languageFilesPath));

        return $directories->mapWithKeys(function ($directory) {
            $language = basename($directory);

            return [$language => $language];
        })->filter(function ($language) {
            // at the moemnt, we're not supporting vendor specific translations
            return $language != 'vendor';
        });
    }

    /**
     * Get all group translations from the application.
     *
     * @param mixed $language
     *
     * @return array
     */
    public function allGroup($language) {
        $groupPath = "{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}";

        if (!CFile::exists($groupPath)) {
            return [];
        }

        $groups = CCollection::make(CFile::allFiles($groupPath));

        return $groups->map(function ($group) {
            return $group->getBasename('.php');
        });
    }

    /**
     * Get all the translations from the application.
     *
     * @return CCollection
     */
    public function allTranslations() {
        return $this->allLanguages()->mapWithKeys(function ($language) {
            return [$language => $this->allTranslationsFor($language)];
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
        return CCollection::make([
            'group' => $this->getGroupTranslationsFor($language),
            'single' => $this->getSingleTranslationsFor($language),
        ]);
    }

    /**
     * Add a new language to the application.
     *
     * @param string     $language
     * @param null|mixed $name
     *
     * @return void
     */
    public function addLanguage($language, $name = null) {
        if ($this->languageExists($language)) {
            throw new CTranslation_Exception_LanguageExistsException(c::__('translation::errors.language_exists', ['language' => $language]));
        }

        CFile::makeDirectory("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "${language}");
        if (!CFile::exists("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}.json")) {
            $this->saveSingleTranslations($language, c::collect(['single' => c::collect()]));
        }
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
        if (!$this->languageExists($language)) {
            $this->addLanguage($language);
        }

        $translations = $this->getGroupTranslationsFor($language);

        // does the group exist? If not, create it.
        if (!$translations->keys()->contains($group)) {
            $translations->put($group, c::collect());
        }

        $values = $translations->get($group);
        $values[$key] = $value;
        $translations->put($group, c::collect($values));

        $this->saveGroupTranslations($language, $group, $translations->get($group));
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
        if (!$this->languageExists($language)) {
            $this->addLanguage($language);
        }

        $translations = $this->getSingleTranslationsFor($language);
        $translations->get($vendor) ?: $translations->put($vendor, c::collect());
        $translations->get($vendor)->put($key, $value);

        $this->saveSingleTranslations($language, $translations);
    }

    /**
     * Get all of the single translations for a given language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function getSingleTranslationsFor($language) {
        $files = new CCollection(CFile::allFiles($this->languageFilesPath));

        return $files->filter(function ($file) use ($language) {
            return strpos($file, "{$language}.json");
        })->flatMap(function ($file) {
            if (strpos($file->getPathname(), 'vendor')) {
                $vendor = cstr::before(cstr::after($file->getPathname(), 'vendor' . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

                return ["{$vendor}::single" => new CCollection(json_decode(CFile::get($file), true))];
            }

            return ['single' => new CCollection(json_decode(CFile::get($file), true))];
        });
    }

    /**
     * Get all of the group translations for a given language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function getGroupTranslationsFor($language) {
        return $this->getGroupFilesFor($language)->mapWithKeys(function ($group) {
            // here we check if the path contains 'vendor' as these will be the
            // files which need namespacing
            if (cstr::contains($group->getPathname(), 'vendor')) {
                $vendor = cstr::before(cstr::after($group->getPathname(), 'vendor' . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

                return ["{$vendor}::{$group->getBasename('.php')}" => new CCollection(carr::dot(CFile::getRequire($group->getPathname())))];
            }

            return [$group->getBasename('.php') => new CCollection(carr::dot(CFile::getRequire($group->getPathname())))];
        });
    }

    /**
     * Get all the translations for a given file.
     *
     * @param string $language
     * @param string $file
     *
     * @return array
     */
    public function getTranslationsForFile($language, $file) {
        $file = cstr::finish($file, '.php');
        $filePath = "{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}" . DIRECTORY_SEPARATOR . "{$file}";
        $translations = [];

        if (CFile::exists($filePath)) {
            $translations = carr::dot(CFile::getRequire($filePath));
        }

        return $translations;
    }

    /**
     * Determine whether or not a language exists.
     *
     * @param string $language
     *
     * @return bool
     */
    public function languageExists($language) {
        return $this->allLanguages()->contains($language);
    }

    /**
     * Add a new group of translations.
     *
     * @param string $language
     * @param string $group
     *
     * @return void
     */
    public function addGroup($language, $group) {
        $this->saveGroupTranslations($language, $group, []);
    }

    /**
     * Save group type language translations.
     *
     * @param string $language
     * @param string $group
     * @param array  $translations
     *
     * @return void
     */
    public function saveGroupTranslations($language, $group, $translations) {
        // here we check if it's a namespaced translation which need saving to a
        // different path
        $translations = $translations instanceof CCollection ? $translations->toArray() : $translations;
        ksort($translations);
        $translations = CTranslation_Helper::arrayUndot($translations);
        if (cstr::contains($group, '::')) {
            return $this->saveNamespacedGroupTranslations($language, $group, $translations);
        }
        CFile::put("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}" . DIRECTORY_SEPARATOR . "{$group}.php", "<?php\n\nreturn " . var_export($translations, true) . ';' . \PHP_EOL);
    }

    /**
     * Save namespaced group type language translations.
     *
     * @param string $language
     * @param string $group
     * @param array  $translations
     *
     * @return void
     */
    private function saveNamespacedGroupTranslations($language, $group, $translations) {
        list($namespace, $group) = explode('::', $group);
        $directory = "{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . "{$namespace}" . DIRECTORY_SEPARATOR . "{$language}";

        if (!CFile::exists($directory)) {
            CFile::makeDirectory($directory, 0755, true);
        }

        CFile::put("${directory}" . DIRECTORY_SEPARATOR . "{$group}.php", "<?php\n\nreturn " . var_export($translations, true) . ';' . \PHP_EOL);
    }

    /**
     * Save single type language translations.
     *
     * @param string      $language
     * @param CCollection $translations
     *
     * @return void
     */
    private function saveSingleTranslations($language, $translations) {
        foreach ($translations as $group => $translation) {
            $vendor = cstr::before($group, '::single');
            $languageFilePath = $vendor !== 'single' ? 'vendor' . DIRECTORY_SEPARATOR . "{$vendor}" . DIRECTORY_SEPARATOR . "{$language}.json" : "{$language}.json";
            CFile::put(
                "{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$languageFilePath}",
                json_encode((object) $translations->get($group), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );
        }
    }

    /**
     * Get all the group files for a given language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function getGroupFilesFor($language) {
        $groups = new CCollection(CFile::allFiles("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . "{$language}"));
        // namespaced files reside in the vendor directory so we'll grab these
        // the `getVendorGroupFileFor` method
        $groups = $groups->merge($this->getVendorGroupFilesFor($language));

        return $groups;
    }

    /**
     * Get a collection of group names for a given language.
     *
     * @param string $language
     *
     * @return Collection
     */
    public function getGroupsFor($language) {
        return $this->getGroupFilesFor($language)->map(function ($file) {
            if (cstr::contains($file->getPathname(), 'vendor')) {
                $vendor = cstr::before(cstr::after($file->getPathname(), 'vendor' . DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);

                return "{$vendor}::{$file->getBasename('.php')}";
            }

            return $file->getBasename('.php');
        });
    }

    /**
     * Get all the vendor group files for a given language.
     *
     * @param string $language
     *
     * @return CCollection
     */
    public function getVendorGroupFilesFor($language) {
        if (!CFile::exists("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor')) {
            return;
        }

        $vendorGroups = [];
        foreach (CFile::directories("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor') as $vendor) {
            $vendor = carr::last(explode(DIRECTORY_SEPARATOR, $vendor));
            if (!CFile::exists("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . "{$vendor}" . DIRECTORY_SEPARATOR . "{$language}")) {
                array_push($vendorGroups, []);
            } else {
                array_push($vendorGroups, CFile::allFiles("{$this->languageFilesPath}" . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . "{$vendor}" . DIRECTORY_SEPARATOR . "{$language}"));
            }
        }

        return new CCollection(carr::flatten($vendorGroups));
    }
}
