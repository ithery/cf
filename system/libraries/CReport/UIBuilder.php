<?php

/**
 * Visual designer for CReport jrxml definition, JasperReports Studio like UI.
 *
 * The UI only exposes features supported by the CReport builder/generator:
 * bands (title, pageHeader, columnHeader, detail, columnFooter, pageFooter, summary),
 * groups with header/footer, variables, and staticText/textField/line/rectangle/image elements.
 */
class CReport_UIBuilder {
    /**
     * View path of the builder UI.
     *
     * @var string
     */
    const VIEW = 'cresenity.report.ui-builder';

    /**
     * Create the builder UI view.
     *
     * @param array $data optional view data, e.g. ['jrxml' => $initialJrxml]
     *
     * @return CView_View
     */
    public static function view(array $data = []) {
        if (!array_key_exists('jrxml', $data)) {
            $data['jrxml'] = null;
        }

        return c::view(self::VIEW, $data);
    }
}
