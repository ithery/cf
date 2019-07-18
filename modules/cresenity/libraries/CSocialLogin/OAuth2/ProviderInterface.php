<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 15, 2019, 8:00:25 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CSocialLogin_OAuth2_ProviderInterface {

    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return CSocialLogin_OAuth2_User
     */
    public function user();
}
