<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:53:19 PM
 */

/**
 * Providers MUST always be stateless and immutable.
 */
interface CGeo_Interface_ProviderInterface {
    /**
     * @param CGeo_Query_GeocodeQuery $query
     *
     * @throws CGeo_Exception
     *
     * @return CGeo_Interface_CollectionInterface
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query);

    /**
     * @param CGeo_Query_ReverseQuery $query
     *
     * @throws CGeo_Exception
     *
     * @return CGeo_Collection
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query);

    /**
     * Returns the provider's name.
     *
     * @return string
     */
    public function getName();
}
