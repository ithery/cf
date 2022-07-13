<?php

interface CBot_Contract_VerifiesServiceInterface {
    public function verifyRequest(CHTTP_Request $request);
}
