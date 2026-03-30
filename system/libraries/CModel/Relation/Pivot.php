<?php

defined('SYSPATH') or die('No direct access allowed.');

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
