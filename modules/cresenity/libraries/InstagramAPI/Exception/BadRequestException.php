<?php

/**
 * Used for endpoint calls that fail with HTTP code "400 Bad Request", but only
 * if no other more serious exception was found in the server response.
 */
class InstagramAPI_Exception_BadRequestException extends InstagramAPI_Exception_EndpointException
{
}
