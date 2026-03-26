<?php

defined('SYSPATH') or die('No direct access allowed.');

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
