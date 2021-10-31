<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\PHP;

use PHPUnit\TextUI\XmlConfiguration\Filesystem\DirectoryCollection;
use const PATH_SEPARATOR;
use function constant;
use function define;
use function defined;
use function getenv;
use function implode;
use function ini_get;
use function ini_set;
use function putenv;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class PhpHandler {
    public function handle(Php $configuration) {
        $this->handleIncludePaths($configuration->includePaths());
        $this->handleIniSettings($configuration->iniSettings());
        $this->handleConstants($configuration->constants());
        $this->handleGlobalVariables($configuration->globalVariables());
        $this->handleServerVariables($configuration->serverVariables());
        $this->handleEnvVariables($configuration->envVariables());
        $this->handleVariables('_POST', $configuration->postVariables());
        $this->handleVariables('_GET', $configuration->getVariables());
        $this->handleVariables('_COOKIE', $configuration->cookieVariables());
        $this->handleVariables('_FILES', $configuration->filesVariables());
        $this->handleVariables('_REQUEST', $configuration->requestVariables());
    }

    private function handleIncludePaths(DirectoryCollection $includePaths) {
        if (!$includePaths->isEmpty()) {
            $includePathsAsStrings = [];

            foreach ($includePaths as $includePath) {
                $includePathsAsStrings[] = $includePath->path();
            }

            ini_set(
                'include_path',
                implode(PATH_SEPARATOR, $includePathsAsStrings)
                . PATH_SEPARATOR
                . ini_get('include_path')
            );
        }
    }

    private function handleIniSettings(IniSettingCollection $iniSettings) {
        foreach ($iniSettings as $iniSetting) {
            $value = $iniSetting->value();

            if (defined($value)) {
                $value = (string) constant($value);
            }

            ini_set($iniSetting->name(), $value);
        }
    }

    private function handleConstants(ConstantCollection $constants) {
        foreach ($constants as $constant) {
            if (!defined($constant->name())) {
                define($constant->name(), $constant->value());
            }
        }
    }

    private function handleGlobalVariables(VariableCollection $variables) {
        foreach ($variables as $variable) {
            $GLOBALS[$variable->name()] = $variable->value();
        }
    }

    private function handleServerVariables(VariableCollection $variables) {
        foreach ($variables as $variable) {
            $_SERVER[$variable->name()] = $variable->value();
        }
    }

    private function handleVariables($target, VariableCollection $variables) {
        foreach ($variables as $variable) {
            $GLOBALS[$target][$variable->name()] = $variable->value();
        }
    }

    private function handleEnvVariables(VariableCollection $variables) {
        foreach ($variables as $variable) {
            $name = $variable->name();
            $value = $variable->value();
            $force = $variable->force();

            if ($force || getenv($name) === false) {
                putenv("{$name}={$value}");
            }

            $value = getenv($name);

            if ($force || !isset($_ENV[$name])) {
                $_ENV[$name] = $value;
            }
        }
    }
}
