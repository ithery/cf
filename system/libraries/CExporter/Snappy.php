<?php

class CExporter_Snappy {
    /**
     * @return CExporter_Snappy_PdfWrapper
     */
    public static function pdf() {
        $binary = CF::config('exporter.snappy.pdf.binary', '/usr/local/bin/wkhtmltopdf');
        $options = CF::config('exporter.snappy.pdf.options', []);
        $env = CF::config('exporter.snappy.pdf.env', []);
        $timeout = CF::config('exporter.snappy.pdf.timeout', false);

        $snappy = new \Knp\Snappy\Pdf($binary, $options, $env);
        if (false !== $timeout) {
            $snappy->setTimeout($timeout);
        }

        return new CExporter_Snappy_PdfWrapper($snappy);
    }
}
