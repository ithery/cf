<?php

class CManager_OnBoarding_Manager {
    /**
     * @var CCollection<OnboardingStep>
     */
    public $steps;

    public function __construct($model) {
        $this->steps = CManager::onBoarding()->steps($model);
    }

    /**
     * @return CCollection<CManager_Onboarding_Step>
     */
    public function steps(): CCollection {
        return $this->steps;
    }

    public function inProgress(): bool {
        return !$this->finished();
    }

    public function finished(): bool {
        return $this->steps
            ->filter(function (CManager_Onboarding_Step $step) {
                return $step->incomplete();
            })
            ->filter(function (CManager_Onboarding_Step $step) {
                return $step->notExcluded();
            })
            ->isEmpty();
    }

    /**
     * @return null|CManager_Onboarding_Step
     */
    public function nextUnfinishedStep() {
        return $this->steps->first(function (CManager_Onboarding_Step $step) {
            return $step->incomplete();
        });
    }

    public function percentageCompleted(): float {
        $totalCompleteSteps = $this->steps
            ->filter(function (CManager_Onboarding_Step $step) {
                return $step->complete();
            })
            ->count();

        return $totalCompleteSteps / $this->steps->count() * 100;
    }
}
