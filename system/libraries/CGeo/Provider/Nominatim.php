<?php

use Http\Client\HttpClient;

final class CGeo_Provider_Nominatim extends CGeo_ProviderHttpAbstract implements CGeo_Interface_ProviderInterface {
    /**
     * @var string
     */
    private $rootUrl;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $referer;

    /**
     * @param HttpClient $client    an HTTP client
     * @param string     $userAgent Value of the User-Agent header
     * @param string     $referer   Value of the Referer header
     *
     * @return Nominatim
     */
    public static function withOpenStreetMapServer(HttpClient $client, $userAgent, $referer = ''): self {
        return new self($client, 'https://nominatim.openstreetmap.org', $userAgent, '', $referer);
    }

    /**
     * @param HttpClient $client    an HTTP client
     * @param string     $rootUrl   Root URL of the nominatim server
     * @param string     $userAgent Value of the User-Agent header
     * @param string     $extension Value of extension nominatim path
     * @param string     $referer   Value of the Referer header
     */
    public function __construct(HttpClient $client, $rootUrl, $userAgent, $extension = '', $referer = '') {
        parent::__construct($client);

        $this->rootUrl = rtrim($rootUrl, '/');
        $this->userAgent = $userAgent;
        $this->referer = $referer;
        $this->extension = $extension;

        if (empty($this->userAgent)) {
            throw new CGeo_Exception_InvalidArgument('The User-Agent must be set to use the Nominatim provider.');
        }
    }

    protected function getExtension() {
        $extension = $this->extension;
        if ($extension && !cstr::startsWith($extension, '.')) {
            $extension = '.' . $extension;
        }

        return $extension;
    }

    /**
     * @inheritdoc
     */
    public function geocodeQuery(CGeo_Query_GeocodeQuery $query) {
        $address = $query->getText();
        // This API doesn't handle IPs
        if (filter_var($address, FILTER_VALIDATE_IP)) {
            throw new CGeo_Exception_UnsupportedOperation('The Nominatim provider does not support IP addresses.');
        }
        $queries = [
            'format' => 'jsonv2',
            'q' => $address,
            'addressdetails' => 1,
            'extratags' => 1,
            'limit' => $query->getLimit(),
        ];
        $key = $query->getData('key');
        if ($key) {
            $queries['key'] = $key;
        }

        $url = $this->rootUrl
            . '/search' . $this->getExtension() . '?'
            . http_build_query($queries, '', '&', PHP_QUERY_RFC3986);

        $countrycodes = $query->getData('countrycodes');
        if (!is_null($countrycodes)) {
            if (is_array($countrycodes)) {
                $countrycodes = array_map('strtolower', $countrycodes);

                $url .= '&' . http_build_query([
                    'countrycodes' => implode(',', $countrycodes),
                ], '', '&', PHP_QUERY_RFC3986);
            } else {
                $url .= '&' . http_build_query([
                    'countrycodes' => strtolower($countrycodes),
                ], '', '&', PHP_QUERY_RFC3986);
            }
        }

        $viewbox = $query->getData('viewbox');
        if (!is_null($viewbox) && is_array($viewbox) && 4 === count($viewbox)) {
            $url .= '&' . http_build_query([
                'viewbox' => implode(',', $viewbox),
            ], '', '&', PHP_QUERY_RFC3986);

            $bounded = $query->getData('bounded');
            if (!is_null($bounded) && true === $bounded) {
                $url .= '&' . http_build_query([
                    'bounded' => 1,
                ], '', '&', PHP_QUERY_RFC3986);
            }
        }

        $content = $this->executeQuery($url, $query->getLocale());

        $json = json_decode($content);
        if (is_null($json) || !is_array($json)) {
            throw CGeo_Exception_InvalidServerResponse::create($url);
        }

        if (empty($json)) {
            return new CGeo_Model_AddressCollection([]);
        }

        $results = [];
        foreach ($json as $place) {
            $results[] = $this->jsonResultToLocation($place, false);
        }

        return new CGeo_Model_AddressCollection($results);
    }

