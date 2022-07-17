<?php

/**
 * Pseudo Thread.
 */
class CServer_SMTP_Thread {
    /**
     * @var int
     */
    private $exit = 0;

    /**
     * @return int
     */
    public function getExit(): int {
        return $this->exit;
    }

    /**
     * @param int $exit
     */
    public function setExit(int $exit = 1) {
        $this->exit = $exit;
    }
}
