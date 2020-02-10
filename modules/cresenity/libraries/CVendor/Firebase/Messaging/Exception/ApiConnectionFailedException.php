<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CVendor_Firebase_Messaging_Exception_ApiConnectionFailedException extends RuntimeException implements CVendor_Firebase_Messaging_ExceptionInterface {

    use CVendor_Firebase_Trait_ExceptionHasRequestAndResponseTrait;
    use CVendor_Firebase_Trait_ExceptionHasErrorsTrait;
}
