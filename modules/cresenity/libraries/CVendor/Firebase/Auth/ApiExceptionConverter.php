<?php

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\NetworkExceptionInterface;

/**
 * @internal
 */
class CVendor_Firebase_Auth_ApiExceptionConverter {
    private $responseParser;

    public function __construct() {
        $this->responseParser = new CVendor_Firebase_Http_ErrorResponseParser();
    }

    /**
     * @param Throwable $exception
     *
     * @return CVendor_Firebase_Auth_ExceptionInterface
     */
    public function convertException(Throwable $exception) {
        if ($exception instanceof RequestException) {
            return $this->convertGuzzleRequestException($exception);
        }

        if ($exception instanceof NetworkExceptionInterface) {
            return new CVendor_Firebase_Auth_Exception_ApiConnectionFailedException('Unable to connect to the API: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        return new CVendor_Firebase_Auth_Exception_AuthErrorException($exception->getMessage(), $exception->getCode(), $exception);
    }

    /**
     * @param RequestException $e
     *
     * @return CVendor_Firebase_Auth_ExceptionInterface
     */
    private function convertGuzzleRequestException(RequestException $e) {
        $message = $e->getMessage();
        $code = $e->getCode();
        $response = $e->getResponse();

        if ($response !== null) {
            $message = $this->responseParser->getErrorReasonFromResponse($response);
            $code = $response->getStatusCode();
        }

        if (\mb_stripos($message, 'credentials_mismatch') !== false) {
            return new CVendor_Firebase_Auth_Exception_CredentialsMismatchException('Invalid custom token: The custom token corresponds to a different Firebase project.', $code, $e);
        }

        if (\mb_stripos($message, 'email_exists') !== false) {
            return new CVendor_Firebase_Auth_Exception_EmailExistsException('The email address is already in use by another account.', $code, $e);
        }

        if (\mb_stripos($message, 'email_not_found') !== false) {
            return new CVendor_Firebase_Auth_Exception_EmailNotFoundException('There is no user record corresponding to this identifier. The user may have been deleted.', $code, $e);
        }

        if (\mb_stripos($message, 'invalid_custom_token') !== false) {
            return new CVendor_Firebase_Auth_Exception_InvalidCustomTokenException('Invalid custom token: The custom token format is incorrect or the token is invalid for some reason (e.g. expired, invalid signature, etc.)', $code, $e);
        }

        if (\mb_stripos($message, 'invalid_password') !== false) {
            return new CVendor_Firebase_Auth_Exception_InvalidPasswordException('The password is invalid or the user does not have a password.', $code, $e);
        }

        if (\mb_stripos($message, 'missing_password') !== false) {
            return new CVendor_Firebase_Auth_Exception_MissingPasswordException('Missing Password', $code, $e);
        }

        if (\mb_stripos($message, 'operation_not_allowed') !== false) {
            return new CVendor_Firebase_Auth_Exception_OperationNotAllowedException('Operation not allowed.', $code, $e);
        }

        if (\mb_stripos($message, 'user_disabled') !== false) {
            return new CVendor_Firebase_Auth_Exception_UserDisabledException('The user account has been disabled by an administrator.', $code, $e);
        }

        if (\mb_stripos($message, 'user_not_found') !== false) {
            return new CVendor_Firebase_Auth_Exception_UserNotFoundException('There is no user record corresponding to this identifier. The user may have been deleted.', $code, $e);
        }

        if (\mb_stripos($message, 'weak_password') !== false) {
            return new CVendor_Firebase_Auth_Exception_WeakPasswordException('The password must be 6 characters long or more.', $code, $e);
        }

        if (\mb_stripos($message, 'phone_number_exists') !== false) {
            return new CVendor_Firebase_Auth_Exception_PhoneNumberExistsException('The phone number is already in use by another account.', $code, $e);
        }

        if (\mb_stripos($message, 'invalid_idp_response') !== false) {
            return new CVendor_Firebase_Auth_Exception_ProviderLinkFailedException('The supplied auth credential is malformed or has expired.', $code, $e);
        }

        if (\mb_stripos($message, 'invalid_id_token') !== false) {
            return new CVendor_Firebase_Auth_Exception_ProviderLinkFailedException('The user\'s credential is no longer valid. The user must sign in again.', $code, $e);
        }

        if (\mb_stripos($message, 'federated_user_id_already_linked') !== false) {
            return new CVendor_Firebase_Auth_Exception_ProviderLinkFailedException('This credential is already associated with a different user account.', $code, $e);
        }

        if (\mb_stripos($message, 'expired_oob_code') !== false) {
            return new CVendor_Firebase_Auth_Exception_ExpiredOobCodeException('The action code has expired.', $code, $e);
        }

        if (\mb_stripos($message, 'invalid_oob_code') !== false) {
            return new CVendor_Firebase_Auth_Exception_InvalidOobCodeException('The action code is invalid. This can happen if the code is malformed, expired, or has already been used.', $code, $e);
        }

        return new CVendor_Firebase_Auth_Exception_AuthErrorException($message, $code, $e);
    }
}
