<?php

final class CGeo_Provider_GoogleMaps_Model_GoogleAddress extends CGeo_Model_Address {
    /**
     * @var null|string
     */
    private $id;

    /**
     * @var null|string
     */
    private $locationType;

    /**
     * @var array
     */
    private $resultType = [];

    /**
     * @var null|string
     */
    private $formattedAddress;

    /**
     * @var null|string
     */
    private $streetAddress;

    /**
     * @var null|string
     */
    private $intersection;

    /**
     * @var null|string
     */
    private $postalCodeSuffix;

    /**
     * @var null|string
     */
    private $political;

    /**
     * @var null|string
     */
    private $colloquialArea;

    /**
     * @var null|string
     */
    private $ward;

    /**
     * @var null|string
     */
    private $neighborhood;

    /**
     * @var null|string
     */
    private $premise;

    /**
     * @var null|string
     */
    private $subpremise;

    /**
     * @var null|string
     */
    private $naturalFeature;

    /**
     * @var null|string
     */
    private $airport;

    /**
     * @var null|string
     */
    private $park;

    /**
     * @var null|string
     */
    private $pointOfInterest;

    /**
     * @var null|string
     */
    private $establishment;

    /**
     * @var CGeo_Model_AdminLevelCollection
     */
    private $subLocalityLevels;

    /**
     * @var bool
     */
    private $partialMatch;

    /**
     * @param null|string $id
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withId(string $id = null) {
        $new = clone $this;
        $new->id = $id;

        return $new;
    }

    /**
     * @see https://developers.google.com/places/place-id
     *
     * @return null|string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param null|string $locationType
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withLocationType(string $locationType = null) {
        $new = clone $this;
        $new->locationType = $locationType;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getLocationType() {
        return $this->locationType;
    }

    /**
     * @return array
     */
    public function getResultType(): array {
        return $this->resultType;
    }

    /**
     * @param array $resultType
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withResultType(array $resultType) {
        $new = clone $this;
        $new->resultType = $resultType;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getFormattedAddress() {
        return $this->formattedAddress;
    }

    /**
     * @param null|string $formattedAddress
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withFormattedAddress(string $formattedAddress = null) {
        $new = clone $this;
        $new->formattedAddress = $formattedAddress;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getAirport() {
        return $this->airport;
    }

    /**
     * @param null|string $airport
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withAirport(string $airport = null) {
        $new = clone $this;
        $new->airport = $airport;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getColloquialArea() {
        return $this->colloquialArea;
    }

    /**
     * @param null|string $colloquialArea
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withColloquialArea(string $colloquialArea = null) {
        $new = clone $this;
        $new->colloquialArea = $colloquialArea;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getIntersection() {
        return $this->intersection;
    }

    /**
     * @param null|string $intersection
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withIntersection(string $intersection = null) {
        $new = clone $this;
        $new->intersection = $intersection;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getPostalCodeSuffix() {
        return $this->postalCodeSuffix;
    }

    /**
     * @param null|string $postalCodeSuffix
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withPostalCodeSuffix(string $postalCodeSuffix = null) {
        $new = clone $this;
        $new->postalCodeSuffix = $postalCodeSuffix;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getNaturalFeature() {
        return $this->naturalFeature;
    }

    /**
     * @param null|string $naturalFeature
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withNaturalFeature(string $naturalFeature = null) {
        $new = clone $this;
        $new->naturalFeature = $naturalFeature;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getNeighborhood() {
        return $this->neighborhood;
    }

    /**
     * @param null|string $neighborhood
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withNeighborhood(string $neighborhood = null) {
        $new = clone $this;
        $new->neighborhood = $neighborhood;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getPark() {
        return $this->park;
    }

    /**
     * @param null|string $park
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withPark(string $park = null) {
        $new = clone $this;
        $new->park = $park;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getPointOfInterest() {
        return $this->pointOfInterest;
    }

    /**
     * @param null|string $pointOfInterest
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withPointOfInterest(string $pointOfInterest = null) {
        $new = clone $this;
        $new->pointOfInterest = $pointOfInterest;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getPolitical() {
        return $this->political;
    }

    /**
     * @param null|string $political
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withPolitical(string $political = null) {
        $new = clone $this;
        $new->political = $political;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getPremise() {
        return $this->premise;
    }

    /**
     * @param string $premise
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withPremise(string $premise = null) {
        $new = clone $this;
        $new->premise = $premise;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getStreetAddress() {
        return $this->streetAddress;
    }

    /**
     * @param null|string $streetAddress
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withStreetAddress(string $streetAddress = null) {
        $new = clone $this;
        $new->streetAddress = $streetAddress;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getSubpremise() {
        return $this->subpremise;
    }

    /**
     * @param null|string $subpremise
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withSubpremise(string $subpremise = null) {
        $new = clone $this;
        $new->subpremise = $subpremise;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getWard() {
        return $this->ward;
    }

    /**
     * @param null|string $ward
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withWard(string $ward = null) {
        $new = clone $this;
        $new->ward = $ward;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getEstablishment() {
        return $this->establishment;
    }

    /**
     * @param null|string $establishment
     *
     * @return CGeo_Provider_GoogleMaps_Model_GoogleAddress
     */
    public function withEstablishment(string $establishment = null) {
        $new = clone $this;
        $new->establishment = $establishment;

        return $new;
    }

    /**
     * @return CGeo_Model_AdminLevelCollection
     */
    public function getSubLocalityLevels() {
        return $this->subLocalityLevels;
    }

    /**
     * @param array $subLocalityLevel
     *
     * @return $this
     */
    public function withSubLocalityLevels(array $subLocalityLevel) {
        $subLocalityLevels = [];
        foreach ($subLocalityLevel as $level) {
            if (empty($level['level'])) {
                continue;
            }

            $name = $level['name'] ?? $level['code'] ?? '';
            if (empty($name)) {
                continue;
            }

            $subLocalityLevels[] = new CGeo_Model_AdminLevel($level['level'], $name, $level['code'] ?? null);
        }

        $subLocalityLevels = array_unique($subLocalityLevels);

        $new = clone $this;
        $new->subLocalityLevels = new CGeo_Model_AdminLevelCollection($subLocalityLevels);

        return $new;
    }

    /**
     * @return bool
     */
    public function isPartialMatch() {
        return $this->partialMatch;
    }

    /**
     * @param bool $partialMatch
     *
     * @return $this
     */
    public function withPartialMatch(bool $partialMatch) {
        $new = clone $this;
        $new->partialMatch = $partialMatch;

        return $new;
    }
}
