<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 11:53:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CCDN_AssetInterface {

    public function init($configurations);

    public function getIncludedDirectories();

    public function getIncludedExtensions();

    public function getIncludedPatterns();

    public function getExcludedDirectories();

    public function getExcludedFiles();

    public function getExcludedExtensions();

    public function getExcludedPatterns();

    public function getExcludeHidden();

    public function getAssets();

    public function setAssets($assets);
}
