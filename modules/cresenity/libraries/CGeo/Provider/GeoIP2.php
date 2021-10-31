<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:46:29 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Exception\AuthenticationException;
use GeoIp2\Exception\OutOfQueriesException;

final class CGeo_Provider_GeoIP2 extends CGeo_Provider {

    /**
     * @var CGeo_Provider_GeoIP2_Adapter
     */
    private $adapter;

    public function __construct(CGeo_Provider_GeoIP2_Adapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query) {
        $address = $query->getText();
        $locale = $query->getLocale() ?: 'en'; // Default to English
        if (!filter_var($address, FILTER_VALIDATE_IP)) {
            throw new CGeo_Exception_UnsupportedOperation('The GeoIP2 provider does not support street addresses, only IP addresses.');
        }
        if ('127.0.0.1' === $address) {
            return new CGeo_Model_AddressCollection([$this->getLocationForLocalhost()]);
        }
        $result = json_decode($this->executeQuery($address));
        if (null === $result) {
            return new CGeo_Model_AddressCollection([]);
        }
        $adminLevels = [];
        if (isset($result->subdivisions) && is_array($result->subdivisions)) {
            foreach ($result->subdivisions as $i => $subdivision) {
                $name = (isset($subdivision->names->{$locale}) ? $subdivision->names->{$locale} : null);
                $code = (isset($subdivision->iso_code) ? $subdivision->iso_code : null);
                if (null !== $name || null !== $code) {
                    $adminLevels[] = ['name' => $name, 'code' => $code, 'level' => $i + 1];
                }
            }
        }
        return new CGeo_Model_AddressCollection([
            CGeo_Model_Address::createFromArray([
                'providedBy' => $this->getName(),
                'countryCode' => (isset($result->country->iso_code) ? $result->country->iso_code : null),
                'country' => (isset($result->country->names->{$locale}) ? $result->country->names->{$locale} : null),
                'locality' => (isset($result->city->names->{$locale}) ? $result->city->names->{$locale} : null),
                'latitude' => (isset($result->location->latitude) ? $result->location->latitude : null),
                'longitude' => (isset($result->location->longitude) ? $result->location->longitude : null),
                'timezone' => (isset($result->location->time_zone) ? $result->location->time_zone : null),
                'postalCode' => (isset($result->postal->code) ? $result->postal->code : null),
                'adminLevels' => $adminLevels,
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query) {
        throw new CGeo_Exception_UnsupportedOperation('The GeoIP2 provider is not able to do reverse geocoding.');
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'geoip2';
    }

    /**
     * @param string $address
     */
    private function executeQuery($address) {
        $uri = sprintf('file://geoip?%s', $address);
        try {
            $result = $this->adapter->getContent($uri);
        } catch (AddressNotFoundException $e) {
            return '';
        } catch (AuthenticationException $e) {
            throw new InvalidCredentials(
            $e->getMessage(), $e->getCode(), $e
            );
        } catch (OutOfQueriesException $e) {
            throw new QuotaExceeded(
            $e->getMessage(), $e->getCode(), $e
            );
        }
        return $result;
    }

}
