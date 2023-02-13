<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CTrait_Element_Property_HelpText {
    /**
     * Help Text of element.
     *
     * @var string
     */
    protected $helpText;

    /**
     * Label of element before translation.
     *
     * @var string
     */
    protected $rawHelpText;

    /**
     * @param string     $helpText
     * @param bool|array $lang
     *
     * @return $this
     */
    public function setHelpText($helpText, $lang = true) {
        $this->rawHelpText = $helpText;
        if ($lang !== false) {
            $helpText = c::__($helpText, is_array($lang) ? $lang : []);
        }
        $this->helpText = $helpText;

        return $this;
    }

    /**
     * @return string
     */
    public function getHelpText() {
        return $this->rawHelpText;
    }

    /**
     * @return string
     */
    public function getTranslationHelpText() {
        return $this->helpText;
    }
}
