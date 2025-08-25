<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database;

use Throwable;
use Beste\Json;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Exception\DatabaseApiExceptionConverter;

/**
 * @internal
 */
class ApiClient {
    private ClientInterface $client;

    private UrlBuilder $resourceUrlBuilder;

    private DatabaseApiExceptionConverter $errorHandler;

    public function __construct(
        ClientInterface $client,
        UrlBuilder $resourceUrlBuilder,
        DatabaseApiExceptionConverter $errorHandler
    ) {
        $this->client = $client;
        $this->resourceUrlBuilder = $resourceUrlBuilder;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @throws DatabaseException
     */
    public function get(string $path): mixed {
        $response = $this->requestApi('GET', $path);

        return Json::decode((string) $response->getBody(), true);
    }

    /**
     * @throws DatabaseException
     *
     * @return array<string, mixed>
     */
    public function getWithETag(string $path): array {
        $response = $this->requestApi('GET', $path, [
            'headers' => [
                'X-Firebase-ETag' => 'true',
            ],
        ]);

        $value = Json::decode((string) $response->getBody(), true);
        $etag = $response->getHeaderLine('ETag');

        return [
            'value' => $value,
            'etag' => $etag,
        ];
    }

    /**
     * @throws DatabaseException
     */
    public function set(string $path, mixed $value): mixed {
        $response = $this->requestApi('PUT', $path, ['json' => $value]);

        return Json::decode((string) $response->getBody(), true);
    }

    /**
     * @throws DatabaseException
     */
    public function setWithEtag(string $path, mixed $value, string $etag): mixed {
        $response = $this->requestApi('PUT', $path, [
            'headers' => [
                'if-match' => $etag,
            ],
            'json' => $value,
        ]);

        return Json::decode((string) $response->getBody(), true);
    }

    /**
     * @throws DatabaseException
     */
    public function removeWithEtag(string $path, string $etag): void {
        $this->requestApi('DELETE', $path, [
            'headers' => [
                'if-match' => $etag,
            ],
        ]);
    }

    /**
     * @throws DatabaseException
     */
    public function updateRules(string $path, RuleSet $ruleSet): mixed {
        $rules = $ruleSet->getRules();
        $encodedRules = Json::encode((object) $rules);

        $response = $this->requestApi('PUT', $path, [
            'body' => $encodedRules,
        ]);

        return Json::decode((string) $response->getBody(), true);
    }

    /**
     * @throws DatabaseException
     */
    public function push(string $path, mixed $value): string {
        $response = $this->requestApi('POST', $path, ['json' => $value]);

        return Json::decode((string) $response->getBody(), true)['name'];
    }

    /**
     * @throws DatabaseException
     */
    public function remove(string $path): void {
        $this->requestApi('DELETE', $path);
    }

    /**
     * @param array<array-key, mixed> $values
     *
     * @throws DatabaseException
     */
    public function update(string $path, array $values): void {
        $this->requestApi('PATCH', $path, ['json' => $values]);
    }

    /**
     * @param null|array<string, mixed> $options
     *
     * @throws DatabaseException
     */
    private function requestApi(string $method, string $path, ?array $options = []): ResponseInterface {
        $options ??= [];

        $uri = new Uri($path);

        $url = $this->resourceUrlBuilder->getUrl(
            $uri->getPath(),
            Query::parse($uri->getQuery()),
        );

        $request = new Request($method, $url);

        try {
            return $this->client->send($request, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
