<?php

/**
 * Used for endpoint calls that fail with HTTP code "404 Not Found", but only
 * if no other more serious exception was found in the server response.
 */
class InstagramAPI_Exception_NotFoundException extends InstagramAPI_Exception_EndpointException {
    
}
