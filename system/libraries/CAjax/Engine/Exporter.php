<?php
class CAjax_Engine_Exporter extends CAjax_Engine {
    public function execute() {
        $data = $this->ajaxMethod->getData();

        $exporter = carr::get($data, 'exporter');
        $filename = carr::get($data, 'filename');
        $writerType = carr::get($data, 'writerType');
        $headers = carr::get($data, 'headers', []);
        $exporter = unserialize($exporter);

        return CExporter::download($exporter, $filename, $writerType, $headers);
    }
}
