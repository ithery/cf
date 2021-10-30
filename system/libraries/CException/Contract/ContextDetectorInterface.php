<?php

interface CException_Contract_ContextDetectorInterface {
    /**
     * @return CException_Contract_ContextInterface
     */
    public function detectCurrentContext();
}
