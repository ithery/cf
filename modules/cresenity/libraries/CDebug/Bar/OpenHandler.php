<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 23, 2018, 1:39:40 AM
 */

/**
 * Handler to list and open saved dataset
 */
class CDebug_Bar_OpenHandler {
    protected $debugBar;

    /**
     * @param CDebug_Bar $debugBar
     *
     * @throws CDebug_Bar_Exception
     */
    public function __construct(CDebug_Bar $debugBar) {
        if (!$debugBar->isDataPersisted()) {
            throw new CDebug_Bar_Exception('DebugBar must have a storage backend to use OpenHandler');
        }
        $this->debugBar = $debugBar;
    }

    /**
     * Handles the current request
     *
     * @param array $request    Request data
     * @param bool  $echo
     * @param bool  $sendHeader
     *
     * @return string
     *
     * @throws CDebug_Bar_Exception
     */
    public function handle($request = null, $echo = true, $sendHeader = true) {
        if ($request === null) {
            $request = $_REQUEST;
        }
        $op = 'find';
        if (isset($request['op'])) {
            $op = $request['op'];
            if (!in_array($op, ['find', 'get', 'clear'])) {
                throw new CDebug_Bar_Exception("Invalid operation '{$request['op']}'");
            }
        }
        if ($sendHeader) {
            $this->debugBar->getHttpDriver()->setHeaders([
                'Content-Type' => 'application/json'
            ]);
        }
        $response = json_encode(call_user_func([$this, $op], $request));
        if ($echo) {
            echo $response;
        }
        return $response;
    }

    /**
     * Find operation
     *
     * @param $request
     *
     * @return array
     */
    protected function find($request) {
        $max = 20;
        if (isset($request['max'])) {
            $max = $request['max'];
        }
        $offset = 0;
        if (isset($request['offset'])) {
            $offset = $request['offset'];
        }
        $filters = [];
        foreach (['utime', 'datetime', 'ip', 'uri', 'method'] as $key) {
            if (isset($request[$key])) {
                $filters[$key] = $request[$key];
            }
        }
        return $this->debugBar->getStorage()->find($filters, $max, $offset);
    }

    /**
     * Get operation
     *
     * @param $request
     *
     * @return array
     *
     * @throws CDebug_Bar_Exception
     */
    protected function get($request) {
        if (!isset($request['id'])) {
            throw new CDebug_Bar_Exception("Missing 'id' parameter in 'get' operation");
        }
        return $this->debugBar->getStorage()->get($request['id']);
    }

    /**
     * Clear operation
     *
     * @param mixed $request
     */
    protected function clear($request) {
        $this->debugBar->getStorage()->clear();
        return ['success' => true];
    }
}
