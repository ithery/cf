<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SebastianBergmann;

final class Version {

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $release;

    /**
     * @var string
     */
    private $version;

    public function __construct($release, $path) {
        $this->release = $release;
        $this->path = $path;
    }

    public function getVersion() {
        if ($this->version === null) {
            if (\substr_count($this->release, '.') + 1 === 3) {
                $this->version = $this->release;
            } else {
                $this->version = $this->release . '-dev';
            }

            $git = $this->getGitInformation($this->path);

            if ($git) {
                if (\substr_count($this->release, '.') + 1 === 3) {
                    $this->version = $git;
                } else {
                    $git = \explode('-', $git);

                    $this->version = $this->release . '-' . \end($git);
                }
            }
        }

        return $this->version;
    }

    /**
     * @return bool|string
     */
    private function getGitInformation($path) {
        if (!\is_dir($path . DIRECTORY_SEPARATOR . '.git')) {
            return false;
        }

        $process = \proc_open(
                'git describe --tags',
                [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes,
                $path
        );

        if (!\is_resource($process)) {
            return false;
        }

        $result = \trim(\stream_get_contents($pipes[1]));

        \fclose($pipes[1]);
        \fclose($pipes[2]);

        $returnCode = \proc_close($process);

        if ($returnCode !== 0) {
            return false;
        }

        return $result;
    }

}
