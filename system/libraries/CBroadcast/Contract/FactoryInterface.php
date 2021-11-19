<?php
interface CBroadcast_Contract_FactoryInterface {
    /**
     * Get a broadcaster implementation by name.
     *
     * @param null|string $name
     *
     * @return \CBroadcast_Contract_BroadcasterInterface
     */
    public function connection($name = null);
}
