<?php

class Controller_Demo_Elements_Shimmer extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Shimmer');

        $widget = $app->addWidget()->setTitle('Shimmer Demo');
        $divRow = $widget->addDiv()->addClass('row');
        $divRow->addDiv()->addClass('col-md-6')->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
            $b->col('col-12', function ($b) {
                $b->img()
                    ->row('', function ($b) {
                        $b->col('col-6 big')
                            ->col('col-4 empty big')
                            ->col('col-2 big')
                            ->col('col-4')
                            ->col('col-8 empty')
                            ->col('col-6')
                            ->col('col-6 empty')
                            ->col('col-12 empty');
                    });
            })->col('col-12', function ($b) {
                $b->img()
                    ->row('', function ($b) {
                        $b->col('col-6 big')
                            ->col('col-4 empty big')
                            ->col('col-2 big')
                            ->col('col-4')
                            ->col('col-8 empty')
                            ->col('col-6')
                            ->col('col-6 empty')
                            ->col('col-12 empty');
                    });
            });
        });
        $colRight = $divRow->addDiv()->addClass('col-md-6');
        $colRight->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
            $b->col('col-12', function ($b) {
                $b->row('', function ($b) {
                    $b->col('col-12')
                        ->col('col-8')
                        ->col('col-4 empty')
                        ->col('col-6')
                        ->col('col-6 empty')
                        ->col('col-12 empty')
                        ->col('col-10 empty')
                        ->col('col-2');
                });
            });
        });

        $colRight->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
            $b->col('col-6', function ($b) {
                $b->avatar('');
            })->col('col-6')
                ->spacing()
                ->col('col-12', function ($b) {
                    $b->row('', function ($b) {
                        $b->col('col-12')
                            ->col('col-8')
                            ->col('col-4 empty')
                            ->col('col-6')
                            ->col('col-6 empty')
                            ->col('col-12');
                    });
                });
        });

        return $app;
    }
}
