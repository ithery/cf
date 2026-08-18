<?php

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class CSession_SymfonySessionDecorator implements SessionInterface {
    /**
     * The underlying Laravel session store.
     *
     * @var \CSession_Store
     */
    protected $store;

    /**
     * Create a new session decorator.
     *
     * @param \CSession_Contract_SessionInterface $store
     *
     * @return void
     */
    public function __construct(CSession_Contract_SessionInterface $store) {
        $this->store = $store;
    }

    /**
     * @inheritdoc
     */
    public function start(): bool {
        return $this->store->start();
    }

    /**
     * @inheritdoc
     */
    public function getId(): string {
        return $this->store->getId();
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function setId($id) {
        $this->store->setId($id);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return $this->store->getName();
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function setName($name) {
        $this->store->setName($name);
    }

    /**
     * @inheritdoc
     */
    public function invalidate($lifetime = null) {
        $this->store->invalidate();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function migrate($destroy = false, $lifetime = null) {
        $this->store->migrate($destroy);

        return true;
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function save() {
        $this->store->save();
    }

    /**
     * @inheritdoc
     */
    public function has($name) {
        return $this->store->has($name);
    }

    /**
     * @inheritdoc
     */
    public function get($name, $default = null) {
        return $this->store->get($name, $default);
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function set($name, $value) {
        $this->store->put($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function all() {
        return $this->store->all();
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function replace(array $attributes) {
        $this->store->replace($attributes);
    }

    /**
     * @inheritdoc
     */
    public function remove($name) {
        return $this->store->remove($name);
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function clear() {
        $this->store->flush();
    }

    /**
     * @inheritdoc
     */
    public function isStarted() {
        return $this->store->isStarted();
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function registerBag(SessionBagInterface $bag) {
        throw new BadMethodCallException('Method not implemented by CF.');
    }

    /**
     * @inheritdoc
     */
    public function getBag($name) {
        throw new BadMethodCallException('Method not implemented by CF.');
    }

    /**
     * Gets session meta.
     *
     * @return MetadataBag
     */
    public function getMetadataBag() {
        throw new BadMethodCallException('Method not implemented by CF.');
    }
}
