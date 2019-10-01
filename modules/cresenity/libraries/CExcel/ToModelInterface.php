<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 3:11:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CExcel_ToModelInterface {

    /**
     * @param array $row
     *
     * @return CModel|CModel[]|null
     */
    public function model(array $row);
}
