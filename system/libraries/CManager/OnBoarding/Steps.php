<?php

class CManager_OnBoarding_Steps {
    /**
     * @var array<CManager_OnBoarding_Step>
     */
    protected array $steps = [];

    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string      $title
     * @param null|string $model
     *
     * @return CManager_OnBoarding_Step
     */
    public function addStep(string $title, string $model = null): CManager_OnBoarding_Step {
        $step = new CManager_OnBoarding_Step($title);

        if ($model && new $model() instanceof CManager_OnBoarding_Contract_OnBoardableInterface) {
            return $this->steps[$model][] = $step;
        }

        return $this->steps['default'][] = $step;
    }

    public function steps(CManager_OnBoarding_Contract_OnBoardableInterface $model): CCollection {
        return c::collect($this->getStepsArray($model))
            ->map(function (CManager_OnBoarding_Step $step) use ($model) {
                return $step->initiate($model);
            })
            ->filter(function (CManager_OnBoarding_Step $step) {
                return $step->notExcluded();
            });
    }

    private function getStepsArray(CManager_OnBoarding_Contract_OnBoardableInterface $model): array {
        $key = get_class($model);

        if (key_exists($key, $this->steps)) {
            return array_merge(
                $this->steps[$key],
                $this->steps['default'] ?? []
            );
        }

        return $this->steps['default'] ?? [];
    }
}
