<?php

class CComponent_GenerateSignedUploadUrl {
    public static function forLocal() {
        // return c::url()->temporarySignedRoute(
        //     'component.upload-file',
        //     c::now()->addMinutes(5)
        // );

        return curl::base() . 'cresenity/component/upload';
    }

    public static function forS3($file, $visibility = 'private') {
        $adapter = CComponent_FileUploadConfiguration::storage()->getDriver()->getAdapter();

        $fileType = $file->getMimeType();
        $fileHashName = CComponent_TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded($file);
        $path = CComponent_FileUploadConfiguration::path($fileHashName);

        $command = $adapter->getClient()->getCommand('putObject', array_filter([
            'Bucket' => $adapter->getBucket(),
            'Key' => $path,
            'ACL' => $visibility,
            'ContentType' => $fileType ?: 'application/octet-stream',
            'CacheControl' => null,
            'Expires' => null,
        ]));

        $signedRequest = $adapter->getClient()->createPresignedRequest(
            $command,
            '+5 minutes'
        );

        return [
            'path' => $fileHashName,
            'url' => (string) $signedRequest->getUri(),
            'headers' => static::headers($signedRequest, $fileType),
        ];
    }

    protected static function headers($signedRequest, $fileType) {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $fileType ?: 'application/octet-stream'
            ]
        );
    }
}
