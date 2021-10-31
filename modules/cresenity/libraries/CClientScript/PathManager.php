<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 5:52:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class PathManager {

    protected $aliases = array();
    protected $docRoot;

    public function __construct($aliasMap = array(), $documentRoot = null) {
        if ($documentRoot == null) {
            $documentRoot = DOCROOT;
        }
        $this->addAliasMap($aliasMap);
        $this->setDocumentRoot($documentRoot);
    }

    public function addAliasMap($map) {
        foreach ($map as $alias => $path) {
            $check = realpath($path);
            if (!$check)
                throw new \Exception("Alias path does not exist: {$path}");
            $this->aliases[$alias] = $check;
        }
    }

    public function setDocumentRoot($path) {
        if (!$path) {
            $path = DOCROOT;
        }
        $this->docRoot = realpath($path);
        if (!$this->docRoot) {
            throw new CException("Document root is not a valid path :path", array(':path', $path));
        }
    }

    public function getPath($url) {
        foreach ($this->aliases as $alias => $path) {
            if (strpos($url, $alias) !== 0)
                continue;
            return $path . basename($url);
        }
        return $this->docRoot . $url;
    }

}
