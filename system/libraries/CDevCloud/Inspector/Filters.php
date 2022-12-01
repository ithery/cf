<?php
class CDevCloud_Inspector_Filters {
    /**
     * Determine if the given request should be monitored.
     *
     * @param string[]      $notAllowed
     * @param CHTTP_Request $request
     *
     * @return bool
     */
    public static function isApprovedRequest(array $notAllowed, CHTTP_Request $request): bool {
        foreach ($notAllowed as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if current command should be monitored.
     *
     * @param string   $command
     * @param string[] $notAllowed
     *
     * @return bool
     */
    public static function isApprovedArtisanCommand(string $command, array $notAllowed = null): bool {
        return is_null($notAllowed) || !in_array($command, $notAllowed);
    }

    /**
     * Determine if the given Job class should be monitored.
     *
     * @param null|string[] $notAllowed
     * @param string        $class
     *
     * @return bool
     */
    public static function isApprovedJobClass(string $class, array $notAllowed = null) {
        return !is_array($notAllowed) || !in_array($class, $notAllowed);
    }

    /**
     * Hide the given request parameters.
     *
     * @param array $data
     * @param array $hidden
     *
     * @return array
     */
    public static function hideParameters($data, $hidden) {
        foreach ($hidden as $parameter) {
            if (carr::get($data, $parameter)) {
                carr::set($data, $parameter, '********');
            }
        }

        return $data;
    }
}
