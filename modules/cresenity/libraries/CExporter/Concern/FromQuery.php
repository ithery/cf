<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

interface CExporter_Concern_FromQuery {

    /**
     * @return CDatabase_Query_Builder
     */
    public function query();
}
