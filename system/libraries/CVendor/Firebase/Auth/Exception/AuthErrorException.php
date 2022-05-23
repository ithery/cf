<?php
final class CVendor_Firebase_Auth_Exception_AuthErrorException extends RuntimeException implements CVendor_Firebase_Auth_ExceptionInterface {
    public static function missingProjectId($message) {
        $factoryClass = CVendor_Firebase::class;

        $fullMessage = <<<MSG
{$message}
The current Firebase project is configured without a project ID. The project
ID can be determined automatically with service account credentials, by
providing a `GOOGLE_CLOUD_PROJECT=project_id` environment variable, or
manually by using the respective method when instantiating the SDK's
factory ({$factoryClass}).
MSG;

        return new self($fullMessage);
    }
}
