<?php

final class CGeo_Provider_Nominatim_Model_NominatimAddress extends CGeo_Model_Address {
    /**
     * @var null|string
     */
    private $attribution;

    /**
     * @var null|string
     */
    private $category;

    /**
     * @var null|string
     */
    private $displayName;

    /**
     * @var null|string
     */
    private $quarter;

    /**
     * @var null|string
     */
    private $osmType;

    /**
     * @var null|int
     */
    private $osmId;

    /**
     * @var null|string
     */
    private $type;

    /**
     * @var null|array
     */
    private $details;

    /**
     * @var null|array
     */
    private $tags;

    /**
     * @return null|string
     */
    public function getAttribution() {
        return $this->attribution;
    }

    /**
     * @param null|string $attribution
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withAttribution(string $attribution = null): self {
        $new = clone $this;
        $new->attribution = $attribution;

        return $new;
    }

    /**
     * @deprecated
     *
     * @return null|string
     */
    public function getClass() {
        return $this->getCategory();
    }

    /**
     * @param null|string $category
     *
     * @deprecated
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withClass(string $category = null) {
        return $this->withCategory($category);
    }

    /**
     * @return null|string
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param null|string $category
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withCategory(string $category = null): self {
        $new = clone $this;
        $new->category = $category;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getDisplayName() {
        return $this->displayName;
    }

    /**
     * @param null|string $displayName
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withDisplayName(string $displayName = null): self {
        $new = clone $this;
        $new->displayName = $displayName;

        return $new;
    }

    /**
     * @return null|int
     */
    public function getOSMId() {
        return $this->osmId;
    }

    /**
     * @param null|int $osmId
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withOSMId(int $osmId = null): self {
        $new = clone $this;
        $new->osmId = $osmId;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getOSMType() {
        return $this->osmType;
    }

    /**
     * @param null|string $osmType
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withOSMType(string $osmType = null) {
        $new = clone $this;
        $new->osmType = $osmType;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param null|string $type
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withType(string $type = null) {
        $new = clone $this;
        $new->type = $type;

        return $new;
    }

    /**
     * @return null|string
     */
    public function getQuarter(): ?string {
        return $this->quarter;
    }

    /**
     * @param null|string $quarter
     *
     * @return CGeo_Provider_Nominatim_Model_NominatimAddress
     */
    public function withQuarter(string $quarter = null): self {
        $new = clone $this;
        $new->quarter = $quarter;

        return $new;
    }

    /**
     * @return null|array
     */
    public function getDetails(): ?array {
        return $this->details;
    }

    /**
     * @param null|array $details
     */
    public function withDetails(array $details = null): self {
        $new = clone $this;
        $new->details = $details;

        return $new;
    }

    /**
     * @return null|array
     */
    public function getTags(): ?array {
        return $this->tags;
    }

    /**
     * @param null|array $tags
     */
    public function withTags(array $tags = null): self {
        $new = clone $this;
        $new->tags = $tags;

        return $new;
    }
}
