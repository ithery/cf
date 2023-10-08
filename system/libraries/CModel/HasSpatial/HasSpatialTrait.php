<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 7, 2023, 3:15:06 PM
 */
trait CModel_HasSlug_HasSpatialTrait {
    public function newModelQuery($query): CModel_HasSpatial_SpatialModelQuery {
        return new CModel_HasSpatial_SpatialModelQuery($query);
    }
}
