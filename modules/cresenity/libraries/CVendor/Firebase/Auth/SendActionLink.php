<?php

final class CVendor_Firebase_Auth_SendActionLink {
    /**
     * @var CVendor_Firebase_Auth_CreateActionLink
     */
    private $action;

    /**
     * @var null|string
     */
    private $locale;

    /**
     * @var null|string
     */
    private $idTokenString = null;

    public function __construct(CVendor_Firebase_Auth_CreateActionLink $action, $locale = null) {
        $this->action = $action;
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function type() {
        return $this->action->type();
    }

    /**
     * @return string
     */
    public function email() {
        return $this->action->email();
    }

    /**
     * @return CVendor_Firebase_Auth_ActionCodeSettingsInterface
     */
    public function settings() {
        return $this->action->settings();
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->action->tenantId();
    }

    /**
     * @return null|string
     */
    public function locale() {
        return $this->locale;
    }

    /**
     * Only to be used when the API endpoint expects the ID Token of the given user.
     *
     * Currently seems only to be the case on VERIFY_EMAIL actions.
     *
     * @param string $idTokenString
     *
     * @internal
     *
     * @see https://github.com/firebase/firebase-js-sdk/issues/1958
     *
     * @return self
     */
    public function withIdTokenString($idTokenString) {
        $instance = clone $this;
        $instance->action = clone $this->action;
        $instance->idTokenString = $idTokenString;

        return $instance;
    }

    /**
     * @internal
     *
     * Only to be used when the API endpoint expects the ID Token of the given user.
     *
     * Currently seems only to be the case on VERIFY_EMAIL actions.
     *
     * @see https://github.com/firebase/firebase-js-sdk/issues/1958
     *
     * @return null|string
     */
    public function idTokenString() {
        return $this->idTokenString;
    }
}
