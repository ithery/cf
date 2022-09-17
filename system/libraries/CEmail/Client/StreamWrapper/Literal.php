<?php
class CEmail_Client_StreamWrapper_Literal {
    /**
     * @var string
     */
    const STREAM_NAME = 'mailsoliteral';

    /**
     * @var array
     */
    private static $aStreams = [];

    /**
     * @var resource
     */
    private $rStream;

    /**
     * @var int
     */
    private $iSize;

    /**
     * @var int
     */
    private $iPos;

    /**
     * @param resource $rStream
     * @param int      $iLiteralLen
     *
     * @return resource|bool
     */
    public static function createStream($rStream, $iLiteralLen, CEmail_Client_Loader $loader = null) {
        if (!in_array(self::STREAM_NAME, stream_get_wrappers())) {
            stream_wrapper_register(self::STREAM_NAME, 'CEmail_Client_StreamWrapper_Literal');
        }

        $sHashName = md5(microtime(true) . rand(1000, 9999));

        self::$aStreams[$sHashName] = [$rStream, $iLiteralLen];

        if ($loader) {
            $loader->IncStatistic('createStream/Literal');
        }

        return fopen(self::STREAM_NAME . '://' . $sHashName, 'rb');
    }

    /**
     * @param string $sPath
     *
     * @return bool
     */
    public function streamOpen($sPath) {
        $this->iPos = 0;
        $this->iSize = 0;
        $this->rStream = false;

        $bResult = false;
        $aPath = parse_url($sPath);

        if (isset($aPath['host'], $aPath['scheme'])
            && 0 < strlen($aPath['host']) && 0 < strlen($aPath['scheme'])
            && self::STREAM_NAME === $aPath['scheme']
        ) {
            $sHashName = $aPath['host'];
            if (isset(self::$aStreams[$sHashName])
                && is_array(self::$aStreams[$sHashName])
                && 2 === count(self::$aStreams[$sHashName])
            ) {
                $this->rStream = self::$aStreams[$sHashName][0];
                $this->iSize = self::$aStreams[$sHashName][1];
            }

            $bResult = is_resource($this->rStream);
        }

        return $bResult;
    }

    /**
     * @param int $iCount
     *
     * @return string
     */
    public function streamRead($iCount) {
        $sResult = false;
        if ($this->iSize < $this->iPos + $iCount) {
            $iCount = $this->iSize - $this->iPos;
        }

        if ($iCount > 0) {
            $sReadResult = '';
            $iRead = $iCount;
            while (0 < $iRead) {
                $sAddRead = @fread($this->rStream, $iRead);
                if (false === $sAddRead) {
                    $sReadResult = false;

                    break;
                }

                $sReadResult .= $sAddRead;
                $iRead -= strlen($sAddRead);
                $this->iPos += strlen($sAddRead);
            }

            if (false !== $sReadResult) {
                $sResult = $sReadResult;
            }
        }

        return $sResult;
    }

    /**
     * @return int
     */
    public function streamWrite() {
        return 0;
    }

    /**
     * @return int
     */
    public function streamTell() {
        return $this->iPos;
    }

    /**
     * @return bool
     */
    public function streamEof() {
        return $this->iPos >= $this->iSize;
    }

    /**
     * @return array
     */
    public function streamStat() {
        return [
            'dev' => 2,
            'ino' => 0,
            'mode' => 33206,
            'nlink' => 1,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 2,
            'size' => $this->iSize,
            'atime' => 1061067181,
            'mtime' => 1056136526,
            'ctime' => 1056136526,
            'blksize' => -1,
            'blocks' => -1
        ];
    }

    /**
     * @return bool
     */
    public function streamSeek() {
        return false;
    }
}
