<?php

interface CVendor_Firebase_Messaging_ExceptionInterface extends CVendor_Firebase_ExceptionInterface {
    /**
     * @return array<mixed>
     */
    public function errors();
}
