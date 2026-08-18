<?php

class CElement_Depends_Selector {
    /**
     * @var array
     */
    protected $selectors = [];

    /**
     * @param array $selectors
     *
     * @return void
     */
    public function __construct(array $selectors = []) {
        $this->setSelectors($selectors);
    }

    /**
     * @param CRenderable|string|array $selector
     *
     * @return $this
     */
    public function addSelector($selector) {
        if (is_array($selector)) {
            foreach ($selector as $selectorItem) {
                $this->addSelector($selectorItem);
            }
        } else {
            if ($selector instanceof CRenderable) {
                $selector = '#' . $selector->id();
            }
            $this->selectors[] = $selector;
        }

        return $this;
    }

    /**
     * @param CRenderable|string|array $selectors
     *
     * @return $this
     */
    public function setSelectors($selectors) {
        $this->selectors = [];
        $this->addSelector($selectors);

        return $this;
    }

    /**
     * @return string
     */
    public function getQuerySelector() {
        return implode(', ', $this->selectors);
    }

    /**
     * @return string
     */
    public function getScriptForValue() {
        $valueScripts = [];
        foreach ($this->selectors as $dependsOnSelector) {
            $valueScripts[] = "$('" . $dependsOnSelector . "').is(':checkbox') ? $('" . $dependsOnSelector . ":checked').val() : $('" . $dependsOnSelector . "').val()";
        }
        $valueScript = implode(', ', $valueScripts);
        if (count($this->selectors) > 1) {
            $valueScript = '[' . $valueScript . ']';
        }

        return $valueScript;
    }
}
