<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 10:04:13 PM
 */
class CElement_Component_DataTable_Options {
    /**
     * Page number buttons only (1.10.8).
     */
    const OPTION_PAGING_TYPE_NUMBERS = 'numbers';

    /**
     * 'Previous' and 'Next' buttons only.
     */
    const OPTION_PAGING_TYPE_SIMPLE = 'simple';

    /**
     * 'Previous' and 'Next' buttons, plus page numbers.
     */
    const OPTION_PAGING_TYPE_SIMPLE_NUMBERS = 'simple_numbers';

    /**
     * 'First', 'Previous', 'Next' and 'Last' buttons.
     */
    const OPTION_PAGING_TYPE_FULL = 'full';

    /**
     * 'First', 'Previous', 'Next' and 'Last' buttons, plus page numbers.
     */
    const OPTION_PAGING_TYPE_FULL_NUMBERS = 'full_numbers';

    /**
     * 'First' and 'Last' buttons, plus page numbers.
     */
    const OPTION_PAGING_TYPE_FIRST_LAST_NUMBERS = 'first_last_numbers';

    protected $options;

    private $keyPreviousVersion = [
        'bPaginate' => 'paging',
        'bLengthChange' => 'lengthChange',
        'bFilter' => 'searching',
        'bInfo' => 'info',
        'bProcessing' => 'processing',
        'bServerSide' => 'serverSide',
        'bAutoWidth' => 'autoWidth',
        'bDeferRender' => 'deferRender',
        'bStateSave' => 'stateSave',
        'sPaginationType' => 'pagingType',
    ];

    public function __construct() {
        $defaultOptions = [
            'deferRender' => true,
            'searching' => true,
            'processing' => true,
            'ordering' => true,
            'scrollX' => false,
            'serverSide' => false,
            'info' => true,
            'paging' => true,
            'lengthChange' => true,
            'autoWidth' => false,
            'pagingType' => static::OPTION_PAGING_TYPE_FULL_NUMBERS,
            'height' => false,
            'stateSave' => false,
        ];
        foreach ($defaultOptions as $key => $value) {
            $defaultOptions[$key] = c::theme('datatable.options.' . $key, $value);
            if ($defaultOptions[$key] === null) {
                $defaultOptions[$key] = $value;
            }
        }
        $this->options = $defaultOptions;
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    public function setOption($key, $option) {
        $key = $this->normalizeKey($key);
        carr::set($this->options, $key, $option);

        return $this;
    }

    public function getOption($key, $defaultValue = null) {
        $key = $this->normalizeKey($key);

        return carr::get($this->options, $key, $defaultValue);
    }

    public function normalizeKey($key) {
        if (!isset($this->options[$key])) {
            //locate previous version for this key
            if (isset($this->keyPreviousVersion[$key])) {
                $key = $this->keyPreviousVersion[$key];
            }
        }

        return $key;
    }

    public function toJsonRow($key, $withTrailingComma = true) {
        $value = $this->getOption($key);
        $keys = [$key];
        $flippedKeyPreviousVersion = array_flip($this->keyPreviousVersion);
        if (isset($flippedKeyPreviousVersion[$key])) {
            $keys[] = $flippedKeyPreviousVersion[$key];
        }

        $jsonRow = '';
        foreach ($keys as $key) {
            if ($key == 'scrollX' && $value == false) {
                //dont draw this key for false falue
                break;
            }
            if ($key == 'scrollY' && $value == false) {
                //dont draw this key for false falue
                break;
            }
            $key = (strpos($key, ' ') !== false || strpos($key, '-') !== false) ? "'" . $key . "'" : $key;
            $jsonRow .= ' ' . $key . ': ' . json_encode($value) . ',';
        }
        if (strlen($jsonRow) > 0) {
            //remove space
            $jsonRow = substr($jsonRow, 1);
        }

        if (!$withTrailingComma && strlen($jsonRow) > 0) {
            $jsonRow = substr($jsonRow, 0, -1);
        }

        return $jsonRow;
    }
}
