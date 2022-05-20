<?php

interface CVendor_Firebase_JWT_Contract_TokenInterface {
    /**
     * @return array<string, mixed>
     */
    public function headers();

    /**
     * @return array<string, mixed>
     */
    public function payload();

    public function toString();

    public function __toString();
}
