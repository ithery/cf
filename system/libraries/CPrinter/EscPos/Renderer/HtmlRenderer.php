<?php

class CPrinter_EscPos_Renderer_HtmlRenderer extends CPrinter_EscPos_RendererAbstract {
    /**
     * @return string
     */
    public function render() {
        $data = $this->data;
        //replace initialize
        $data = str_replace(CPrinter_EscPos::ESC . '@', '', $data);

        //replace emphasis
        //$this->addCode(CPrinter_EscPos::ESC . 'E' . ($on ? chr(1) : chr(0)));
        $openEmphasis = CPrinter_EscPos::ESC . 'E' . chr(1);
        $closeEmphasis = CPrinter_EscPos::ESC . 'E' . chr(0);
        $regexEmphasis = '#' . $openEmphasis . '.+?' . $closeEmphasis . '#ims';
        $regexEmphasis = str_replace(chr(0), '\x00', $regexEmphasis);
        $data = preg_replace_callback($regexEmphasis, function ($matches) {
            $match = carr::get($matches, 0);
            $match = $this->replaceWithSpan($match, CPrinter_EscPos::ESC . 'E' . chr(1), CPrinter_EscPos::ESC . 'E' . chr(0), 'font-weight:bold');

            return $match;
        }, $data);

        //replace mode
        $allModes = CPrinter_EscPos::MODE_FONT_B | CPrinter_EscPos::MODE_EMPHASIZED | CPrinter_EscPos::MODE_DOUBLE_HEIGHT | CPrinter_EscPos::MODE_DOUBLE_WIDTH | CPrinter_EscPos::MODE_UNDERLINE;

        $data = $this->handleBarcode($data);

        return $data;
    }

    protected function handleBarcode($data) {
        $regexNotSupportsBarcodeB = '#' . CPrinter_EscPos::GS . 'k(.)(.+?)\x00#ims';

        $barcodeTypeMap = [
            CPrinter_EscPos::BARCODE_CODE39 => \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_39,
            CPrinter_EscPos::BARCODE_CODE128 => \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128,
            CPrinter_EscPos::BARCODE_CODE93 => \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_93,
            CPrinter_EscPos::BARCODE_CODABAR => \Picqer\Barcode\BarcodeGenerator::TYPE_CODABAR,
            CPrinter_EscPos::BARCODE_ITF => \Picqer\Barcode\BarcodeGenerator::TYPE_ITF_14,
            CPrinter_EscPos::BARCODE_UPCA => \Picqer\Barcode\BarcodeGenerator::TYPE_UPC_A,
            CPrinter_EscPos::BARCODE_UPCE => \Picqer\Barcode\BarcodeGenerator::TYPE_UPC_E,
            CPrinter_EscPos::BARCODE_JAN13 => \Picqer\Barcode\BarcodeGenerator::TYPE_EAN_13,
            CPrinter_EscPos::BARCODE_JAN8 => \Picqer\Barcode\BarcodeGenerator::TYPE_EAN_8,
        ];
        preg_match_all($regexNotSupportsBarcodeB, $data, $matches);
        $fulls = carr::get($matches, 0);
        foreach ($fulls as $index => $full) {
            $type = ord(carr::get($matches, '1.' . $index)) + 65;
            $content = carr::get($matches, '2.' . $index);
            $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            $pos = strpos($data, $full);

            $barcode = $generator->getBarcode($content, carr::get($barcodeTypeMap, $type), 2, 50);
            $data = str_replace($full, $barcode, $data);
        }
        $regexSupportsBarcodeB = '#' . CPrinter_EscPos::GS . 'k(.)(.)#ims';
        preg_match_all($regexSupportsBarcodeB, $data, $matches);
        $fulls = carr::get($matches, 0);
        foreach ($fulls as $index => $full) {
            $type = ord(carr::get($matches, '1.' . $index));
            $contentLength = ord(carr::get($matches, '2.' . $index));
            $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            $pos = strpos($data, $full);
            $content = substr($data, $pos + strlen($full), $contentLength);
            // cdbg::dd($content);
            // $black = [0, 0, 0];
            $fullWithContent = $full . $content;
            $barcode = $generator->getBarcode($content, carr::get($barcodeTypeMap, $type), 2, 50);
            $data = str_replace($fullWithContent, $barcode, $data);
        }

        // if (!$this->profile->getSupportsBarcodeB()) {
        //     // A simpler barcode command which supports fewer codes

        //     $this->connector->write(CPrinter_EscPos::GS . 'k' . chr($type - 65) . $content . CPrinter_EscPos::NUL);

        //     return;
        // }
        // // More advanced function B, used in preference
        // $this->connector->write(CPrinter_EscPos::GS . 'k' . chr($type) . chr(strlen($content)) . $content);
        return $data;
    }

    private function replaceWithSpan($string, $open, $close, $style = '') {
        $spanOpen = '<span';
        if ($style) {
            $spanOpen .= ' style="' . $style . '"';
        }
        $spanOpen .= '>';
        $spanClose = '</span>';
        $string = str_replace($open, $spanOpen, $string);
        $string = str_replace($close, $spanClose, $string);

        return $string;
    }
}
