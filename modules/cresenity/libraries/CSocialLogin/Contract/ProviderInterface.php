<?php
interface CSocialLogin_Contract_ProviderInterface {
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\CHTTP_RedirectResponse
     */
    public function redirect();

    /**
     * Get the User instance for the authenticated user.
     *
     * @return \CSocialLogin_Contract_UserInterface
     */
    public function user();
}
