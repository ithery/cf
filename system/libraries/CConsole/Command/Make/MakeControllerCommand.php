<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CConsole_Command_Make_MakeControllerCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:controller {controller}';

    public function handle() {
        CConsole::domainRequired($this);
        $controller = $this->argument('controller');
        $controller = str_replace('/', '.', $controller);
        $controllers = explode('.', $controller);
        $controllerPath = c::fixPath(CF::appDir()) . 'default' . DS . 'controllers' . DS;
        $controllerClass = 'Controller';
        if (!CFile::isDirectory($controllerPath)) {
            CFile::makeDirectory($controllerPath);
        }


        $file = array_pop($controllers);
        foreach ($controllers as $segment) {
            $controllerPath .= $segment . DS;
            if (!CFile::isDirectory($controllerPath)) {
                CFile::makeDirectory($controllerPath);
            }
            $controllerClass .= '_' . ucfirst($segment);
        }
        $controllerFile = $controllerPath . $file . EXT;

        if (file_exists($controllerFile)) {
            $this->info('Controller ' . $controller . ' already created, no changes');
            return CConsole::SUCCESS_EXIT;
        }
        $controllerClass .= '_' . ucfirst($file);
        $stubFile = CF::findFile('stubs', 'controller', true, 'stub');
        if (!$stubFile) {
            $this->error('controller stub not found');
            exit(1);
        }
        $content = CFile::get($stubFile);
        $content = str_replace('{ControllerClass}', $controllerClass, $content);
        CFile::put($controllerFile, $content);

        $this->info('Controller ' . $controller . ' created on:' . $controllerFile);
    }

}
