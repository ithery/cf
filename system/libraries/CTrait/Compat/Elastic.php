<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Elastic {
    /**
     * Begin a fluent search query builder.
     *
     * @deprecated since version 1.2
     *
     * @return CElastic_DSL_SearchBuilder
     */
    public function searchBuilder() {
        return new CElastic_DSL_SearchBuilder($this, $this->getDSLQuery());
    }
}
