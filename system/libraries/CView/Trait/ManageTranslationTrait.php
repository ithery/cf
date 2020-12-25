<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 28, 2020
 */
trait CView_Trait_ManageTranslationTrait {
    /**
     * The translation replacements for the translation being rendered.
     *
     * @var array
     */
    protected $translationReplacements = [];

    /**
     * Start a translation block.
     *
     * @param array $replacements
     *
     * @return void
     */
    public function startTranslation($replacements = []) {
        ob_start();

        $this->translationReplacements = $replacements;
    }

    /**
     * Render the current translation.
     *
     * @return string
     */
    public function renderTranslation() {
        return CTranslation::translator()->get(
            trim(ob_get_clean()),
            $this->translationReplacements
        );
    }
}
