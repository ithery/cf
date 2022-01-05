<?php
use PhpOption\Option;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\Adapter\PutenvAdapter;

class CEnv_Adapter_DotEnvAdapter implements CEnv_AdapterInterface {
    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool
     */
    protected $putenv = true;

    /**
     * The environment repository instance.
     *
     * @var null|\Dotenv\Repository\RepositoryInterface
     */
    protected $repository;

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public function enablePutenv() {
        $this->putenv = true;
        $this->repository = null;
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public function disablePutenv() {
        $this->putenv = false;
        $this->repository = null;
    }

    /**
     * Get the environment repository instance.
     *
     * @return \Dotenv\Repository\RepositoryInterface
     */
    public function getRepository() {
        if ($this->repository === null) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();

            if ($this->putenv) {
                $builder = $builder->addAdapter(PutenvAdapter::class);
            }

            $this->repository = $builder->immutable()->make();
        }

        return $this->repository;
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null) {
        return Option::fromValue($this->getRepository()->get($key))
            ->map(function ($value) {
                switch (strtolower($value)) {
                    case 'true':
                    case '(true)':
                        return true;
                    case 'false':
                    case '(false)':
                        return false;
                    case 'empty':
                    case '(empty)':
                        return '';
                    case 'null':
                    case '(null)':
                        return;
                }

                if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                    return $matches[2];
                }

                return $value;
            })
            ->getOrCall(function () use ($default) {
                return c::value($default);
            });
    }
}
