<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:36:06 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */


/**
 * DSL Interface.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 */
interface CElastic_Client_QueryBuilder_DSL
{
    const TYPE_QUERY = 'query';
    const TYPE_AGGREGATION = 'aggregation';
    const TYPE_SUGGEST = 'suggest';
    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType();
}