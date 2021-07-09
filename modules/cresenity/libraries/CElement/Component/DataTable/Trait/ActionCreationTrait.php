<?php

trait CElement_Component_DataTable_Trait_ActionCreationTrait {
    public function createExportAction($options) {
        $id = carr::get($options, 'id');
        $options['action'] = CExporter::ACTION_STORE;
        $act = CElement_Factory::createComponent('Action', $id)->setLabel('Export');

        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('DataTableExporter');
        $ajaxMethod->setData('table', serialize($this));
        $ajaxMethod->setData('exporter', $options);
        $downloadUrl = $ajaxMethod->makeUrl();

        $act->setLink($downloadUrl)->setLinkTarget('_blank');

        return $act;
    }

    public function createDownloadAction($options = []) {
        $id = carr::get($options, 'id');

        $options['action'] = CExporter::ACTION_DOWNLOAD;
        $act = CElement_Factory::createComponent('Action', $id)->setLabel('Export');

        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('DataTableExporter');
        $ajaxMethod->setData('table', serialize($this));
        $ajaxMethod->setData('exporter', $options);
        $downloadUrl = $ajaxMethod->makeUrl();

        $act->setLink($downloadUrl)->setLinkTarget('_blank');

        return $act;
    }

    public function createDownloadProgressAction($options = []) {
        $id = carr::get($options, 'id');

        $options['action'] = CExporter::ACTION_DOWNLOAD;
        $options['queued'] = true;
        $act = CElement_Factory::createComponent('Action', $id)->setLabel('Export');

        $disk = carr::get($options, 'disk');
        $filename = carr::get($options, 'filename');

        $fileUrl = CStorage::instance()->disk($disk)->url($filename);

        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('DataTableExporter');
        $ajaxMethod->setData('table', serialize($this));
        $ajaxMethod->setData('exporter', $options);
        $ajaxMethod->setData('progress', true);
        $ajaxMethod->setData('state', 'PENDING');
        $ajaxMethod->setData('progressValue', '0');
        $ajaxMethod->setData('progressMax', '100');
        $ajaxMethod->setData('fileUrl', $fileUrl);
        $downloadUrl = $ajaxMethod->makeUrl();

        $act->addListener('click')->addHandler('downloadProgress')->setUrl($downloadUrl);

        return $act;
    }
}
