<?php

interface CManager_OnBoarding_Contract_OnBoardableInterface {
    /**
     * @return CManager_OnBoarding_Manager
     */
    public function onboarding(): CManager_OnBoarding_Manager;
}
