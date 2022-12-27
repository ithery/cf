<?php

trait CQC_Phpstan_Concern_LoadsAuthModel {
    /**
     * @phpstan-return class-string|null
     */
    private function getAuthModel(?string $guard = null): ?string {
        if (($guard === null && !($guard = CF::config('auth.defaults.guard')))
            || !($provider = CF::config('auth.guards.' . $guard . '.provider'))
            || !($authModel = CF::config('auth.providers.' . $provider . '.model'))
        ) {
            return null;
        }

        return $authModel;
    }
}
