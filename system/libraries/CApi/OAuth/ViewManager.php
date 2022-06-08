<?php

class CApi_OAuth_ViewManager {
    protected $authorizeView = 'cresenity.api.oauth.authorize';

    protected $loginView = 'cresenity.api.oauth.login';

    public function __construct() {
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function setAuthorizeView($view) {
        $this->authorizeView = $view;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizeView() {
        return $this->authorizeView;
    }

    /**
     * @param string $view
     *
     * @return $this
     */
    public function setLoginView($view) {
        $this->loginView = $view;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginView() {
        return $this->loginView;
    }
}
