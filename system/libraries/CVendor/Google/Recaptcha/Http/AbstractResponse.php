<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

abstract class CVendor_Google_Recaptcha_Http_AbstractResponse implements Arrayable, Jsonable, JsonSerializable {
    /* -----------------------------------------------------------------
      |  Constants
      | -----------------------------------------------------------------
     */

    /**
     * Did not receive a 200 from the service.
     */
    const E_BAD_RESPONSE = 'bad-response';

    /**
     * ReCAPTCHA response not provided.
     */
    const E_MISSING_INPUT_RESPONSE = 'missing-input-response';

    /* -----------------------------------------------------------------
      |  Properties
      | -----------------------------------------------------------------
     */

    /**
     * Success or failure.
     *
     * @var bool
     */
    protected $success = false;

    /**
     * Error code strings.
     *
     * @var array
     */
    protected $errorCodes = [];

    /**
     * The hostname of the site where the reCAPTCHA was solved.
     *
     * @var string
     */
    protected $hostname;

    /**
     * Timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ).
     *
     * @var string
     */
    protected $challengeTs;

    /**
     * APK package name.
     *
     * @var string
     */
    protected $apkPackageName;

    /* -----------------------------------------------------------------
      |  Constructor
      | -----------------------------------------------------------------
     */

    /**
     * Response constructor.
     *
     * @param bool        $success
     * @param array       $errorCodes
     * @param null|string $hostname
     * @param null|string $challengeTs
     * @param null|string $apkPackageName
     * @param null|float  $score
     * @param null|string $action
     */
    public function __construct($success, array $errorCodes = [], $hostname = null, $challengeTs = null, $apkPackageName = null, $score = null, $action = null) {
        $this->success = $success;
        $this->errorCodes = $errorCodes;
        $this->hostname = $hostname;
        $this->challengeTs = $challengeTs;
        $this->apkPackageName = $apkPackageName;
    }

    /* -----------------------------------------------------------------
      |  Getters
      | -----------------------------------------------------------------
     */

    /**
     * Get error codes.
     *
     * @return array
     */
    public function getErrorCodes() {
        return $this->errorCodes;
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    public function getHostname() {
        return $this->hostname;
    }

    /**
     * Get challenge timestamp.
     *
     * @return string
     */
    public function getChallengeTs() {
        return $this->challengeTs;
    }

    /**
     * Get APK package name.
     *
     * @return string
     */
    public function getApkPackageName() {
        return $this->apkPackageName;
    }

    /* -----------------------------------------------------------------
      |  Main Methods
      | -----------------------------------------------------------------
     */

    /**
     * Build the response from the expected JSON returned by the service.
     *
     * @param string $json
     *
     * @return Google_Recaptcha_Http_AbstractResponse
     */
    public static function fromJson($json) {
        $responseData = json_decode($json, true);

        if (!$responseData) {
            return new static(false, [Google_Recaptcha_Http_ResponseV3::E_INVALID_JSON]);
        }

        return static::fromArray($responseData);
    }

    /**
     * Build the response from an array.
     *
     * @param array $array
     *
     * @return Google_Recaptcha_Http_AbstractResponse|mixed
     */
    abstract public static function fromArray(array $array);

    /**
     * Convert the response object to array.
     *
     * @return array
     */
    abstract public function toArray();

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the response object to array.
     *
     * @return array
     */
    public function jsonSerialize() {
        return $this->toArray();
    }

    /* -----------------------------------------------------------------
      |  Check Methods
      | -----------------------------------------------------------------
     */

    /**
     * Check if the response is successful.
     *
     * @return bool
     */
    public function isSuccess() {
        return $this->success === true;
    }

    /**
     * Check the hostname.
     *
     * @param string $hostname
     *
     * @return bool
     */
    public function isHostname($hostname) {
        return $this->getHostname() === $hostname;
    }
}
