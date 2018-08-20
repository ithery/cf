<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:19:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Container for all DBAL events.
 *
 * This class cannot be instantiated.
 *
 */
final class CJob_Events {

    /**
     * Private constructor. This class cannot be instantiated.
     */
    private function __construct() {
        
    }

    const onJobPreRun = 'onJobPreRun';
    const onJobPostRun = 'onJobPostRun';
    const onBackgroundJobPreRun = 'onBackgroundJobPreRun';
    const onBackgroundJobPostRun = 'onBackgroundJobPostRun';

}
