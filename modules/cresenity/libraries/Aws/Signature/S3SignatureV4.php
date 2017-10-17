<?php

/**
 * Amazon S3 signature version 4 support.
 */
class Aws_Signature_S3SignatureV4 extends Aws_Signature_SignatureV4
{
    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function signRequest(
        Psr_Http_Message_RequestInterface $request,
        Aws_Credentials_CredentialsInterface $credentials
    ) {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader(
                'X-Amz-Content-Sha256',
                $this->getPayload($request)
            );
        }

        return parent::signRequest($request, $credentials);
    }

    /**
     * Always add a x-amz-content-sha-256 for data integrity.
     */
    public function presign(
        Psr_Http_Message_RequestInterface $request,
        Aws_Credentials_CredentialsInterface $credentials,
        $expires,
        array $options = []
    ) {
        if (!$request->hasHeader('x-amz-content-sha256')) {
            $request = $request->withHeader(
                'X-Amz-Content-Sha256',
                $this->getPresignedPayload($request)
            );
        }

        return parent::presign($request, $credentials, $expires, $options);
    }

    /**
     * Override used to allow pre-signed URLs to be created for an
     * in-determinate request payload.
     */
    protected function getPresignedPayload(Psr_Http_Message_RequestInterface $request)
    {
        return Aws_Signature_SignatureV4::UNSIGNED_PAYLOAD;
    }

    /**
     * Amazon S3 does not double-encode the path component in the canonical request
     */
    protected function createCanonicalizedPath($path)
    {
        return '/' . ltrim($path, '/');
    }
}
