<?php

class CException_ErrorModel implements CInterface_Arrayable {
    protected $throwable;

    /**
     * @var array
     */
    protected $solutions = [];

    /**
     * @var string
     */
    protected $defaultTab = 'trace';

    /**
     * @var array
     */
    protected $defaultTabProps = [];

    /**
     * @var string
     */
    protected $solutionTransformerClass;

    protected $report;

    public function __construct(
        $throwable,
        array $solutions,
        $solutionTransformerClass = null
    ) {
        $this->throwable = $throwable;

        $this->solutions = $solutions;

        $this->solutionTransformerClass = $solutionTransformerClass ?: CException_Solution_SolutionTransformer::class;

        $this->report = CException_Manager::instance()->createReport($throwable);
    }

    public function throwableString(): string {
        if (!$this->throwable) {
            return '';
        }

        $throwableString = sprintf(
            "%s: %s in file %s on line %d\n\n%s\n",
            get_class($this->throwable),
            $this->throwable->getMessage(),
            $this->throwable->getFile(),
            $this->throwable->getLine(),
            $this->report->getThrowable()->getTraceAsString()
        );

        return htmlspecialchars($throwableString);
    }

    public function title() {
        $message = htmlspecialchars($this->report->getMessage());

        return "🧨 {$message}";
    }

    public function config() {
        return CException::config()->toArray();
    }

    public function solutions() {
        $solutions = array_map(function ($solution) {
            return (new $this->solutionTransformerClass($solution))->toArray();
        }, $this->solutions);

        return $solutions;
    }

    public function jsonEncode($data): string {
        $jsonOptions = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        return json_encode($data, $jsonOptions);
    }

    public function getAssetContents($asset) {
        $assetPath = __DIR__ . "/../../resources/compiled/{$asset}";

        return file_get_contents($assetPath);
    }

    protected function shareEndpoint() {
        return  'https://flareapp.io/api/public-reports';
    }

    public function report() {
        return $this->report->toArray();
    }

    public function toArray() {
        return [
            'throwableString' => $this->throwableString(),
            'shareEndpoint' => $this->shareEndpoint(),
            'title' => $this->title(),
            'report' => $this->report(),
            'shareableReport' => (new CException_Truncation_ReportTrimmer())->trim($this->report()),
            'config' => $this->config(),
            'solutions' => $this->solutions(),
            'housekeepingEndpoint' => '',
            'jsonEncode' => Closure::fromCallable([$this, 'jsonEncode']),
            'getAssetContents' => Closure::fromCallable([$this, 'getAssetContents']),
            'defaultTab' => $this->defaultTab,
            'defaultTabProps' => $this->defaultTabProps,
            'theme' => carr::get($this->config(), 'theme'),
        ];
    }
}
