# Element - Shimmer
### Introduction

Element Shimmer digunakan untuk mempresentasikan ui loading

Contoh Sederhana untuk shimmer
```php
$app->addDiv()->addClass('row')->addDiv()->addClass('col-md-6')->addShimmer()->withBuilder(function (CElement_Component_Shimmer_Builder $b) {
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
```
