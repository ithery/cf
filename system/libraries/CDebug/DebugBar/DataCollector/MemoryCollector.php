<?php

defined('SYSPATH') or die('No direct access allowed.');
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\DataCollector;

/**
 * Collects info about memory usage.
 */
class CDebug_DebugBar_DataCollector_MemoryCollector extends DataCollector implements Renderable {
    protected $realUsage = false;

    protected $peakUsage = 0;

    /**
     * Returns whether total allocated memory page size is used instead of actual used memory size
     * by the application.  See $real_usage parameter on memory_get_peak_usage for details.
     *
     * @return bool
     */
    public function getRealUsage() {
        return $this->realUsage;
    }

    /**
     * Sets whether total allocated memory page size is used instead of actual used memory size
     * by the application.  See $real_usage parameter on memory_get_peak_usage for details.
     *
     * @param bool $realUsage
     */
    public function setRealUsage($realUsage) {
        $this->realUsage = $realUsage;
    }

    /**
     * Returns the peak memory usage.
     *
     * @return int
     */
    public function getPeakUsage() {
        return $this->peakUsage;
    }

    /**
     * Updates the peak memory usage value.
     */
    public function updatePeakUsage() {
        $this->peakUsage = memory_get_peak_usage($this->realUsage);
    }

    /**
     * @return array
     */
    public function collect() {
        $this->updatePeakUsage();

        return [
            'peak_usage' => $this->peakUsage,
            'peak_usage_str' => $this->getDataFormatter()->formatBytes($this->peakUsage)
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        return 'memory';
    }

    /**
     * @return array
     */
    public function getWidgets() {
        return [
            'memory' => [
                'icon' => 'cogs',
                'tooltip' => 'Memory Usage',
                'map' => 'memory.peak_usage_str',
                'default' => "'0B'"
            ]
        ];
    }
}
