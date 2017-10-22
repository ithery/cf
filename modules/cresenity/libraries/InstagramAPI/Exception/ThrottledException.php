<?php

/**
 * Means that you have become throttled by Instagram's API server
 * because of too many requests. You must slow yourself down!
 */
class InstagramAPI_Exception_ThrottledException extends InstagramAPI_Exception_RequestException {
    
}