    /**
     * @inheritdoc
     */
    public function reverseQuery(CGeo_Query_ReverseQuery $query) {
        $coordinates = $query->getCoordinates();
        $longitude = $coordinates->getLongitude();
        $latitude = $coordinates->getLatitude();

        $queries = [
            'format' => 'jsonv2',
            'lat' => $latitude,
            'lon' => $longitude,
            'addressdetails' => 1,
            'zoom' => $query->getData('zoom', 18),
        ];
        $key = $query->getData('key');

        if ($key) {
            $queries['key'] = $key;
        }
        $url = $this->rootUrl
            . '/reverse' . $this->getExtension() . '?'
            . http_build_query($queries, '', '&', PHP_QUERY_RFC3986);

        $content = $this->executeQuery($url, $query->getLocale());
        $json = json_decode($content);
        if (is_null($json) || isset($json->error)) {
            return new CGeo_Model_AddressCollection([]);
        }

        if (empty($json)) {
            return new CGeo_Model_AddressCollection([]);
        }

        return new CGeo_Model_AddressCollection([$this->jsonResultToLocation($json, true)]);
    }

    /**
     * @param \stdClass $place
     * @param bool      $reverse
     *
     * @return CGeo_Model_Location
     */
    private function jsonResultToLocation($place, bool $reverse) {
        $builder = new CGeo_Model_AddressBuilder($this->getName());

        foreach (['state', 'county'] as $i => $tagName) {
            if (isset($place->address->{$tagName})) {
                $builder->addAdminLevel($i + 1, $place->address->{$tagName}, '');
            }
        }

        // get the first postal-code when there are many
        if (isset($place->address->postcode)) {
            $postalCode = $place->address->postcode;
            if (!empty($postalCode)) {
                $postalCode = current(explode(';', $postalCode));
            }
            $builder->setPostalCode($postalCode);
        }

        $localityFields = ['city', 'town', 'village', 'hamlet'];
        foreach ($localityFields as $localityField) {
            if (isset($place->address->{$localityField})) {
                $localityFieldContent = $place->address->{$localityField};

                if (!empty($localityFieldContent)) {
                    $builder->setLocality($localityFieldContent);

                    break;
                }
            }
        }

        $builder->setStreetName($place->address->road ?? $place->address->pedestrian ?? null);
        $builder->setStreetNumber($place->address->house_number ?? null);
        $builder->setSubLocality($place->address->suburb ?? null);
        $builder->setCountry($place->address->country ?? null);
        $builder->setCountryCode(isset($place->address->country_code) ? strtoupper($place->address->country_code) : null);

        $builder->setCoordinates(floatval($place->lat), floatval($place->lon));

        $builder->setBounds($place->boundingbox[0], $place->boundingbox[2], $place->boundingbox[1], $place->boundingbox[3]);

        /** @var CGeo_Provider_Nominatim_Model_NominatimAddress $location */
        $location = $builder->build(CGeo_Provider_Nominatim_Model_NominatimAddress::class);
        $location = $location->withAttribution($place->licence);
        $location = $location->withDisplayName($place->display_name);

        $includedAddressKeys = ['city', 'town', 'village', 'state', 'county', 'hamlet', 'postcode', 'road', 'pedestrian', 'house_number', 'suburb', 'country', 'country_code', 'quarter'];

        $location = $location->withDetails(array_diff_key((array) $place->address, array_flip($includedAddressKeys)));

        if (isset($place->extratags)) {
            $location = $location->withTags((array) $place->extratags);
        }
        if (isset($place->address->quarter)) {
            $location = $location->withQuarter($place->address->quarter);
        }
        if (isset($place->osm_id)) {
            $location = $location->withOSMId(intval($place->osm_id));
        }
        if (isset($place->osm_type)) {
            $location = $location->withOSMType($place->osm_type);
        }

        if (false === $reverse) {
            $location = $location->withCategory($place->category);
            $location = $location->withType($place->type);
        }

        return $location;
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'nominatim';
    }

    /**
     * @param string      $url
     * @param null|string $locale
     *
     * @return string
     */
    private function executeQuery($url, $locale = null) {
        if (null !== $locale) {
            $url .= '&' . http_build_query([
                'accept-language' => $locale,
            ], '', '&', PHP_QUERY_RFC3986);
        }

        $request = $this->getRequest($url);
        $request = $request->withHeader('User-Agent', $this->userAgent);

        if (!empty($this->referer)) {
            $request = $request->withHeader('Referer', $this->referer);
        }

        return $this->getParsedResponse($request);
    }
}
