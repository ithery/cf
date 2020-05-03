<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CTracker_Model_SystemClass extends CTracker_Model {

    use CModel_Tracker_TrackerSystemClassTrait;

    protected $table = 'log_system_class';
    protected $fillable = [
        'name',
    ];

}
