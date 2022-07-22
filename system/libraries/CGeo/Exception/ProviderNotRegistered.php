<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:02:45 PM
 */
final class CGeo_Exception_ProviderNotRegistered extends \RuntimeException implements CGeo_Interface_ExceptionInterface {
    /**
     * @param string $providerName
     * @param array  $registeredProviders
     */
    public static function create($providerName, array $registeredProviders = []) {
        return new self(sprintf(
            'Provider "%s" is not registered, so you cannot use it. Did you forget to register it or made a typo?%s',
            $providerName,
            0 == count($registeredProviders) ? '' : sprintf(' Registered providers are: %s.', implode(', ', $registeredProviders))
        ));
    }

    public static function noProviderRegistered() {
        return new self('No provider registered.');
    }
}
