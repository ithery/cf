<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Defines how an output builder should take a generated
 * diff array and return a string representation of that diff.
 */
interface CComparator_Differ_OutputInterface {

    public function getDiff(array $diff);
}
