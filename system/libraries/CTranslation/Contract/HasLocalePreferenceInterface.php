<?php
interface CTranslation_Contract_HasLocalePreferenceInterface {
    /**
     * Get the preferred locale of the entity.
     *
     * @return null|string
     */
    public function preferredLocale();
}
