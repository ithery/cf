<?php
class ConfigTransformer {
    protected $items = [];

    public function __construct() {
        $this->items = c::collect();
    }

    public function transform($keys, $alias = '') {
        return c::collect($keys)->map(function ($keys, $index) use ($alias) {
            if ($alias) {
                $alias = $alias . '.';
            }
            if (!is_string($index)) {
                return;
            }
            $alias .= $index;
            if (is_array($keys)) {
                return $this->transform($keys, $alias);
            } else {
                $this->items->push($alias);

                return $keys;
            }
        });
    }

    public function all(): array {
        return $this->items->filter(function ($config, $key) {
            return strpos($config, 'app.providers') === false
                && strpos($config, 'filesystems.links') === false
                && strpos($config, 'app.aliases') === false;
        })->toArray();
    }
}

class Controller_Demo_Dashboard extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Dashboard');

        return $app;
    }

    public function config() {
        $configs = CConfig::repository()->all();
        $config = new ConfigTransformer();
        $config->transform($configs);
        echo json_encode($config->all());
    }

    public function translation() {
        $translations = [];
        $paths = array_reverse(CF::paths());
        foreach ($paths as $path) {
            $translationPath = $path . 'i18n' . DIRECTORY_SEPARATOR;
            if (CFile::isDirectory($translationPath)) {
                $directories = CFile::directories($translationPath);
                foreach ($directories as $directory) {
                    $files = CFile::files($directory);
                    foreach ($files as $file) {
                        $fileName = str_replace('.php', '', $file->getFileName());
                        $fields = include $file->getPathName();
                        if (is_array($fields)) {
                            foreach ($fields as $field => $message) {
                                $translations[] = "{$fileName}.{$field}";
                                if ($fileName == 'core') {
                                    $translations[] = $field;
                                }
                            }
                        }
                    }
                }
            }
        }
        echo json_encode(array_filter($translations));
    }

    public function getPermissions($navs) {
        $permissions = [];
        foreach ($navs as $nav) {
            $name = carr::get($nav, 'name');
            $subnav = carr::get($nav, 'subnav');
            $action = carr::get($nav, 'action');
            if ($name) {
                $permissions[] = $name;
            }
            if (is_array($subnav)) {
                $subnavPermissions = $this->getPermissions($subnav);
                $permissions = array_merge($permissions, $subnavPermissions);
            }
            if (is_array($action)) {
                foreach ($action as $act) {
                    $actName = carr::get($act, 'name');
                    if ($actName) {
                        $permissions[] = $actName;
                    }
                }
            }
        }

        return $permissions;
    }

    public function permission() {
        $permissions = [];
        $path = CF::appDir();
        $navPath = $path . DS . 'default' . DS . 'navs';
        $files = CFile::files($navPath);
        foreach ($files as $file) {
            // $fileName = str_replace('.php', '', $file->getFileName());
            $navs = include $file->getPathName();
            if (is_array($navs)) {
                $permissions = array_merge($permissions, $this->getPermissions($navs));
            }
        }
        echo json_encode(array_filter($permissions));
    }
}
