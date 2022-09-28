<?php

class CEmail_Client_Imap_Response {
    const RESPONSE_TYPE_UNKNOWN = 0;

    const RESPONSE_TYPE_TAGGED = 1;

    const RESPONSE_TYPE_UNTAGGED = 2;

    const RESPONSE_TYPE_CONTINUATION = 3;

    const RESPONSE_STATUS_OK = 'OK';

    const RESPONSE_STATUS_NO = 'NO';

    const RESPONSE_STATUS_BAD = 'BAD';

    const RESPONSE_STATUS_BYE = 'BYE';

    const RESPONSE_STATUS_PREAUTH = 'PREAUTH';

    /**
     * @var array
     */
    public $responseList;

    /**
     * @var null|array
     */
    public $pptionalResponse;

    /**
     * @var string
     */
    public $statusOrIndex;

    /**
     * @var string
     */
    public $humanReadable;

    /**
     * @var bool
     */
    public $isStatusResponse;

    /**
     * @var string
     */
    public $responseType;

    /**
     * @var string
     */
    public $tag;

    private function __construct() {
        $this->responseList = [];
        $this->optionalResponse = null;
        $this->statusOrIndex = '';
        $this->humanReadable = '';
        $this->usStatusResponse = false;
        $this->responseType = self::RESPONSE_TYPE_UNKNOWN;
        $this->Tag = '';
    }

    /**
     * @return \CEmail_Client_Imap_Response
     */
    public static function newInstance() {
        return new self();
    }

    /**
     * @param string $aList
     *
     * @return string
     */
    private function recToLine($aList) {
        $aResult = [];
        if (\is_array($aList)) {
            foreach ($aList as $mItem) {
                $aResult[] = \is_array($mItem) ? '(' . $this->recToLine($mItem) . ')' : (string) $mItem;
            }
        }

        return \implode(' ', $aResult);
    }

    /**
     * @return string
     */
    public function toLine() {
        return $this->recToLine($this->responseList);
    }
}
