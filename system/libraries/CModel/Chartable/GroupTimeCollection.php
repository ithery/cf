<?php
class CModel_Chartable_GroupTimeCollection extends CCollection {
    public function toChart($callback = null) {
        $labels = [];
        $datasets = [];

        foreach ($this as $groupKey => $timeCollection) {
            /** @var CModel_Chartable_TimeCollection */
            $chart = $timeCollection->toChart();

            $labels = $chart['labels']; // ambil sekali saja

            $data = [
                'label' => (string) $groupKey,
                'data' => $chart['values'],
                'borderWidth' => 1,
            ];
            if ($callback != null && c::isCallable($callback)) {
                $data = c::call($callback, [$data, $groupKey]);
            }
            $datasets[] = $data;
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
