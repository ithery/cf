<?php

/**
 * 
 */
interface CExporter_Transaction_TransactionHandler
{
	/**
     * @param callable $callback
     *
     * @return mixed
     */
    public function __invoke(callable $callback);
}