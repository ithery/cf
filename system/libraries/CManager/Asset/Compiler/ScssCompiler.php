<?php

class CManager_Asset_Compiler_ScssCompiler {
    use CManager_Asset_Trait_CssTrait;

    protected $file;

    /**
     * @var string
     */
    protected $outFile = '';

    protected $lastModTimeNewestAsset = 0;

    protected $lastModTimeCompiledAsset = 0;

    public function __construct($file) {
        $this->file = $file;

        $this->determineOutFile();
        $this->determineLastModified();
    }

    protected function determineLastModified() {
        //Set the instance variable to store the last modified time of the newest file
        if (!file_exists($this->file)) {
            throw new Exception('Error to compile scss asset, ' . $this->file . ' not exist');
        }
        $this->lastModTimeNewestAsset = filemtime($this->file);

        $this->lastModTimeCompiledAsset = 0;
        if (file_exists($this->outFile)) {
            $this->lastModTimeCompiledAsset = filemtime($this->outFile);
        }
    }

    /**
     * @return string
     */
    protected function determineOutFile() {
        $ymd = date('Ymd', filemtime($this->file));
        $basePath = CF::publicPath() ? CF::publicPath() . '/' : DOCROOT;
        $this->outFile = $basePath . 'compiled/asset/scss/' . $ymd . '/' . md5($this->file) . '.css';
    }

    protected function needToRecompile() {
        return $this->lastModTimeCompiledAsset < $this->lastModTimeNewestAsset;
    }

    public function compile() {
        if ($this->needToRecompile()) {
            $dirname = dirname($this->outFile);
            if (!is_dir($dirname)) {
                CFile::makeDirectory($dirname, 0755, true);
            }

            $stringSass = CFile::get($this->file);
            // //$compiler = new CManager_Asset_SCSS_Compiler();
            // if (version_compare(PHP_VERSION, '7.2') < 0) {
            //     $compiler = new CManager_Asset_SCSS_Compiler();
            //     $compiler->setImportPaths(dirname($this->file));
            //     $compiled = $compiler->compile($stringSass);
            // } else {
            $compiler = new CManager_Asset_SCSS_ScssPhpCompiler();
            $compiler->setImportPaths(dirname($this->file));
            $sourceMap = CF::config('assets.scss.source_map', !CF::isProduction());
            $sourceMapOptions = [
                'outputSourceFiles' => $sourceMap,
            ];
            if ($sourceMap) {
                $sourceMapOptions['sourceMapWriteTo'] = $this->outFile . '.map';
                $sourceMapOptions['sourceMapURL'] = basename($this->outFile . '.map');
                $sourceMapOptions['sourceMapBasepath'] = dirname($this->file);
                $sourceMapOptions['sourceMapFilename'] = basename($this->outFile);
            }

            $compiled = $compiler->compile($stringSass, $sourceMapOptions, $this->file);
            if ($compiled instanceof \ScssPhp\ScssPhp\CompilationResult) {
                if ($sourceMap) {
                    $sourceMap = $compiled->getSourceMap();

                    CFile::put($this->outFile . '.map', $sourceMap);
                }

                $compiled = $compiled->getCss();
            }
            // }
            CFile::put($this->outFile, $compiled);
        }

        return $this->outFile;
    }
}
