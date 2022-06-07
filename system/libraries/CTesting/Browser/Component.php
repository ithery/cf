<?php

abstract class CTesting_Browser_Component {
    /**
     * Get the root selector associated with this component.
     *
     * @return string
     */
    abstract public function selector();

    /**
     * Assert that the current page contains this component.
     *
     * @param \CTesting_Browser $browser
     *
     * @return void
     */
    public function assert(CTesting_Browser $browser) {
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements() {
        return [];
    }

    /**
     * Allow this class to be used in place of a selector string.
     *
     * @return string
     */
    public function __toString() {
        return '';
    }
}
