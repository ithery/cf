<?php
class CEmail_Client_Loader {
    /**
     * @var bool
     */
    public $storeStatistic = true;

    /**
     * @var array
     */
    private $incStatistic = [];

    /**
     * @var array
     */
    private $setStatistic = [];

    /**
     * @staticvar bool $bIsInited
     *
     * @return void
     */
    public function __construct() {
        $this->setStatistic('inited', \microtime(true));
    }

    /**
     * @param string $sName
     * @param int    $iIncSize = 1
     *
     * @return void
     */
    public function incStatistic($sName, $iIncSize = 1) {
        if ($this->storeStatistic) {
            $this->incStatistic[$sName] = isset($this->incStatistic[$sName])
                ? $this->incStatistic[$sName] + $iIncSize : $iIncSize;
        }
    }

    /**
     * @param string $sName
     * @param mixed  $mValue
     *
     * @return void
     */
    public function setStatistic($sName, $mValue) {
        if ($this->storeStatistic) {
            $this->setStatistic[$sName] = $mValue;
        }
    }

    /**
     * @param string $sName
     *
     * @return mixed
     */
    public function getStatistic($sName) {
        return $this->storeStatistic && isset($this->setStatistic[$sName]) ? $this->setStatistic[$sName] : null;
    }

    /**
     * @return null|array
     */
    public function statistic() {
        $aResult = null;
        if ($this->storeStatistic) {
            $aResult = [
                'php' => [
                    'phpversion' => PHP_VERSION,
                    'ssl' => (int) \function_exists('openssl_open'),
                    'iconv' => (int) \function_exists('iconv')
                ]];

            if (\CBase_DependencyUtils::functionExistsAndEnabled('memory_get_usage')
                && \CBase_DependencyUtils::functionExistsAndEnabled('memory_get_peak_usage')
            ) {
                $aResult['php']['memory_get_usage']
                    = CBase_Formatter::formatFileSize(\memory_get_usage(true), 2);
                $aResult['php']['memory_get_peak_usage']
                    = CBase_Formatter::formatFileSize(\memory_get_peak_usage(true), 2);
            }

            $iTimeDelta = \microtime(true) - self::GetStatistic('Inited');
            $this->setStatistic('timeDelta', $iTimeDelta);

            $aResult['statistic'] = $this->setStatistic;
            $aResult['counts'] = $this->incStatistic;
            $aResult['time'] = $iTimeDelta;
        }

        return $aResult;
    }
}
