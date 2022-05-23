<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 13, 2018, 11:38:00 PM
 */
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
