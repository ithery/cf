<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use Throwable;
use Beste\Json;
use function array_filter;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Kreait\Firebase\Exception\RemoteConfigException;

use Kreait\Firebase\Exception\RemoteConfigApiExceptionConverter;

/**
 * @internal
 */
class ApiClient {
    private string $baseUri;

    private ClientInterface $client;

    private RemoteConfigApiExceptionConverter $errorHandler;

    public function __construct(
        string $projectId,
        ClientInterface $client,
        RemoteConfigApiExceptionConverter $errorHandler
    ) {
        $this->client = $client;
        $this->errorHandler = $errorHandler;
        $this->baseUri = "https://firebaseremoteconfig.googleapis.com/v1/projects/{$projectId}/remoteConfig";
    }

    /**
     * @see https://firebase.google.com/docs/reference/remote-config/rest/v1/projects/getRemoteConfig
     *
     * @param null|VersionNumber|int|string $versionNumber
     *
     * @throws RemoteConfigException
     */
    public function getTemplate($versionNumber = null): ResponseInterface {
        if (in_array($versionNumber, [null, '', '0'], true)) {
            $versionNumber = VersionNumber::fromValue(0);
        }

        return $this->requestApi('GET', 'remoteConfig', [
            'query' => [
                'version_number' => (string) $versionNumber,
            ],
        ]);
    }

    /**
     * @throws RemoteConfigException
     */
    public function validateTemplate(Template $template): ResponseInterface {
        return $this->requestApi('PUT', 'remoteConfig', [
            'headers' => [
                'Content-Type' => 'application/json; UTF-8',
                'If-Match' => $template->etag(),
            ],
            'query' => [
                'validate_only' => 'true',
            ],
            'body' => Json::encode($template),
        ]);
    }

    /**
     * @throws RemoteConfigException
     */
    public function publishTemplate(Template $template): ResponseInterface {
        return $this->requestApi('PUT', 'remoteConfig', [
            'headers' => [
                'Content-Type' => 'application/json; UTF-8',
                'If-Match' => $template->etag(),
            ],
            'body' => Json::encode($template),
        ]);
    }

    /**
     * @see https://firebase.google.com/docs/reference/remote-config/rest/v1/projects.remoteConfig/listVersions
     *
     * @throws RemoteConfigException
     */
    public function listVersions(FindVersions $query, ?string $nextPageToken = null): ResponseInterface {
        $uri = $this->baseUri . ':listVersions';

        $since = $query->since();
        $until = $query->until();
        $lastVersionNumber = $query->lastVersionNumber();
        $pageSize = $query->pageSize();

        $since = $since ? $since->format('Y-m-d\TH:i:s.v\Z') : null;
        $until = $until ? $until->format('Y-m-d\TH:i:s.v\Z') : null;
        $lastVersionNumber = $lastVersionNumber !== null ? (string) $lastVersionNumber : null;
        $pageSize = $pageSize !== null ? (string) $pageSize : null;

        return $this->requestApi('GET', $uri, [
            'query' => array_filter([
                'startTime' => $since,
                'endTime' => $until,
                'endVersionNumber' => $lastVersionNumber,
                'pageSize' => $pageSize,
                'pageToken' => $nextPageToken,
            ], fn ($value): bool => $value !== null),
        ]);
    }

    /**
     * @throws RemoteConfigException
     */
    public function rollbackToVersion(VersionNumber $versionNumber): ResponseInterface {
        $uri = $this->baseUri . ':rollback';

        return $this->requestApi('POST', $uri, [
            'json' => [
                'version_number' => (string) $versionNumber,
            ],
        ]);
    }

    /**
     * @param non-empty-string          $method
     * @param non-empty-string          $uri
     * @param null|array<string, mixed> $options
     *
     * @throws RemoteConfigException
     */
    private function requestApi(string $method, string $uri, ?array $options = null): ResponseInterface {
        $options ??= [];
        $options['decode_content'] = 'gzip';

        try {
            return $this->client->request($method, $uri, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
