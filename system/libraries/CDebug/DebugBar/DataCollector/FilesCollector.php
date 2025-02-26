<?php

use DebugBar\DataCollector\Renderable;

use DebugBar\DataCollector\DataCollector;

class CDebug_DebugBar_DataCollector_FilesCollector extends DataCollector implements Renderable {
    const BASE_PATH = DOCROOT;

    /**
     * @inheritDoc
     */
    public function collect() {
        $files = $this->getIncludedFiles();

        $included = [];

        foreach ($files as $file) {
            $included[] = [
                'message' => "'" . $this->stripBasePath($file) . "',",
                // Use PHP syntax so we can copy-paste to compile config file.
                'is_string' => true,
            ];
        }
        // First the included files, then those that are going to be compiled.
        $messages = $included;

        return [
            'messages' => $messages,
            'count' => count($included),
        ];
    }

    /**
     * Get the files included on load.
     *
     * @return array
     */
    protected function getIncludedFiles() {
        return get_included_files();
    }

    /**
     * Remove the basePath from the paths, so they are relative to the base.
     *
     * @param $path
     *
     * @return string
     */
    protected function stripBasePath($path) {
        return ltrim(str_replace(self::BASE_PATH, '', $path), '/');
    }

    /**
     * @inheritDoc
     */
    public function getWidgets() {
        $name = $this->getName();

        return [
            "$name" => [
                'icon' => 'files-o',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => "$name.messages",
                'default' => '{}'
            ],
            "$name:badge" => [
                'map' => "$name.count",
                'default' => 'null'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName() {
        return 'files';
    }
}
