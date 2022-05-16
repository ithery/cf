<?php

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\UriInterface;

final class CVendor_Firebase_Auth_ActionCodeSettings_ValidatedActionCodeSettings implements CVendor_Firebase_Auth_ActionCodeSettingsInterface {
    /**
     * @var null|UriInterface
     */
    private $continueUrl = null;

    /**
     * @var null|bool
     */
    private $canHandleCodeInApp = null;

    /**
     * @var null|UriInterface
     */
    private $dynamicLinkDomain = null;

    /**
     * @var null|string
     */
    private $androidPackageName = null;

    /**
     * @var null|string
     */
    private $androidMinimumVersion = null;

    /**
     * @var null|bool
     */
    private $androidInstallApp = null;

    /**
     * @var null|string
     */
    private $iOSBundleId = null;

    private function __construct() {
    }

    public static function empty(): self {
        return new self();
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @return self
     */
    public static function fromArray(array $settings) {
        $instance = new self();

        $settings = \array_filter($settings, static fn ($value) => $value !== null);

        foreach ($settings as $key => $value) {
            switch (\mb_strtolower($key)) {
                case 'continueurl':
                case 'url':
                    $instance->continueUrl = Utils::uriFor($value);

                    break;
                case 'handlecodeinapp':
                    $instance->canHandleCodeInApp = (bool) $value;

                    break;
                case 'dynamiclinkdomain':
                    $instance->dynamicLinkDomain = Utils::uriFor($value);

                    break;
                case 'androidpackagename':
                    $instance->androidPackageName = (string) $value;

                    break;
                case 'androidminimumversion':
                    $instance->androidMinimumVersion = (string) $value;

                    break;
                case 'androidinstallapp':
                    $instance->androidInstallApp = (bool) $value;

                    break;
                case 'iosbundleid':
                    $instance->iOSBundleId = (string) $value;

                    break;
                default:
                    throw new InvalidArgumentException("Unsupported action code setting '{$key}'");
            }
        }

        return $instance;
    }

    /**
     * @return array<string, bool|string>
     */
    public function toArray() {
        $continueUrl = $this->continueUrl !== null ? (string) $this->continueUrl : null;
        $dynamicLinkDomain = $this->dynamicLinkDomain !== null ? (string) $this->dynamicLinkDomain : null;

        return \array_filter([
            'continueUrl' => $continueUrl,
            'canHandleCodeInApp' => $this->canHandleCodeInApp,
            'dynamicLinkDomain' => $dynamicLinkDomain,
            'androidPackageName' => $this->androidPackageName,
            'androidMinimumVersion' => $this->androidMinimumVersion,
            'androidInstallApp' => $this->androidInstallApp,
            'iOSBundleId' => $this->iOSBundleId,
        ], static fn ($value) => $value !== null);
    }
}
