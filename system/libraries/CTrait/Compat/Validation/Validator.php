<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 11:22:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Validation_Validator {

    public function first_error() {
        return $this->errors()->first();
    }

}
