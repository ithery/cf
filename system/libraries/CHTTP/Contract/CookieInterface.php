<?php

interface CHTTP_Contract_CookieInterface {
    /**
     * Create a new cookie instance.
     *
     * @param string      $name
     * @param string      $value
     * @param int         $minutes
     * @param null|string $path
     * @param null|string $domain
     * @param null|bool   $secure
     * @param bool        $httpOnly
     * @param bool        $raw
     * @param null|string $sameSite
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);

    /**
     * Create a cookie that lasts "forever" (five years).
     *
     * @param string      $name
     * @param string      $value
     * @param null|string $path
     * @param null|string $domain
     * @param null|bool   $secure
     * @param bool        $httpOnly
     * @param bool        $raw
     * @param null|string $sameSite
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null);

    /**
     * Expire the given cookie.
     *
     * @param string      $name
     * @param null|string $path
     * @param null|string $domain
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forget($name, $path = null, $domain = null);

    /**
     * Queue a cookie to send with the next response.
     *
     * @param array $parameters
     *
     * @return void
     */
    public function queue(...$parameters);

    /**
     * Remove a cookie from the queue.
     *
     * @param string      $name
     * @param null|string $path
     *
     * @return void
     */
    public function unqueue($name, $path = null);

    /**
     * Get the cookies which have been queued for the next request.
     *
     * @return array
     */
    public function getQueuedCookies();

    /**
     * Determine if a cookie has been queued.
     *
     * @param string      $key
     * @param null|string $path
     *
     * @return bool
     */
    public function hasQueued($key, $path = null);
}
