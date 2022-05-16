<?php
class CServer_NodeJs_Wrapper_ReactWrapper extends CServer_NodeJs_WrapperAbstract {
    protected $sourceMap;

    protected $lastFile;

    public function __construct(CServer_NodeJs_Runner $node, $file, $sourceMap = false) {
        $this->sourceMap = $sourceMap;
        parent::__construct($node, $file);
        $this->checkInstallation();
    }

    public function checkInstallation() {
        $npmModules = [
            'babel-cli' => '^6.10.1',
            'babel-plugin-transform-react-jsx' => '^6.8.0',
            'babel-preset-es2015' => '^6.8.0',
            'babel-preset-react' => '^6.11.1'
        ];
        $this->node->installer()->install($npmModules);
    }

    public function write($file) {
        $this->lastFile = $file;
        parent::write($file);
    }

    public function getSourceMapFile() {
        if (!$this->lastFile) {
            return;
        }

        return $this->lastFile . '.map';
    }

    public function getSourceMap() {
        if (!($file = $this->getSourceMapFile())) {
            return;
        }

        return file_get_contents($file);
    }

    public function compile() {
        $path = $this->getPath('source.jsx');
        if (!$this->lastFile) {
            $this->lastFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . preg_replace('/\.jsx$/i', '', basename($path)) . '.js';
        }
        $destination = $this->lastFile;
        $inFile = escapeshellarg($path);
        $outFile = escapeshellarg($destination);
        $appDirectory = $this->node->getDirectory();
        $plugins = implode(',', array_map(function ($plugin) use ($appDirectory) {
            return escapeshellarg(implode(DIRECTORY_SEPARATOR, [$appDirectory, 'node_modules', 'babel-plugin-' . $plugin]));
        }, [
            'transform-react-jsx',
        ]));
        $presets = implode(',', array_map(function ($preset) use ($appDirectory) {
            return escapeshellarg(implode(DIRECTORY_SEPARATOR, [$appDirectory, 'node_modules', 'babel-preset-' . $preset]));
        }, [
            'es2015',
            'react',
        ]));
        $arguments = '--presets ' . $presets
            . ' --plugins ' . $plugins . ' ' . $inFile
            . ' --out-file ' . $outFile;
        if ($this->sourceMap) {
            $arguments .= ' --source-maps --debug';
        }
        $arguments .= ' 2>&1';
        $output = $this->execModuleScript('babel-cli', 'bin/babel.js', $arguments);

        if (preg_match('/Exception|Error/i', $output)) {
            throw new \ErrorException("Command error: ${output}", 2);
        }
        if (is_null($output) && file_exists($destination)) {
            $output = file_get_contents($destination);
        }

        return $output;
    }

    public function fallback() {
        $fallback = new CServer_NodeJs_Wrapper_ReactWrapper_ReactWrapperFallback($this->getSource());

        return $fallback->parseJsx();
    }
}
