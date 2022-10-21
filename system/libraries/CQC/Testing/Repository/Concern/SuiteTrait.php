<?php
use Symfony\Component\Finder\Finder;

trait CQC_Testing_Repository_Concern_SuiteTrait {
    /**
     * Create or update a suite.
     *
     * @param $name
     * @param $suite_data
     *
     * @return null|CQC_Testing_Model_Suite|bool
     */
    public function createOrUpdateSuite($name, $suite_data) {
        return CQC_Testing_Model_Suite::updateOrCreate(
            [
                'name' => $name,
            ],
            [
                'tests_path' => carr::get($suite_data, 'tests_path'),
                'command_options' => carr::get($suite_data, 'command_options'),
                'file_mask' => carr::get($suite_data, 'file_mask'),
                'retries' => carr::get($suite_data, 'retries'),
                'editor' => carr::get($suite_data, 'editor'),
                'coverage_enabled' => carr::get($suite_data, 'coverage.enabled', false),
                'coverage_index' => carr::get($suite_data, 'coverage.index'),
            ]
        );
    }

    /**
     * Find suite by project and name.
     *
     * @param $name
     * @param $project_id
     *
     * @return null|\CQC_Testing_Model_Suite
     */
    public function findSuiteByNameAndProject($name, $project_id) {
        return CQC_Testing_Model_Suite::where('name', $name)
            ->where('project_id', $project_id)
            ->first();
    }

    /**
     * Get all suites.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getSuites() {
        return CQC_Testing_Model_Suite::all();
    }

    /**
     * Find suite by id.
     *
     * @param mixed $id
     *
     * @return null|\CQC_Testing_Model_Suite
     */
    public function findSuiteById($id) {
        return CQC_Testing_Model_Suite::find($id);
    }

    /**
     * Remove suites that are not in present in config.
     *
     * @param $suites
     * @param $project
     */
    public function removeMissingSuites($suites, $project) {
        CQC_Testing_Model_Suite::where('project_id', $project->id)->whereNotIn('name', c::collect($suites)->keys())->each(function ($suite) {
            $suite->delete();
        });
    }

    /**
     * Sync all tests for a particular suite.
     *
     * @param $suite
     * @param $exclusions
     * @param mixed $showTests
     */
    protected function syncSuiteTests($suite, $exclusions, $showTests) {
        $files = $this->getAllFilesFromSuite($suite);

        foreach ($files as $file) {
            if (!$this->isExcluded($exclusions, null, $file) && $this->isTestable($file->getRealPath())) {
                $this->createOrUpdateTest($file, $suite);

                if ($showTests) {
                    $this->addMessage('NEW TEST: ' . $file->getRealPath());
                }
            } else {
                // If the test already exists, delete it.
                //
                if ($test = $this->findTestByNameAndSuite($file, $suite)) {
                    $test->delete();
                }
            }
        }

        foreach ($suite->tests as $test) {
            if (!file_exists($path = $test->fullPath)) {
                $test->delete();
            }
        }
    }

    /**
     * Get all files from a suite.
     *
     * @param $suite
     *
     * @return array
     */
    protected function getAllFilesFromSuite($suite) {
        if (!file_exists($suite->testsFullPath)) {
            die('FATAL ERROR: directory not found: ' . $suite->testsFullPath . '.');
        }

        $files = Finder::create()->files()->in($suite->testsFullPath);

        if ($suite->file_mask) {
            $files->name($suite->file_mask);
        }

        return iterator_to_array($files, false);
    }

    /**
     * Get all suites for a path.
     *
     * @param $path
     *
     * @return mixed
     */
    public function getSuitesForPath($path) {
        $projects = $this->getProjects();

        // Reduce the collection of projects by those whose path properties
        // (should be only 1) are contained in the fullpath of our
        // changed file
        $filtered_projects = $projects->filter(function ($project) use ($path) {
            return substr_count($path, $project->path) > 0;
        });

        // At this point we have (hopefully only 1) project. Now we need
        // the suite(s) associated with the project.
        return CQC_Testing_Model_Suite::get();
    }
}
