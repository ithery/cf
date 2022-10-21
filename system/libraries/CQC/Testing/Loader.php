<?php

class CQC_Testing_Loader {
    /**
     * @var CQC_Testing_Repository
     */
    protected $dataRepository;

    protected $exclusions;
    public function __construct(CQC_Testing_Repository $repository) {
        $this->dataRepository = $repository;
        $this->exclusions = [];
    }

    /**
     * @param CQC_Testing_TestSuite[] $data
     *
     * @return void
     */
    public function refreshSuites(array $data) {
        $currentSuites = c::collect($data)->map(function (CQC_Testing_TestSuite $suite) {
            return $suite->getName();
        })->toArray();
        $this->dataRepository->removeMissingSuites($currentSuites);

        c::collect($data)->map(function (CQC_Testing_TestSuite $suite) {
            $this->createSuite($suite);
        });
        $this->dataRepository->syncTests($this->exclusions);
    }

    /**
     * Create or update the suite.
     *
     * @param CQC_Testing_TestSuite $suite
     */
    private function createSuite(CQC_Testing_TestSuite $suite) {
        if (!$this->dataRepository->createOrUpdateSuite($suite)) {
            die;
        }
    }
}
