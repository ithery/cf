<?php

/*
 * This file is a part of the DiscordPHP project.
 *
 * Copyright (c) 2015-present David Cole <david.cole1340@gmail.com>
 *
 * This file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Discord\Repository;

use Discord\Http\Http;
use Discord\Parts\Part;
use Discord\Http\Endpoint;
use Discord\Factory\Factory;
use Discord\Helpers\Collection;
use React\Promise\PromiseInterface;

/**
 * Repositories provide a way to store and update parts on the Discord server.
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 * @author David Cole <david.cole1340@gmail.com>
 */
abstract class AbstractRepository extends Collection {
    /**
     * The discriminator.
     *
     * @var string discriminator
     */
    protected $discrim = 'id';

    /**
     * The HTTP client.
     *
     * @var Http client
     */
    protected $http;

    /**
     * The parts factory.
     *
     * @var Factory parts factory
     */
    protected $factory;

    /**
     * Endpoints for interacting with the Discord servers.
     *
     * @var array endpoints
     */
    protected $endpoints = [];

    /**
     * Variables that are related to the repository.
     *
     * @var array variables
     */
    protected $vars = [];

    /**
     * AbstractRepository constructor.
     *
     * @param Http    $http    the HTTP client
     * @param Factory $factory the parts factory
     * @param array   $vars    an array of variables used for the endpoint
     */
    public function __construct(Http $http, Factory $factory, array $vars = []) {
        $this->http = $http;
        $this->factory = $factory;
        $this->vars = $vars;

        parent::__construct([], $this->discrim, $this->class);
    }

    /**
     * Freshens the repository collection.
     *
     * @throws \Exception
     *
     * @return PromiseInterface
     */
    public function freshen(): PromiseInterface {
        if (!isset($this->endpoints['all'])) {
            return \React\Promise\reject(new \Exception('You cannot freshen this repository.'));
        }

        $endpoint = new Endpoint($this->endpoints['all']);
        $endpoint->bindAssoc($this->vars);

        return $this->http->get($endpoint)->then(function ($response) {
            $this->fill([]);

            foreach ($response as $value) {
                $value = array_merge($this->vars, (array) $value);
                $part = $this->factory->create($this->class, $value, true);

                $this->push($part);
            }

            return $this;
        });
    }

    /**
     * Builds a new, empty part.
     *
     * @param array $attributes the attributes for the new part
     * @param bool  $created
     *
     * @throws \Exception
     *
     * @return Part the new part
     */
    public function create(array $attributes = [], bool $created = false): Part {
        $attributes = array_merge($attributes, $this->vars);

        return $this->factory->create($this->class, $attributes, $created);
    }

    /**
     * Attempts to save a part to the Discord servers.
     *
     * @param Part $part the part to save
     *
     * @throws \Exception
     *
     * @return PromiseInterface
     */
    public function save(Part $part): PromiseInterface {
        if ($part->created) {
            if (!isset($this->endpoints['update'])) {
                return \React\Promise\reject(new \Exception('You cannot update this part.'));
            }

            $method = 'patch';
            $endpoint = new Endpoint($this->endpoints['update']);
            $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));
            $attributes = $part->getUpdatableAttributes();
        } else {
            if (!isset($this->endpoints['create'])) {
                return \React\Promise\reject(new \Exception('You cannot create this part.'));
            }

            $method = 'post';
            $endpoint = new Endpoint($this->endpoints['create']);
            $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));
            $attributes = $part->getCreatableAttributes();
        }

        return $this->http->{$method}($endpoint, $attributes)->then(function ($response) use (&$part) {
            $part->fill((array) $response);
            $part->created = true;
            $part->deleted = false;

            $this->push($part);

            return $part;
        });
    }

    /**
     * Attempts to delete a part on the Discord servers.
     *
     * @param Part|snowflake $part the part to delete
     *
     * @throws \Exception
     *
     * @return PromiseInterface
     */
    public function delete($part): PromiseInterface {
        if (!($part instanceof Part)) {
            $part = $this->factory->part($this->class, [$this->discrim => $part], true);
        }

        if (!$part->created) {
            return \React\Promise\reject(new \Exception('You cannot delete a non-existant part.'));
        }

        if (!isset($this->endpoints['delete'])) {
            return \React\Promise\reject(new \Exception('You cannot delete this part.'));
        }

        $endpoint = new Endpoint($this->endpoints['delete']);
        $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));

        return $this->http->delete($endpoint)->then(function ($response) use (&$part) {
            $part->created = false;

            return $part;
        });
    }

    /**
     * Returns a part with fresh values.
     *
     * @param Part $part the part to get fresh values
     *
     * @throws \Exception
     *
     * @return PromiseInterface
     */
    public function fresh(Part $part): PromiseInterface {
        if (!$part->created) {
            return \React\Promise\reject(new \Exception('You cannot get a non-existant part.'));
        }

        if (!isset($this->endpoints['get'])) {
            return \React\Promise\reject(new \Exception('You cannot get this part.'));
        }

        $endpoint = new Endpoint($this->endpoints['get']);
        $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));

        return $this->http->get($endpoint)->then(function ($response) use (&$part) {
            $part->fill((array) $response);

            return $part;
        });
    }

    /**
     * Gets a part from the repository or Discord servers.
     *
     * @param string $id    the ID to search for
     * @param bool   $fresh whether we should skip checking the cache
     *
     * @throws \Exception
     *
     * @return PromiseInterface
     */
    public function fetch(string $id, bool $fresh = false): PromiseInterface {
        if (!$fresh && $part = $this->get($this->discrim, $id)) {
            return \React\Promise\resolve($part);
        }

        if (!isset($this->endpoints['get'])) {
            return \React\Promise\resolve(new \Exception('You cannot get this part.'));
        }

        $part = $this->factory->create($this->class, [$this->discrim => $id]);
        $endpoint = new Endpoint($this->endpoints['get']);
        $endpoint->bindAssoc(array_merge($part->getRepositoryAttributes(), $this->vars));

        return $this->http->get($endpoint)->then(function ($response) {
            $part = $this->factory->create($this->class, array_merge($this->vars, (array) $response), true);
            $this->push($part);

            return $part;
        });
    }

    /**
     * Handles debug calls from var_dump and similar functions.
     *
     * @return array an array of attributes
     */
    public function __debugInfo(): array {
        return $this->jsonSerialize();
    }
}
