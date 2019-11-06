<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:44:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CModel_HasTranslation_HasTranslationTrait {

    public function getAttributeValue($key) {
        if (!$this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }
        return $this->getTranslation($key, $this->getLocale());
    }

    public function setAttribute($key, $value) {
        // Pass arrays and untranslatable attributes to the parent method.
        if (!$this->isTranslatableAttribute($key) || is_array($value)) {
            return parent::setAttribute($key, $value);
        }
        // If the attribute is translatable and not already translated, set a
        // translation for the current app locale.
        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    public function translate($key, $locale = '') {
        return $this->getTranslation($key, $locale);
    }

    public function getTranslation($key, $locale, $useFallbackLocale = true) {
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);
        $translations = $this->getTranslations($key);
        $translation = isset($translations[$locale]) ? $translations[$locale] : '';
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }
        return $translation;
    }

    public function getTranslationWithFallback($key, $locale) {
        return $this->getTranslation($key, $locale, true);
    }

    public function getTranslationWithoutFallback($key, $locale) {
        return $this->getTranslation($key, $locale, false);
    }

    public function getTranslations($key = null) {
        if ($key !== null) {
            $this->guardAgainstNonTranslatableAttribute($key);
            $attributes = $this->getAttributes();
            return array_filter(json_decode(isset($attributes[$key]) ? $attributes[$key] : '' ?: '{}', true) ?: [], function ($value) {
                return $value !== null && $value !== '';
            });
        }
        return array_reduce($this->getTranslatableAttributes(), function ($result, $item) {
            $result[$item] = $this->getTranslations($item);
            return $result;
        });
    }

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

    public function setTranslations($key, array $translations) {
        $this->guardAgainstNonTranslatableAttribute($key);
        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $locale, $translation);
        }
        return $this;
    }

    public function forgetTranslation($key, $locale) {
        $translations = $this->getTranslations($key);
        unset($translations[$locale]);
        $this->setAttribute($key, $translations);
        return $this;
    }

    public function forgetAllTranslations($locale) {
        CF::collect($this->getTranslatableAttributes())->each(function ( $attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });
        return $this;
    }

    public function getTranslatedLocales($key) {
        return array_keys($this->getTranslations($key));
    }

    public function isTranslatableAttribute($key) {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function hasTranslation($key, $locale = null) {
        $locale = $locale ?: $this->getLocale();
        return isset($this->getTranslations($key)[$locale]);
    }

    protected function guardAgainstNonTranslatableAttribute($key) {
        if (!$this->isTranslatableAttribute($key)) {
            throw AttributeIsNotTranslatable::make($key, $this);
        }
    }

    protected function normalizeLocale($key, $locale, $useFallbackLocale) {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }
        if (!$useFallbackLocale) {
            return $locale;
        }
        if (!is_null($fallbackLocale = config('app.fallback_locale'))) {
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
        return CF::collect($this->getTranslatableAttributes())
                        ->mapWithKeys(function ( $key) {
                            return [$key => $this->getTranslations($key)];
                        })
                        ->toArray();
    }

    public function getCasts() {
        return array_merge(
                parent::getCasts(), array_fill_keys($this->getTranslatableAttributes(), 'array')
        );
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
//    
//    public function getAttributeValue($key) {
//        if (!$this->isTranslatableAttribute($key)) {
//            return parent::getAttributeValue($key);
//        }
//        return $this->getTranslation($key, config('app.locale')) ?: array_first($this->getTranslations($key));
//    }

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray() {
        $values = array_map(function ($attribute) {
            return $this->getTranslation($attribute, config('app.locale')) ?: null;
        }, $keys = $this->getTranslatableAttributes());
        return array_replace(parent::attributesToArray(), array_combine($keys, $values));
    }

//    /**
//     * Get translations.
//     *
//     * @param $key
//     *
//     * @return array
//     */
//    public function getTranslations($key) {
//        $this->guardAgainstNonTranslatableAttribute($key);
//        $attributes = $this->getAttributes();
//        $value = json_decode(isset($attributes[$key]) ? $attributes[$key] : '' ?: '{}', true);
//        // Inject default translation if none supplied
//        if (!is_array($value)) {
//            $oldValue = $value;
//            if ($this->hasSetMutator($key)) {
//                $method = 'set' . studly_case($key) . 'Attribute';
//                $value = $this->{$method}($value);
//            }
//            $value = [$locale = app()->getLocale() => $value];
//            $this->attributes[$key] = $this->asJson($value);
//            event(new TranslationHasBeenSet($this, $key, $locale, $oldValue, $value));
//        }
//        return $value;
//    }
}
