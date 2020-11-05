<?php

/**
 * Description of HtmlPrinter
 *
 * @author Hery
 */
class CQC_Printer_HtmlPrinter extends CQC_PrinterAbstract {

    /**
     * Retrieve the relevant portion of the PHP source file with syntax highlighting
     *
     * @param   string      $fileName   path to the source file
     * @param   int         $midLine    line to show around
     * @param   int         $numLines   number of lines to show
     * @return  string                  highlighted source HTML formatted
     */
    protected function highlightSourceAround($fileName, $midLine, $numLines = true) {
        $offset = max(0, $midLine - ceil($numLines / 2));
        return $this->highlightSource($fileName, $offset, $numLines, $midLine);
    }

    /**
     * Retrieve the relevant portion of the PHP source file with syntax highlighting
     *
     * @param string        $fileName   path to the source file
     * @param int           $firstLine  first line number to show
     * @param int           $numLines   number of lines to show
     * @param int           $markLine   line number to mark if required
     * @return  string                  highlighted source HTML formatted
     */
    protected function highlightSource($fileName, $firstLine = 1, $numLines = null, $markLine = null) {
        if (!isset($this->source[$fileName])) {
            $lines = highlight_file($fileName, true);
            $lines = explode("<br />", $lines);
            $this->source[$fileName] = $lines;
        } else {
            $lines = $this->source[$fileName];
        }

        $lines = array_slice($lines, $firstLine - 1, $numLines);

        $html = '<table class="code" cellpadding="0" cellspacing="0" border="0">';
        $row = 0;
        $lineno = $firstLine;
        foreach ($lines as $line) {
            $html .= '<tr class="line' . ($lineno == $markLine ? ' hilite' : '') . ($row & 1 ? ' odd' : ' even') . '"><td class="linenum">' . $lineno . '</td><td class="linetxt"><span>' . $line . '</span></td></tr>';
            $lineno++;
            $row++;
        }
        $html .= '</table>';

        return $html;
    }

}
