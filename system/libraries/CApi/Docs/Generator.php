<?php
use OpenApi\Util;
use OpenApi\Annotations\Server;
use OpenApi\Annotations\OpenApi;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;
use OpenApi\Generator as OpenApiGenerator;
use Symfony\Component\Yaml\Dumper as YamlDumper;

class CApi_Docs_Generator {
    protected const SCAN_OPTION_PROCESSORS = 'processors';

    protected const SCAN_OPTION_PATTERN = 'pattern';

    protected const SCAN_OPTION_ANALYSER = 'analyser';

    protected const SCAN_OPTION_ANALYSIS = 'analysis';

    protected const SCAN_OPTION_EXCLUDE = 'exclude';

    protected const AVAILABLE_SCAN_OPTIONS = [
        self::SCAN_OPTION_PATTERN,
        self::SCAN_OPTION_ANALYSER,
        self::SCAN_OPTION_ANALYSIS,
        self::SCAN_OPTION_EXCLUDE,
    ];

    /**
     * @var string|array
     */
    protected $annotationsDir;

    /**
     * @var string
     */
    protected $outputDir;

    /**
     * @var string
     */
    protected $jsonDocsFile;

    /**
     * @var string
     */
    protected $yamlDocsFile;

    protected $constants;

    /**
     * @var array
     */
    protected $scanOptions;

    /**
     * @var OpenApi
     */
    protected $openApi;

    /**
     * @var array
     */
    protected $excludedDirs;

    /**
     * @param array                        $pathsConfig
     * @param array                        $contants
     * @param CApi_Docs_SecurityDefinition $security
     * @param array                        $scanOptions
     * @param bool                         $yamlCopyRequired
     */
    public function __construct($pathsConfig, $contants, CApi_Docs_SecurityDefinition $security, $scanOptions, $yamlCopyRequired = false) {
        $this->annotationsDir = carr::get($pathsConfig, 'annotations', []);
        $this->excludedDirs = carr::get($pathsConfig, 'excludes', []);
        $this->outputDir = carr::get($pathsConfig, 'output.directory');
        $this->basePath = carr::get($pathsConfig, 'base', null);
        $this->jsonDocsFile = rtrim($this->outputDir, DS) . DS . carr::get($pathsConfig, 'output.json', 'api-docs.json');
        $this->yamlDocsFile = rtrim($this->outputDir, DS) . DS . carr::get($pathsConfig, 'output.yaml', 'api-docs.yaml');
        $this->constants = $contants;
        $this->scanOptions = $scanOptions;
        $this->security = $security;
        $this->yamlCopyRequired = $yamlCopyRequired;
    }

    /**
     * @throws CApi_Docs_Exception_SwaggerException
     */
    public function generateDocs() {
        return $this->prepareDirectory()
            ->defineConstants()
            ->scanFilesForDocumentation()
            ->populateServers()
            ->saveJson()
            ->makeYamlCopy();
    }

    /**
     * Check directory structure and permissions.
     *
     * @throws CApi_Docs_Exception_SwaggerException
     *
     * @return $this
     */
    protected function prepareDirectory() {
        if (CFile::isDirectory($this->outputDir) && !CFile::isWritable($this->outputDir)) {
            throw new CApi_Docs_Exception_SwaggerException('Documentation storage directory is not writable');
        }

        if (!CFile::isDirectory($this->outputDir)) {
            CFile::makeDirectory($this->outputDir, 0755, true);
        }

        if (!CFile::isDirectory($this->outputDir)) {
            throw new CApi_Docs_Exception_SwaggerException('Documentation storage directory could not be created');
        }

        return $this;
    }

    /**
     * Define constant which will be replaced.
     *
     * @return $this
     */
    protected function defineConstants() {
        if (!empty($this->constants)) {
            foreach ($this->constants as $key => $value) {
                defined($key) || define($key, $value);
            }
        }

        return $this;
    }

    /**
     * Scan directory and create Swagger.
     *
     * @return $this
     */
    protected function scanFilesForDocumentation() {
        $generator = $this->createOpenApiGenerator();
        $finder = $this->createScanFinder();

        // Analysis.
        $analysis = carr::get($this->scanOptions, self::SCAN_OPTION_ANALYSIS);

        $this->openApi = $generator->generate($finder, $analysis);

        return $this;
    }

    /**
     * Prepares generator for generating the documentation.
     *
     * @return OpenApiGenerator $generator
     */
    protected function createOpenApiGenerator(): OpenApiGenerator {
        $generator = new OpenApiGenerator();

        // Processors.
        $this->setProcessors($generator);

        // Analyser.
        $this->setAnalyser($generator);

        return $generator;
    }

    /**
     * @param OpenApiGenerator $generator
     *
     * @return void
     */
    protected function setProcessors(OpenApiGenerator $generator): void {
        $processorClasses = carr::get($this->scanOptions, self::SCAN_OPTION_PROCESSORS, []);
        $processors = [];

        foreach ($generator->getProcessors() as $processor) {
            $processors[] = $processor;
            if ($processor instanceof \OpenApi\Processors\BuildPaths) {
                foreach ($processorClasses as $customProcessor) {
                    $processors[] = new $customProcessor();
                }
            }
        }

        if (!empty($processors)) {
            $generator->setProcessors($processors);
        }
    }

    /**
     * @param OpenApiGenerator $generator
     *
     * @return void
     */
    protected function setAnalyser(OpenApiGenerator $generator): void {
        $analyser = carr::get($this->scanOptions, self::SCAN_OPTION_ANALYSER);

        if (!empty($analyser)) {
            $generator->setAnalyser($analyser);
        }
    }

    /**
     * Prepares finder for determining relevant files.
     *
     * @return Finder
     */
    protected function createScanFinder() {
        $pattern = carr::get($this->scanOptions, self::SCAN_OPTION_PATTERN);
        $exclude = carr::get($this->scanOptions, self::SCAN_OPTION_EXCLUDE);

        $exclude = !empty($exclude) ? $exclude : $this->excludedDirs;

        return Util::finder($this->annotationsDir, $exclude, $pattern);
    }

    /**
     * Generate servers section or basePath depending on Swagger version.
     *
     * @return $this
     */
    protected function populateServers(): self {
        if ($this->basePath !== null) {
            if (!is_array($this->openApi->servers)) {
                $this->openApi->servers = [];
            }

            $this->openApi->servers[] = new Server(['url' => $this->basePath]);
        }

        return $this;
    }

    /**
     * Save documentation as json file.
     *
     * @throws Exception
     *
     * @return $this
     */
    protected function saveJson() {
        $this->openApi->saveAs($this->jsonDocsFile);

        $this->security->generate($this->jsonDocsFile);

        return $this;
    }

    /**
     * Save documentation as yaml file.
     *
     * @return $this
     */
    protected function makeYamlCopy() {
        if ($this->yamlCopyRequired) {
            $yamlDocs = (new YamlDumper(2))->dump(
                json_decode(file_get_contents($this->jsonDocsFile), true),
                20,
                0,
                Yaml::DUMP_OBJECT_AS_MAP ^ Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE
            );

            file_put_contents(
                $this->yamlDocsFile,
                $yamlDocs
            );
        }

        return $this;
    }
}
