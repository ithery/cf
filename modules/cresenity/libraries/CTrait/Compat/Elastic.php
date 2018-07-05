<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 11:38:00 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Elastic {
    /**
     * Begin a fluent search query builder.
     *
     * @deprecated since version 1.2
     * @return CElastic_DSL_SearchBuilder
     */
    public function searchBuilder() {
        return new CElastic_DSL_SearchBuilder($this, $this->getDSLQuery());
    }
  
}
