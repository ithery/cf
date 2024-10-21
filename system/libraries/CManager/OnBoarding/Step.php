<?php

class CManager_OnBoarding_Step implements CInterface_Arrayable {
    protected array $attributes = [];

    /**
     * @var null|callable
     */
    protected $callableAttributes;

    /**
     * @var null|callable
     */
    protected $excludeIf;

    /**
     * @var null|callable
     */
    protected $completeIf;

    /**
     * @var null|CManager_OnBoarding_Contract_OnBoardableInterface
     */
    protected $model;

    public function __construct(string $title) {
        $this->attributes(['title' => $title]);
    }

    public function cta(string $cta): self {
        $this->attributes(['cta' => $cta]);

        return $this;
    }

    public function link(string $link): self {
        $this->attributes(['link' => $link]);

        return $this;
    }

    public function excludeIf(callable $callback): self {
        $this->excludeIf = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function completeIf($callback): self {
        $this->completeIf = $callback;

        return $this;
    }

    public function setModel(CManager_OnBoarding_Contract_OnBoardableInterface $model): self {
        $this->model = $model;

        return $this;
    }

    public function setCallableAttributes(): void {
        if (is_null($this->callableAttributes)) {
            return;
        }

        $this->attributes(c::once(function () {
            c::container()->call($this->callableAttributes, ['model' => $this->model]);
        }));
    }

    public function initiate(CManager_OnBoarding_Contract_OnBoardableInterface $model): self {
        $this->setModel($model);

        $this->setCallableAttributes();

        return $this;
    }

    public function excluded(): bool {
        if ($this->excludeIf && $this->model) {
            return c::once(function () {
                c::container()->call($this->excludeIf, ['model' => $this->model]);
            });
        }

        return false;
    }

    public function notExcluded(): bool {
        return !$this->excluded();
    }

    public function complete(): bool {
        if ($this->completeIf && $this->model) {
            return c::once(function () {
                return c::container()->call($this->completeIf, ['model' => $this->model]);
            });
        }

        return false;
    }

    public function incomplete(): bool {
        return !$this->complete();
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function attribute(string $key, $default = null) {
        return carr::get($this->attributes, $key, $default);
    }

    /**
     * @param array|callable $attributes
     *
     * @return self
     */
    public function attributes($attributes): self {
        if (is_callable($attributes)) {
            $this->callableAttributes = $attributes;
        }

        if (is_array($attributes)) {
            $this->attributes = array_merge($this->attributes, $attributes);
        }

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key) {
        return $this->attribute($key);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $key, $value): void {
        $this->attributes[$key] = $value;
    }

    public function __isset(string $key): bool {
        return isset($this->attributes[$key]);
    }

    public function toArray() {
        return array_merge($this->attributes, [
            'complete' => $this->complete(),
            'excluded' => $this->excluded(),
        ]);
    }
}
