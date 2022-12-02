<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 7, 2018, 5:25:54 AM
 */
class CElement_Component_Metric_ValueMetric extends CElement_Component {
    use CElement_Trait_UseViewTrait;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->view = 'cresenity/element/component/metric/value';
    }
}
