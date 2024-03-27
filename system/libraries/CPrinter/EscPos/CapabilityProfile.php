<?php

/**
 * Store compatibility information about one printer.
 */
class CPrinter_EscPos_CapabilityProfile {
    /**
     * @var string
     *             Hash of the code page data structure, to identify it for caching
     */
    protected $codePageCacheKey;

    /**
     * @var array
     *            Associtive array of CodePage objects, indicating which encodings the printer supports
     */
    protected $codePages;

    /**
     * @var array
     *            Not used
     */
    protected $colors;

    /**
     * @var array
     *            Feature values
     */
    protected $features;

    /**
     * @var array
     *            Not used
     */
    protected $fonts;

    /**
     * @var array
     *            Not used
     */
    protected $media;

    /**
     * @var string
     *             Name of the profile, including model number
     */
    protected $name;

    /**
     * @var string
     *             Notes on the profile, null if not set
     */
    protected $notes;

    /**
     * @var string
     *             ID of the profile
     */
    protected $profileId;

    /**
     * @var string
     *             Name of manufacturer
     */
    protected $vendor;

    /**
     * @var array
     *            Data structure containing encodings loaded from disk, null if not loaded yet
     */
    protected static $encodings = null;

    /**
     * @var array
     *            Data structure containing profiles loaded from disk, null if not loaded yet
     */
    protected static $profiles = null;

    /**
     * Construct new CapabilityProfile.
     * The encoding data must be loaded from disk before calling.
     *
     * @param string $profileId
     *                            ID of the profile
     * @param array  $profileData
     *                            Profile data from disk
     */
    protected function __construct(string $profileId, array $profileData) {
        // Basic primitive fields
        $this->profileId = $profileId;
        $this->name = $profileData['name'];
        $this->notes = $profileData['notes'];
        $this->vendor = $profileData['vendor'];
        // More complex fields that are not currently loaded into custom objects
        $this->features = $profileData['features'];
        $this->colors = $profileData['colors'];
        $this->fonts = $profileData['fonts'];
        $this->media = $profileData['media'];
        // More complex fields that are loaded into custom objects
        $this->codePages = [];
        $this->codePageCacheKey = md5(json_encode($profileData['codePages']));
        foreach ($profileData['codePages'] as $k => $v) {
            $this->codePages[$k] = new CPrinter_EscPos_CodePage($v, self::$encodings[$v]);
        }
    }

    /**
     * @return string hash of the code page data structure, to identify it for caching
     */
    public function getCodePageCacheKey() : string {
        return $this->codePageCacheKey;
    }

    /**
     * @return array associative array of CodePage objects, indicating which encodings the printer supports
     */
    public function getCodePages() : array {
        return $this->codePages;
    }

    /**
     * @param string $featureName
     *                            Name of the feature to retrieve
     *
     * @throws \InvalidArgumentException Where the feature does not exist.
     *                                   The exception will contain suggestions for the closest-named features.
     *
     * @return mixed feature value
     */
    public function getFeature($featureName) {
        if (isset($this->features[$featureName])) {
            return $this->features[$featureName];
        }
        $suggestionsArr = $this->suggestFeatureName($featureName);
        $suggestionsStr = implode(', ', $suggestionsArr);
        $str = "The feature '$featureName' does not exist. Try one that does exist, such as $suggestionsStr";

        throw new \InvalidArgumentException($str);
    }

    /**
     * @return string ID of the profile
     */
    public function getId() : string {
        return $this->profileId;
    }

    /**
     * @return string name of the printer
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return bool True if Barcode B command is supported, false otherwise
     */
    public function getSupportsBarcodeB() : bool {
        return $this->getFeature('barcodeB') === true;
    }

    /**
     * @return bool True if Bit Image Raster command is supported, false otherwise
     */
    public function getSupportsBitImageRaster() : bool {
        return $this->getFeature('bitImageRaster') === true;
    }

    /**
     * @return bool True if Graphics command is supported, false otherwise
     */
    public function getSupportsGraphics() : bool {
        return $this->getFeature('graphics') === true;
    }

    /**
     * @return bool True if PDF417 code command is supported, false otherwise
     */
    public function getSupportsPdf417Code() : bool {
        return $this->getFeature('pdf417Code') === true;
    }

    /**
     * @return bool True if QR code command is supported, false otherwise
     */
    public function getSupportsQrCode(): bool {
        return $this->getFeature('qrCode') === true;
    }

    /**
     * @return bool True if Star mode commands are supported, false otherwise
     */
    public function getSupportsStarCommands(): bool {
        return $this->getFeature('starCommands') === true;
    }

    /**
     * @return string vendor of this printer
     */
    public function getVendor() : string {
        return $this->vendor;
    }

    /**
     * @param string $featureName
     *                            Feature that does not exist
     *
     * @return array three most similar feature names that do exist
     */
    protected function suggestFeatureName(string $featureName) : array {
        return self::suggestNearest($featureName, array_keys($this->features), 3);
    }

    /**
     * @return array names of all profiles that exist
     */
    public static function getProfileNames() : array {
        self::loadCapabilitiesDataFile();

        return array_keys(self::$profiles);
    }

    /**
     * Retrieve the CapabilityProfile with the given ID.
     *
     * @param string $profileName
     *                            The ID of the profile to load
     *
     * @throws InvalidArgumentException Where the ID does not exist. Some similarly-named profiles will be suggested in the Exception text.
     *
     * @return CPrinter_RawPrint_CapabilityProfile the CapabilityProfile that was requested
     */
    public static function load(string $profileName) {
        self::loadCapabilitiesDataFile();
        if (!isset(self::$profiles[$profileName])) {
            $suggestionsArray = self::suggestProfileName($profileName);
            $suggestionsStr = implode(', ', $suggestionsArray);

            throw new InvalidArgumentException("The CapabilityProfile '$profileName' does not exist. Try one that does exist, such as $suggestionsStr.");
        }

        return new CPrinter_EscPos_CapabilityProfile($profileName, self::$profiles[$profileName]);
    }

    /**
     * Ensure that the capabilities.json data file has been loaded.
     */
    protected static function loadCapabilitiesDataFile() {
        if (self::$profiles === null) {
            $filename = DOCROOT . 'system/data/printer/escpos-printer-capabilities.json';
            $capabilitiesData = json_decode(file_get_contents($filename), true);
            self::$profiles = $capabilitiesData['profiles'];
            self::$encodings = $capabilitiesData['encodings'];
        }
    }

    /**
     * Return choices with smallest edit distance to an invalid input.
     *
     * @param string $input
     *                        Input that is not a valid choice
     * @param array  $choices
     *                        Array of valid choices
     * @param int    $num
     *                        Number of suggestions to return
     */
    public static function suggestNearest(string $input, array $choices, int $num) : array {
        $distances = array_fill_keys($choices, PHP_INT_MAX);
        foreach ($distances as $word => $_) {
            $distances[$word] = levenshtein($input, $word);
        }
        asort($distances);

        return array_slice(array_keys($distances), 0, min($num, count($choices)));
    }

    /**
     * @param string $profileName
     *                            profile name that does not exist
     *
     * @return array three similar profile names that do exist, plus 'simple' and 'default' for good measure
     */
    protected static function suggestProfileName(string $profileName) : array {
        $suggestions = self::suggestNearest($profileName, array_keys(self::$profiles), 3);
        $alwaysSuggest = [
            'simple',
            'default'
        ];
        foreach ($alwaysSuggest as $item) {
            if (array_search($item, $suggestions) === false) {
                array_push($suggestions, $item);
            }
        }

        return $suggestions;
    }
}
