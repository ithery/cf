<?php

interface CHTTP_ResponseCache_Hasher_RequestHasherInterface {
    public function getHashFor(CHTTP_Request $request);
}
