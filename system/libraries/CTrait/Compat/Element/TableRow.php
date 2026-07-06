<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_TableRow
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
        /** @var CElement_Component_TableRow $this */
        return $this->addColumn($content);
    }
}
