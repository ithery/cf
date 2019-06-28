<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 25, 2019, 9:13:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * This interface defines the minimum methods any documentor needs to implement.
 * @package Ripcord
 */
interface CXMLRPC_DocumentorInterface {

    public function setMethodData($methods);

    public function handle($rpcServer);

    public function getIntrospectionXML();
}
