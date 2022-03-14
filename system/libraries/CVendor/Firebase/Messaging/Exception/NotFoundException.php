<?php

final class CVendor_Firebase_Messaging_Exception_NotFoundException extends RuntimeException implements CVendor_Firebase_Messaging_ExceptionInterface {
    use CVendor_Firebase_Trait_ExceptionHasErrorsTrait;

    /**
     * @param array<mixed> $errors
     * @param string       $token
     *
     * @return self
     */
    public static function becauseTokenNotFound($token, array $errors = []) {
        $message = <<<MESSAGE
            The message could not be delivered to the device identified by '{$token}'.
            Although the token is syntactically correct, it is not known to the Firebase
            project you are using. This could have the following reasons:
            - The token has been unregistered from the project. This can happen when a user
              has logged out from the application on the given client, or if they have
              uninstalled or re-installed the application.
            - The token has been registered to a different Firebase project than the project
              you are using to send the message. A common reason for this is when you work
              with different application environments and are sending a message from one
              environment to a device in another environment.
            MESSAGE;

        $notFound = new self($message);
        $notFound->errors = $errors;

        return $notFound;
    }

    /**
     * @param string[] $errors
     *
     * @internal
     *
     * @return static
     */
    public function withErrors(array $errors) {
        $new = new self($this->getMessage(), $this->getCode(), $this->getPrevious());
        $new->errors = $errors;
        $new->response = $this->response;

        return $new;
    }
}
