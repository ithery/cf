<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 1:19:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_DataProvider {

    /**
     *
     * @var CProvider_DataAbstract
     */
    protected $dataProvider;

    /**
     * 
     * @param string $q
     * @return $this
     */
    public function setDataFromQuery($q) {
        $this->dataProvider = CProvider::createSqlDataProvider();
        $this->dataProvider->setSql($q);

        return $this;
    }
    
    public function getData() {
        return $this->dataProvider->getData();
    }

}
