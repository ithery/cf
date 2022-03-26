<?php
interface CSession_ExistenceAwareInterface {
    /**
     * Set the existence state for the session.
     *
     * @param bool $value
     *
     * @return \SessionHandlerInterface
     */
    public function setExists($value);
}
