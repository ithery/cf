<?php

/**
 * Interface used with classes that return a promise.
 */
interface GuzzleHttp_Promise_PromisorInterface {

    /**
     * Returns a promise.
     *
     * @return PromiseInterface
     */
    public function promise();
}
