<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 28, 2019, 3:10:38 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Relation_Pivot extends CModel {

    use CModel_Relation_Trait_AsPivot;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
