<?php

namespace Faker;

if (!function_exists('trigger_deprecation')) {
    /**
     * Triggers a silenced deprecation notice.
     *
     * @param string $package The name of the Composer package that is triggering the deprecation
     * @param string $version The version of the package that introduced the deprecation
     * @param string $message The message of the deprecation
     * @param mixed  ...$args Values to insert in the message using printf() formatting
     *
     * @author Nicolas Grekas <p@tchwork.com>
     */
    function trigger_deprecation($package, $version, $message, ...$args) {
        @trigger_error(($package || $version ? "Since ${package} ${version}: " : '') . ($args ? vsprintf($message, $args) : $message), \E_USER_DEPRECATED);
    }
}
class Factory {
    public const DEFAULT_LOCALE = 'en_US';

    protected static $defaultProviders = ['Address', 'Barcode', 'Biased', 'Color', 'Company', 'DateTime', 'File', 'HtmlLorem', 'Image', 'Internet', 'Lorem', 'Medical', 'Miscellaneous', 'Payment', 'Person', 'PhoneNumber', 'Text', 'UserAgent', 'Uuid'];

    /**
     * Create a new generator.
     *
     * @param string $locale
     *
     * @return Generator
     */
    public static function create($locale = self::DEFAULT_LOCALE) {
        $generator = new Generator();

        foreach (static::$defaultProviders as $provider) {
            $providerClassName = self::getProviderClassname($provider, $locale);
            $generator->addProvider(new $providerClassName($generator));
        }

        return $generator;
    }

    /**
     * @param string $provider
     * @param string $locale
     *
     * @return string
     */
    protected static function getProviderClassname($provider, $locale = '') {
        if ($providerClass = self::findProviderClassname($provider, $locale)) {
            return $providerClass;
        }
        // fallback to default locale
        if ($providerClass = self::findProviderClassname($provider, static::DEFAULT_LOCALE)) {
            return $providerClass;
        }
        // fallback to no locale
        if ($providerClass = self::findProviderClassname($provider)) {
            return $providerClass;
        }

        throw new \InvalidArgumentException(sprintf('Unable to find provider "%s" with locale "%s"', $provider, $locale));
    }

    /**
     * @param string $provider
     * @param string $locale
     *
     * @return null|string
     */
    protected static function findProviderClassname($provider, $locale = '') {
        $providerClass = 'Faker\\' . ($locale ? sprintf('Provider\%s\%s', $locale, $provider) : sprintf('Provider\%s', $provider));

        if (class_exists($providerClass, true)) {
            return $providerClass;
        }

        return null;
    }
}
