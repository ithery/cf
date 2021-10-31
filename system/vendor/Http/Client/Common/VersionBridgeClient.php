<?php

declare(strict_types=1);

namespace Http\Client\Common;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A client that helps you migrate from php-http/httplug 1.x to 2.x. This
 * will also help you to support PHP5 at the same time you support 2.x.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait VersionBridgeClient
{
    abstract protected function doSendRequest(RequestInterface $request);

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->doSendRequest($request);
    }
}
The callable to provide must have the same arguments and return type as PluginClientFactory::createClient.
     * This is used by the HTTPlugBundle to provide a better Symfony integration.
     * Unlike the createClient method, this one is static to allow zero configuration profiling by hooking into early
     * application execution.
     *
     * @internal
     *
     * @param callable(ClientInterface|HttpAsyncClient, Plugin[], array): PluginClient $factory
     */
    public static function setFactory(callable $factory): void
    {
        static::$factory = $factory;
    }

    /**
     * @param ClientInterface|HttpAsyncClient $client
     * @param Plugin[]                        $plugins
     * @param array                           $options {
     *
     *     @var string $client_name to give client a name which may be used when displaying client information  like in
     *         the HTTPlugBundle profiler.
     * }
     *
     * @see PluginClient constructor for PluginClient specific $options.
     */
    public function createClient($client, array $plugins = [], array $options = []): PluginClient
    {
        if (!$client instanceof ClientInterface && !$client instanceof HttpAsyncClient) {
            throw new \TypeError(
                sprintf('%s::createClient(): Argument #1 ($client) must be of type %s|%s, %s given', self::class, ClientInterface::class, HttpAsyncClient::class, get_debug_type($client))
            );
        }

        if (static::$factory) {
            $factory = static::$factory;

            return $factory($client, $plugins, $options);
        }

        unset($options['client_name']);

        return new PluginClient($client, $plugins, $options);
    }
}
