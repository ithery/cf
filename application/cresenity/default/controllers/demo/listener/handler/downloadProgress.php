<?php

class Controller_Demo_Listener_Handler_DownloadProgress extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->setTitle('Download Progress');

        $app->addDiv()->addClass('mb-3')->add(
            'This demo simulates a file download with a progress indicator. '
            . 'Click the button below to start a simulated download that takes about 15 seconds.'
        );

        $action = $app->addAction()->setLabel('Download File')->addClass('btn btn-primary');
        $action->onClickListener()->addDownloadProgressHandler()
            ->setUrl($this->controllerUrl() . 'start');

        return $app;
    }

    public function start() {
        $downloadId = uniqid('demo_dl_', true);

        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('DataTableExporterProgress');
        $ajaxMethod->setData('downloadId', $downloadId);

        $totalSteps = 5;
        CAjax::setData($downloadId, [
            'data' => [
                'progressValue' => 0,
                'progressMax' => $totalSteps,
                'state' => 'PENDING',
                'fileUrl' => null,
                'step' => 0,
                'totalSteps' => $totalSteps,
            ],
        ]);

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => [
                'progressUrl' => $this->controllerUrl() . 'progress/' . $downloadId,
            ],
        ]);
    }

    public function progress($downloadId = null) {
        if (!$downloadId) {
            return c::response()->json([
                'errCode' => 1,
                'errMessage' => 'Invalid download ID',
            ]);
        }

        try {
            $data = CAjax::getData($downloadId);
        } catch (\Exception $e) { // @phpstan-ignore-line
            return c::response()->json([
                'errCode' => 1,
                'errMessage' => 'Download not found',
            ]);
        }

        $progressData = carr::get($data, 'data', []);
        $step = carr::get($progressData, 'step', 0);
        $totalSteps = carr::get($progressData, 'totalSteps', 5);

        $step++;

        if ($step >= $totalSteps) {
            $progressData['state'] = 'DONE';
            $progressData['progressValue'] = $totalSteps;
            $progressData['fileUrl'] = c::url('media/img/favico.png');
        } else {
            $progressData['state'] = 'PENDING';
            $progressData['progressValue'] = $step;
            $progressData['step'] = $step;
        }

        CAjax::setData($downloadId, ['data' => $progressData]);

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $progressData,
        ]);
    }
}
