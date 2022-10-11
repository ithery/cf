<?php

class CApp_React_Renderer {
    protected $name;

    /**
     * Get the cache path for the compiled reacts.
     *
     * @var string
     */
    protected $cachePath;

    /**
     * Get the path for the source react.
     *
     * @var string
     */
    protected $path;

    public function __construct($name) {
        $this->name = $name;
        $this->path = CApp_React_Finder::instance()->find(
            $name = $this->normalize($name)
        );
        $this->cachePath = static::compiledPath();
    }

    public function render($props) {
        $nodeJs = CServer::nodeJs();
        // const reactDevelopmentUrl = 'https://unpkg.com/react@17/umd/react.development.js';
        // const reactDevelopmentDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.development.js';

        // const reactProductionUrl = 'https://unpkg.com/react@17/umd/react.production.min.js';
        // const reactProductionDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.production.min.js';
        $componentName = $this->name;
        $compiledPath = $this->getCompiledPath();
        if ($this->isExpired()) {
            $react = $nodeJs->createReact($this->path);
            $react->compile($compiledPath);
        }
        $jsContents = file_get_contents($compiledPath);
        $domId = 'cres-react-' . uniqid('cr-');
        $html = '<div id="' . $domId . '"></div>';
        $html .= '<script>';
        $html .= '(function() {';
        $html .= $jsContents;
        $propsJson = json_encode($props);
        $html .= <<<JAVASCRIPT
        ;
        var root = document.querySelector("#${domId}");
        ;ReactDOM.render(React.createElement(${componentName}, ${propsJson}), root);
    JAVASCRIPT;
        $html .= '})()';
        $html .= '</script>';

        return $html;
    }

    /**
     * Normalize the given react name.
     *
     * @param string $name
     *
     * @return string
     */
    public static function normalize($name) {
        $delimiter = CApp_React_Finder::HINT_PATH_DELIMITER;

        if (strpos($name, $delimiter) === false) {
            return str_replace('/', '.', $name);
        }

        list($namespace, $name) = explode($delimiter, $name);

        return $namespace . $delimiter . str_replace('/', '.', $name);
    }

    /**
     * Create a new compiler instance.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public static function compiledPath() {
        $path = CF::config('cresjs.react.compiled') ?: DOCROOT . 'temp/nodejs/' . CF::appCode() . '/compiled/react/';
        $path = rtrim($path, '/');

        return $path;
    }

    /**
     * Get the path to the compiled version of a react.
     *
     * @return string
     */
    public function getCompiledPath() {
        return $this->cachePath . '/' . sha1($this->path) . '.js';
    }

    /**
     * Determine if the react at the given path is expired.
     *
     * @return bool
     */
    public function isExpired() {
        $compiled = $this->getCompiledPath($this->path);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!CFile::exists($compiled)) {
            return true;
        }

        return CFile::lastModified($this->path) >= CFile::lastModified($compiled);
    }
}
