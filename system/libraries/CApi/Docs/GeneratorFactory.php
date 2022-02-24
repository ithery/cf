<?php

class CApi_Docs_GeneratorFactory {
    protected $group;

    /**
     * @var array
     */
    protected $annotationDirs;

    /**
     * @var array
     */
    protected $excludeDirs;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $outputDir;

    /**
     * @var string
     */
    protected $outputJson;

    /**
     * @var string
     */
    protected $outputYaml;

    /**
     * @var string
     */
    protected $scanAnalyser;

    /**
     * @var string
     */
    protected $scanAnalysis;

    /**
     * @var string
     */
    protected $scanPattern;

    /**
     * @var array
     */
    protected $scanProcessors;

    /**
     * @var array
     */
    protected $scanExclude;

    /**
     * @var array
     */
    protected $securitySchemes;

    /**
     * @var array
     */
    protected $securityConfig;

    /**
     * @var bool
     */
    protected $yamlCopyRequired;

    public function __construct($group) {
        $this->group = $group;

        $config = CF::config('api.groups.' . $this->group . 'docs');
        $this->annotationDirs = carr::get($config, 'path.annotations', []);
        $this->excludeDirs = carr::get($config, 'path.excludes', []);
        $this->outputDir = carr::get($config, 'path.output.directory', '');
        $this->outputJson = carr::get($config, 'path.output.json', 'api-docs.json');
        $this->outputYaml = carr::get($config, 'path.output.yaml', 'api-docs.yaml');
        $this->basePath = carr::get($config, 'path.base', null);

        $this->scanAnalyser = carr::get($config, 'scan_options.analyser', null); // default \OpenApi\Analysers\TokenAnalyser::class
        $this->scanAnalysis = carr::get($config, 'scan_options.analysis', null); // default \OpenApi\Analysis::class
        $this->scanProcessors = carr::get($config, 'scan_options.processors', []);
        $this->scanPattern = carr::get($config, 'scan_options.pattern', '*.php');
        $this->scanExclude = carr::get($config, 'scan_options.exclude', []);
        $this->securitySchemes = carr::get($config, 'security_options.schemes', []);
        $this->securityConfig = carr::get($config, 'security_options.security', []);
        $this->yamlCopyRequired = carr::get($config, 'generate_yaml', false);
    }

    public function addAnnotationDir($path) {
        $this->annotationDirs[] = $path;

        return $this;
    }

    public function addExcludeDir($path) {
        $this->excludeDirs[] = $path;

        return $this;
    }

    /**
     * @param string $dir
     *
     * @return $this
     */
    public function setOutputDir($dir) {
        $this->outputDir = $dir;

        return $this;
    }

    /**
     * Filename of json file (dont include path here, path is from outputDir).
     *
     * @param string $filename
     *
     * @return $this
     */
    public function setOutputJsonFile($filename) {
        $this->outputJson = $filename;

        return $this;
    }

    public function generateYaml($bool = true) {
        $this->yamlCopyRequired = $bool;
    }

    public function createGenerator() {
        $pathsConfig = [];
        $pathsConfig['annotations'] = $this->annotationDirs;
        $pathsConfig['excludes'] = $this->excludeDirs;
        $pathsConfig['output'] = [];
        $pathsConfig['output']['directory'] = $this->outputDir;
        $pathsConfig['output']['json'] = $this->outputJson;
        $pathsConfig['output']['yaml'] = $this->outputYaml;
        $pathsConfig['base'] = $this->basePath;

        $scanOptions = [
            'analyser' => $this->scanAnalyser,
            'analysis' => $this->scanAnalysis,
            'processors' => $this->scanProcessors,
            'pattern' => $this->scanPattern,
            'exclude' => $this->scanExclude,
        ];
        //$constants = $config['constants'] ?? [];
        //$yamlCopyRequired = $config['generate_yaml_copy'] ?? false;

        $security = new CApi_Docs_SecurityDefinition($this->securitySchemes, $this->securityConfig);

        return new CApi_Docs_Generator(
            $pathsConfig,
            $security,
            $scanOptions,
            $this->yamlCopyRequired
        );
    }

    public function generate() {
        return $this->createGenerator()->generateDocs();
    }
}
