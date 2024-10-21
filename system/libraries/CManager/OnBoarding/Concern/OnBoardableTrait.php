<?php

trait CManager_OnBoarding_Concern_OnBoardableTrait {
    /**
     * @return CManager_OnBoarding_Manager
     */
    public function onboarding(): CManager_OnBoarding_Manager {
        return new CManager_OnBoarding_Manager($this);
    }
}
