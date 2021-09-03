<?php

class CRouting_Exception_UrlGenerationException extends Exception {
    /**
     * Create a new exception for missing route parameters.
     *
     * @param CRouting_Route $route
     *
     * @return static
     */
    public static function forMissingParameters($route) {
        return new static("Missing required parameters for [Route: {$route->getName()}] [URI: {$route->uri()}].");
    }
}
