<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_OptimizerChainFactory {

    /**
     * 
     * @return CImage_OptimizerChain
     */
    public static function create() {
        return (new CImage_OptimizerChain())
                        ->addOptimizer(new CImage_Optimizer_Jpegoptim([
                            '-m85',
                            '--strip-all',
                            '--all-progressive',
                        ]))
                        ->addOptimizer(new CImage_Optimizer_Pngquant([
                            '--force',
                        ]))
                        ->addOptimizer(new CImage_Optimizer_Optipng([
                            '-i0',
                            '-o2',
                            '-quiet',
                        ]))
                        ->addOptimizer(new CImage_Optimizer_Svgo([
                            '--disable={cleanupIDs,removeViewBox}',
                        ]))
                        ->addOptimizer(new CImage_Optimizer_Gifsicle([
                            '-b',
                            '-O3',
                        ]))
                        ->addOptimizer(new CImage_Optimizer_Cwebp([
                            '-m 6',
                            '-pass 10',
                            '-mt',
                            '-q 80',
        ]));
    }

}
