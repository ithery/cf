<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 4:34:34 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * Latest elasticsearch DSL.
 *
 * Latest refers to the version mentioned in README.md.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/index.html
 *
 * @author Manuel Andreo Garcia <andreo.garcia@gmail.com>
 */
class CElastic_Client_QueryBuilder_Version_Latest extends CElastic_Client_QueryBuilder_Version_Version240 {
    // this class always points to the latest valid DSL version
}
