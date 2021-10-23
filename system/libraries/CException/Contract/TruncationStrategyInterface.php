<?php
interface CException_Contract_TruncationStrategyInterface {
    /**
     * @param array $payload
     *
     * @return array
     */
    public function execute(array $payload);
}
