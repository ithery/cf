<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:53:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Providers MUST always be stateless and immutable.
 */
interface CGeo_Interface_ProviderInterface {

    /**
     * @param CGeo_Query_GeocodeQuery $query
     *
     * @return CGeo_Interface_CollectionInterface
     *
     * @throws CGeo_Exception
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query);

    /**
     * @param CGeo_Query_ReverseQuery $query
     *
     * @return CGeo_Collection
     *
     * @throws CGeo_Exception
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query);

    /**
     * Returns the provider's name.
     *
     * @return string
     */
    public function getName();
}
