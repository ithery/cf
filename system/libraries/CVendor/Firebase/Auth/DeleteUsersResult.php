<?php

use Psr\Http\Message\ResponseInterface;

final class CVendor_Firebase_Auth_DeleteUsersResult {
    /**
     * @var int
     */
    private $failureCount;

    /**
     * @var int
     */
    private $successCount;

    /**
     * @var array{
     *             index: int,
     *             localId: string,
     *             message: string
     *             }
     */
    private array $rawErrors;

    /**
     * @param int $successCount
     * @param int $failureCount
     * @param array{
     *     index: int,
     *     localId: string,
     *     message: string
     * } $rawErrors
     */
    private function __construct($successCount, $failureCount, array $rawErrors) {
        $this->successCount = $successCount;
        $this->failureCount = $failureCount;
        $this->rawErrors = $rawErrors;
    }

    /**
     * @param CVendor_Firebase_Auth_DeleteUsersRequest $request
     * @param ResponseInterface                        $response
     *
     * @return self
     */
    public static function fromRequestAndResponse(CVendor_Firebase_Auth_DeleteUsersRequest $request, ResponseInterface $response): self {
        $data = CVendor_Firebase_Util_JSON::decode((string) $response->getBody(), true);
        $errors = $data['errors'] ?? [];

        $failureCount = \count($errors);
        $successCount = \count($request->uids()) - $failureCount;

        return new self($successCount, $failureCount, $errors);
    }

    /**
     * @return int
     */
    public function failureCount() {
        return $this->failureCount;
    }

    /**
     * @return int
     */
    public function successCount() {
        return $this->successCount;
    }

    /**
     * @return array{
     *                index: int,
     *                localId: string,
     *                message: string
     *                }
     */
    public function rawErrors() {
        return $this->rawErrors;
    }
}
