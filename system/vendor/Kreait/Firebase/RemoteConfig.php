<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use Beste\Json;
use Traversable;
use function is_string;
use function array_shift;
use Psr\Http\Message\ResponseInterface;
use Kreait\Firebase\RemoteConfig\Version;
use Kreait\Firebase\RemoteConfig\Template;
use Kreait\Firebase\RemoteConfig\ApiClient;
use Kreait\Firebase\RemoteConfig\FindVersions;

use Kreait\Firebase\RemoteConfig\VersionNumber;
use Kreait\Firebase\Exception\RemoteConfig\VersionNotFound;

/**
 * @internal
 *
 * @phpstan-import-type RemoteConfigTemplateShape from Template
 */
final class RemoteConfig implements Contract\RemoteConfig {
    private ApiClient $client;

    public function __construct(ApiClient $client) {
        $this->client = $client;
    }

    /**
     * @param null|null|Version|VersionNumber|int|string $versionNumber
     *
     * @return Template
     */
    public function get($versionNumber = null): Template {
        if ($versionNumber !== null) {
            $versionNumber = $this->ensureVersionNumber($versionNumber);
        }

        return $this->buildTemplateFromResponse($this->client->getTemplate($versionNumber));
    }

    public function validate($template): void {
        $this->client->validateTemplate($this->ensureTemplate($template));
    }

    public function publish($template): string {
        $etag = $this->client
            ->publishTemplate($this->ensureTemplate($template))
            ->getHeader('ETag');

        $etag = array_shift($etag);

        if (!is_string($etag)) {
            return '*';
        }

        if ($etag === '') {
            return '*';
        }

        return $etag;
    }

    /**
     * @param VersionNumber|int|string $versionNumber
     *
     * @return Version
     */
    public function getVersion($versionNumber): Version {
        $versionNumber = $this->ensureVersionNumber($versionNumber);

        foreach ($this->listVersions() as $version) {
            if ($version->versionNumber()->equalsTo($versionNumber)) {
                return $version;
            }
        }

        throw VersionNotFound::withVersionNumber($versionNumber);
    }

    /**
     * @param VersionNumber|int|string $versionNumber
     *
     * @return Template
     */
    public function rollbackToVersion($versionNumber): Template {
        $versionNumber = $this->ensureVersionNumber($versionNumber);

        return $this->buildTemplateFromResponse($this->client->rollbackToVersion($versionNumber));
    }

    public function listVersions($query = null): Traversable {
        $query = $query instanceof FindVersions ? $query : FindVersions::fromArray((array) $query);
        $pageToken = null;
        $count = 0;
        $limit = $query->limit();

        do {
            $response = $this->client->listVersions($query, $pageToken);
            $result = Json::decode((string) $response->getBody(), true);

            foreach ((array) ($result['versions'] ?? []) as $versionData) {
                ++$count;

                yield Version::fromArray($versionData);

                if ($count === $limit) {
                    return;
                }
            }

            $pageToken = $result['nextPageToken'] ?? null;
        } while ($pageToken !== null);
    }

    /**
     * @param Template|RemoteConfigTemplateShape $value
     */
    private function ensureTemplate($value): Template {
        return $value instanceof Template ? $value : Template::fromArray($value);
    }

    /**
     * @param VersionNumber|positive-int|non-empty-string $value
     */
    private function ensureVersionNumber($value): VersionNumber {
        if ($value instanceof VersionNumber) {
            return $value;
        }

        if ($value instanceof Version) {
            return $value->versionNumber();
        }

        return VersionNumber::fromValue($value);
    }

    private function buildTemplateFromResponse(ResponseInterface $response): Template {
        $etag = $response->getHeaderLine('ETag');

        if ($etag === '') {
            $etag = '*';
        }

        $data = Json::decode((string) $response->getBody(), true);

        return Template::fromArray($data, $etag);
    }
}
