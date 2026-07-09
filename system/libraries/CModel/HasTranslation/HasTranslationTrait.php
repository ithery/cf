<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CModel_HasTranslation_HasTranslationTrait {
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key) {
        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->getTranslation($key, $this->getLocale());
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return CModel
     */
    public function setAttribute($key, $value) {
        // Pass arrays and untranslatable attributes to the parent method.
        if (!$this->isTranslatableAttribute($key) || is_array($value)) {
            return parent::setAttribute($key, $value);
        }
        // If the attribute is translatable and not already translated, set a
        // translation for the current app locale.
        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    /**
     * Get the translation for a given attribute and locale.
     *
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function translate($key, $locale = '') {
        return $this->getTranslation($key, $locale);
    }

    /**
     * Get the translation for a given attribute and locale.
     *
     * @param string $key
     * @param string $locale
     * @param bool   $useFallbackLocale
     *
     * @return mixed
     */
    public function getTranslation($key, $locale, $useFallbackLocale = true) {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);
        $translations = $this->getTranslations($key);
        $translation = isset($translations[$locale]) ? $translations[$locale] : '';
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    /**
     * Get the translation for a given attribute and locale, falling back to the default locale if no translation exists.
     *
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function getTranslationWithFallback($key, $locale) {
        return $this->getTranslation($key, $locale, true);
    }

    /**
     * Get the translation for a given attribute and locale, without falling back to the default locale if no translation exists.
     *
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function getTranslationWithoutFallback($key, $locale) {
        return $this->getTranslation($key, $locale, false);
    }

    public function getTranslations($key = null) {
        if ($key !== null) {
            $this->guardAgainstNonTranslatableAttribute($key);
            $attributes = $this->getAttributes();

            return array_filter(json_decode((isset($attributes[$key]) ? $attributes[$key] : '') ?: '{}', true) ?: [], function ($value) {
                return $value !== null && $value !== '';
            });
        }

        return array_reduce($this->getTranslatableAttributes(), function ($result, $item) {
            $result[$item] = $this->getTranslations($item);

            return $result;
        });
    }

    /**
     * Set the translation for a given attribute and locale.
     *
     * @param string $key
     * @param string $locale
     * @param mixed  $value
     *
     * @return CModel
     */
    public function setTranslation($key, $locale, $value) {
        $this->guardAgainstNonTranslatableAttribute($key);
        $translations = $this->getTranslations($key);
        $oldValue = isset($translations[$locale]) ? $translations[$locale] : '';
        if ($this->hasSetMutator($key)) {
            $method = 'set' . cstr::studly($key) . 'Attribute';
            $this->{$method}($value, $locale);
            $value = $this->attributes[$key];
        }
        $translations[$locale] = $value;
        $this->attributes[$key] = $this->asJson($translations);
        CEvent::dispatch(new CModel_HasTranslation_Event_TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));

        return $this;
    }

    /**
     * Set the translations for a given attribute.
     *
     * @param string $key
     * @param array  $translations
     *
     * @return CModel
     */
    public function setTranslations($key, array $translations) {
        $this->guardAgainstNonTranslatableAttribute($key);
        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $locale, $translation);
        }

        return $this;
    }

    /**
     * Forget the translation for a given attribute and locale.
     *
     * @param string $key
     * @param string $locale
     *
     * @return CModel
     */
    public function forgetTranslation($key, $locale) {
        $translations = $this->getTranslations($key);
        unset($translations[$locale]);
        $this->setAttribute($key, $translations);

        return $this;
    }

    /**
     * Forget all translations for a given locale.
     *
     * @param string $locale
     *
     * @return CModel
     */
    public function forgetAllTranslations($locale) {
        c::collect($this->getTranslatableAttributes())->each(function ($attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });

        return $this;
    }

    /**
     * Get the locales that have been translated for a given attribute.
     *
     * @param string $key
     *
     * @return array
     */
    public function getTranslatedLocales($key) {
        return array_keys($this->getTranslations($key));
    }

    /**
     * Determine if the given attribute is translatable.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isTranslatableAttribute($key) {
        return in_array($key, $this->getTranslatableAttributes());
    }

    /**
     * Determine if the given attribute has a translation for the given locale.
     *
     * @param string $key
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation($key, $locale = null) {
        $locale = $locale ?: $this->getLocale();

        return isset($this->getTranslations($key)[$locale]);
    }

    /**
     * Guard against setting or getting a translation for a non-translatable attribute.
     *
     * @param string $key
     *
     * @throws CModel_HasTranslation_Exception_AttributeIsNotTranslatable
     */
    protected function guardAgainstNonTranslatableAttribute($key) {
        if (!$this->isTranslatableAttribute($key)) {
            throw CModel_HasTranslation_Exception_AttributeIsNotTranslatable::make($key, $this);
        }
    }

    /**
     * Normalize the locale to use for a given attribute and locale, falling back to the default locale if no translation exists.
     *
     * @param string $key
     * @param string $locale
     * @param bool   $useFallbackLocale
     *
     * @return string
     */
    protected function normalizeLocale($key, $locale, $useFallbackLocale) {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }
        if (!$useFallbackLocale) {
            return $locale;
        }
        if (!is_null($fallbackLocale = CF::config('app.fallback_locale'))) {
            return $fallbackLocale;
        }

        return $locale;
    }

    protected function getLocale() {
        return CF::config('app.locale');
    }

    public function getTranslatableAttributes() {
        return is_array($this->translatable) ? $this->translatable : [];
    }

    public function getTranslationsAttribute() {
        return c::collect($this->getTranslatableAttributes())
            ->mapWithKeys(function ($key) {
                return [$key => $this->getTranslations($key)];
            })->toArray();
    }

    public function getCasts() {
        return array_merge(
            parent::getCasts(),
            array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray() {
        $values = array_map(function ($attribute) {
            return $this->getTranslation($attribute, CF::config('app.locale')) ?: null;
        }, $keys = $this->getTranslatableAttributes());

        return array_replace(parent::attributesToArray(), array_combine($keys, $values));
    }

    // /**
    //  * Get translations.
    //  *
    //  * @param $key
    //  *
    //  * @return array
    //  */
    // public function getTranslations($key) {
    //     $this->guardAgainstNonTranslatableAttribute($key);
    //     $attributes = $this->getAttributes();
    //     $value = json_decode(isset($attributes[$key]) ? $attributes[$key] : '' ?: '{}', true);
    //     // Inject default translation if none supplied
    //     if (!is_array($value)) {
    //         $oldValue = $value;
    //         if ($this->hasSetMutator($key)) {
    //             $method = 'set' . studly_case($key) . 'Attribute';
    //             $value = $this->{$method}($value);
    //         }
    //         $value = [$locale = app()->getLocale() => $value];
    //         $this->attributes[$key] = $this->asJson($value);
    //         event(new TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));
    //     }
    //     return $value;
    // }
}
