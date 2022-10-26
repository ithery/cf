<?php

trait CTrait_Controller_Application_Model_Count {
    public function data() {
        $app = c::app();

        $dateStart = '';
        $dateEnd = '';

        $periodDefault = CPeriod::untilDateNow();
        if (strlen($dateStart) == 0) {
            $dateStart = $periodDefault->startDate;
        }
        if (strlen($dateEnd) == 0) {
            $dateEnd = $periodDefault->endDate;
        }

        $app->addField()
            ->addDateRangeDropdownButtonControl('date')
            ->setOpenDirection('right')
            ->setValueStart($dateStart)
            ->setValueEnd($dateEnd)
            ->onChangeListener()
            ->addReloadHandler()
            ->setUrl($this->controllerUrl() . 'reloadData')
            ->SetTarget('data-container')
            ->addParamInput(['date-start', 'date-end']);

        $divData = $app->addDiv('data-container');
        $this->reloadData($divData, [
            'date-start' => (string) $dateStart,
            'date-end' => (string) $dateEnd,
        ]);

        return $app;
    }

    public function reloadData($container = null, $options = null) {
        /** @var CApp $app */
        $app = $container ?? c::app();

        $request = $options ?? $_GET;

        $dateStart = c::get($request, 'date-start');
        $dateEnd = c::get($request, 'date-end');

        $cardContainer = $app->addDiv()->addClass('row');

        foreach ($this->models as $model) {
            $name = preg_replace('/(.*_)/', '', $model);
            $count = $model::where('created', '>=', $dateStart)->where('created', '<=', $dateEnd)->count();

            $div = $cardContainer->addDiv()
                ->addClass('col-sm-4 col-md-3 mb-4')
                ->addDiv()
                ->addClass('card card-small')
                ->addDiv()
                ->addClass('card-body')
                ->addDiv()
                ->addClass('d-flex align-items-center');

            $div->addSpan()->addClass('fas fa-database text-success display-4 mr-3');
            $datail = $div->addDiv();
            $datail->addDiv()->add($name)->setAttr('style', 'font-size:18px;font-weight:600');
            $datail->addDiv()->add(c::formatter()->formatNumber($count))->setAttr('style', 'font-size:14px;font-weight:400');
        }

        return $app;
    }
}
