<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 25, 2019, 10:09:30 PM
 */
interface CVendor_Odoo_ModelInterface extends JsonSerializable, ArrayAccess {
    /**
     * Get a model instance data item, using "dot" notation.
     *
     * @param string $key     example 'parent_ids.2'
     * @param mixed  $default
     * @returns mixed
     */
    public function get($key, $default = null);
}
