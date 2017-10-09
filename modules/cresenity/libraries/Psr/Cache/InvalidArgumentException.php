<?php

/**
 * Exception interface for invalid cache arguments.
 *
 * Any time an invalid argument is passed into a method it must throw an
 * exception class which implements Psr_Cache_InvalidArgumentException.
 */
interface Psr_Cache_InvalidArgumentException extends Psr_Cache_CacheException {
    
}
