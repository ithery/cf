<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jan 12, 2022, 1:55:05 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_TableRow {
    /**
     * @param mixed $content
     *
     * @return CElement_Component_TableRow
     *
     * @deprecated since 1.2, use addColumn
     */
    public function add_column($content) {
        return $this->addColumn($content);
    }
}
