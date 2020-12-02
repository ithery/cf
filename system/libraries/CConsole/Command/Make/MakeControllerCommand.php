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
        $controller = $this->argument('controller');
        $controller = str_replace('/', '.', $controller);
        $controllers = explode('.', $controller);
        $controllerPath = c::fixPath(CF::appDir()) . 'default' . DS . 'controllers' . DS;

        $file = array_pop($controllers);
        foreach ($controllers as $segment) {
            $controllerPath .= $segment . DS;
            if (!CFile::isDirectory($controllerPath)) {
                CFile::makeDirectory($controllerPath);
            }
        }
        $controllerFile = $controllerPath . $file.EXT;
        
        $content = CF::FILE_SECURITY.PHP_EOL.PHP_EOL;
        CFile::put($controllerFile,$content);

        $this->info($controller . ' created on:' . $controllerFile);
    }

}
