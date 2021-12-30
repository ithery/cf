<?php

trait CTrait_Localizable {
    /**
     * Run the callback with the given locale.
     *
     * @param string   $locale
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function withLocale($locale, $callback) {
        if (!$locale) {
            return $callback();
        }

        $original = CF::getLocale();

        try {
            CF::setLocale($locale);

            return $callback();
        } finally {
            CF::setLocale($original);
        }
    }
}
