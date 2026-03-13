<?php

//@codingStandardsIgnoreStart
class CReport_Adapter_Pdf_TCPDF extends \TCPDF {
    protected $producer;

    protected $isXmpEnabled = true;

    protected $overwriteXmp = null;

    protected $xmpToolkit = null;

    /**
     * This is the class constructor.
     * It allows to set up the page format, the orientation and the measure unit used in all the methods (except for the font sizes).
     *
     * @param string    $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
     * @param string    $unit        User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
     * @param mixed     $format      The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
     * @param bool      $unicode     TRUE means that the input text is unicode (default = true)
     * @param string    $encoding    charset encoding (used only when converting back html entities); default is UTF-8
     * @param bool      $diskcache   DEPRECATED FEATURE
     * @param false|int $pdfa        if not false, set the document to PDF/A mode and the good version (1 or 3)
     * @public
     *
     * @see getPageSizeFromFormat(), setPageFormat()
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
        // set file ID for trailer
        $serformat = (is_array($format) ? json_encode($format) : $format);
        $this->file_id = md5(TCPDF_STATIC::getRandomSeed('TCPDF' . $orientation . $unit . $serformat . $encoding));
        $this->hash_key = hash_hmac('sha256', TCPDF_STATIC::getRandomSeed($this->file_id), TCPDF_STATIC::getRandomSeed('TCPDF'), false);
        $this->font_obj_ids = [];
        $this->page_obj_id = [];
        $this->form_obj_id = [];
        // set pdf/a mode
        if ($pdfa != false) {
            $this->pdfa_mode = true;
            $this->pdfa_version = $pdfa;  // 1 or 3
        } else {
            $this->pdfa_mode = false;
        }

        $this->force_srgb = false;
        // set language direction
        $this->rtl = false;
        $this->tmprtl = false;
        // some checks
        $this->_dochecks();
        // initialization of properties
        $this->isunicode = $unicode;
        $this->page = 0;
        $this->transfmrk[0] = [];
        $this->pagedim = [];
        $this->n = 2;
        $this->buffer = '';
        $this->pages = [];
        $this->state = 0;
        $this->fonts = [];
        $this->FontFiles = [];
        $this->diffs = [];
        $this->images = [];
        $this->links = [];
        $this->gradients = [];
        $this->InFooter = false;
        $this->lasth = 0;
        $this->FontFamily = defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'helvetica';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->underline = false;
        $this->overline = false;
        $this->linethrough = false;
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
        $this->pdflayers = [];
        // encryption values
        $this->encrypted = false;
        $this->last_enc_key = '';
        // standard Unicode fonts
        $this->CoreFonts = [
            'courier' => 'Courier',
            'courierB' => 'Courier-Bold',
            'courierI' => 'Courier-Oblique',
            'courierBI' => 'Courier-BoldOblique',
            'helvetica' => 'Helvetica',
            'helveticaB' => 'Helvetica-Bold',
            'helveticaI' => 'Helvetica-Oblique',
            'helveticaBI' => 'Helvetica-BoldOblique',
            'times' => 'Times-Roman',
            'timesB' => 'Times-Bold',
            'timesI' => 'Times-Italic',
            'timesBI' => 'Times-BoldItalic',
            'symbol' => 'Symbol',
            'zapfdingbats' => 'ZapfDingbats'
        ];
        // set scale factor
        $this->setPageUnit($unit);
        // set page format and orientation
        $this->setPageFormat($format, $orientation);
        // page margins (1 cm)
        $margin = 28.35 / $this->k;
        $this->setMargins($margin, $margin);
        $this->clMargin = $this->lMargin;
        $this->crMargin = $this->rMargin;
        // internal cell padding
        $cpadding = $margin / 10;
        $this->setCellPaddings($cpadding, 0, $cpadding, 0);
        // cell margins
        $this->setCellMargins(0, 0, 0, 0);
        // line width (0.2 mm)
        $this->LineWidth = 0.57 / $this->k;
        $this->linestyleWidth = sprintf('%F w', ($this->LineWidth * $this->k));
        $this->linestyleCap = '0 J';
        $this->linestyleJoin = '0 j';
        $this->linestyleDash = '[] 0 d';
        // automatic page break
        $this->setAutoPageBreak(true, (2 * $margin));
        // full width display mode
        $this->setDisplayMode('fullwidth');
        // compression
        $this->setCompression();
        // set default PDF version number
        $this->setPDFVersion();
        $this->tcpdflink = true;
        $this->encoding = $encoding;
        $this->HREF = [];
        $this->getFontsList();
        $this->fgcolor = ['R' => 0, 'G' => 0, 'B' => 0];
        $this->strokecolor = ['R' => 0, 'G' => 0, 'B' => 0];
        $this->bgcolor = ['R' => 255, 'G' => 255, 'B' => 255];
        $this->extgstates = [];
        $this->setTextShadow();
        // signature
        $this->sign = false;
        $this->tsa_timestamp = false;
        $this->tsa_data = [];
        $this->signature_appearance = ['page' => 1, 'rect' => '0 0 0 0', 'name' => 'Signature'];
        $this->empty_signature_appearance = [];
        // user's rights
        $this->ur['enabled'] = false;
        $this->ur['document'] = '/FullSave';
        $this->ur['annots'] = '/Create/Delete/Modify/Copy/Import/Export';
        $this->ur['form'] = '/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate';
        $this->ur['signature'] = '/Modify';
        $this->ur['ef'] = '/Create/Delete/Modify/Import';
        $this->ur['formex'] = '';
        // set default JPEG quality
        $this->jpeg_quality = 75;
        // initialize some settings
        TCPDF_FONTS::utf8Bidi([], '', false, $this->isunicode, $this->CurrentFont);
        // set default font
        // $this->setFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
        $this->setHeaderFont([$this->FontFamily, $this->FontStyle, $this->FontSizePt]);
        $this->setFooterFont([$this->FontFamily, $this->FontStyle, $this->FontSizePt]);
        // check if PCRE Unicode support is enabled
        if ($this->isunicode and (@preg_match('/\pL/u', 'a') == 1)) {
            // PCRE unicode support is turned ON
            // \s     : any whitespace character
            // \p{Z}  : any separator
            // \p{Lo} : Unicode letter or ideograph that does not have lowercase and uppercase variants. Is used to chunk chinese words.
            // \xa0   : Unicode Character 'NO-BREAK SPACE' (U+00A0)
            //$this->setSpacesRE('/(?!\xa0)[\s\p{Z}\p{Lo}]/u');
            $this->setSpacesRE('/(?!\xa0)[\s\p{Z}]/u');
        } else {
            // PCRE unicode support is turned OFF
            $this->setSpacesRE('/[^\S\xa0]/');
        }
        $this->default_form_prop = ['lineWidth' => 1, 'borderStyle' => 'solid', 'fillColor' => [255, 255, 255], 'strokeColor' => [128, 128, 128]];
        // set document creation and modification timestamp
        $this->doc_creation_timestamp = time();
        $this->doc_modification_timestamp = $this->doc_creation_timestamp;
        // get default graphic vars
        $this->default_graphic_vars = $this->getGraphicVars();
        $this->header_xobj_autoreset = false;
        $this->custom_xmp = '';
        $this->custom_xmp_rdf = '';
    }

    public function setProducer($producer) {
        $this->producer = $producer;
    }

    public function setXmpToolkit($xmpToolkit) {
        $this->xmpToolkit = $xmpToolkit;

        return $this;
    }

    public function disableXmp() {
        $this->isXmpEnabled = false;
    }

    /**
     * Adds some Metadata information (Document Information Dictionary)
     * (see Chapter 14.3.3 Document Information Dictionary of PDF32000_2008.pdf Reference).
     *
     * @return int object id
     * @protected
     */
    protected function _putinfo() {
        $oid = $this->_newobj();
        $out = '<<';
        // store current isunicode value
        $prev_isunicode = $this->isunicode;
        if ($this->docinfounicode) {
            $this->isunicode = true;
        }
        if (!TCPDF_STATIC::empty_string($this->title)) {
            // The document's title.
            $out .= ' /Title ' . $this->_textstring($this->title, $oid);
        }
        if (!TCPDF_STATIC::empty_string($this->author)) {
            // The name of the person who created the document.
            $out .= ' /Author ' . $this->_textstring($this->author, $oid);
        }
        if (!TCPDF_STATIC::empty_string($this->subject)) {
            // The subject of the document.
            $out .= ' /Subject ' . $this->_textstring($this->subject, $oid);
        }
        if (!TCPDF_STATIC::empty_string($this->keywords)) {
            // Keywords associated with the document.
            $out .= ' /Keywords ' . $this->_textstring($this->keywords, $oid);
        }
        if (!TCPDF_STATIC::empty_string($this->creator)) {
            // If the document was converted to PDF from another format, the name of the conforming product that created the original document from which it was converted.
            $out .= ' /Creator ' . $this->_textstring($this->creator, $oid);
        }
        // restore previous isunicode value
        $this->isunicode = $prev_isunicode;
        // default producer
        $out .= ' /Producer ' . $this->_textstring($this->producer ?: TCPDF_STATIC::getTCPDFProducer(), $oid);
        // The date and time the document was created, in human-readable form
        $out .= ' /CreationDate ' . $this->_datestring(0, $this->doc_creation_timestamp);
        // The date and time the document was most recently modified, in human-readable form
        $out .= ' /ModDate ' . $this->_datestring(0, $this->doc_modification_timestamp);
        // A name object indicating whether the document has been modified to include trapping information
        // $out .= ' /Trapped /False';
        $out .= ' >>';
        $out .= "\n" . 'endobj';
        $this->_out($out);

        return $oid;
    }

    public function setOverwriteXmp($overwriteXmp) {
        $this->overwriteXmp = $overwriteXmp;

        return $this;
    }

    /**
     * Put XMP data object and return ID.
     *
     * @return int the object ID
     *
     * @since 5.9.121 (2011-09-28)
     * @protected
     */
    protected function _putXMP() {
        $xmpToolkit = $this->xmpToolkit ?: 'Adobe XMP Core 4.2.1-c043 52.372728, 2009/01/18-15:08:04';

        $oid = $this->_newobj();
        // store current isunicode value
        $prev_isunicode = $this->isunicode;
        $this->isunicode = true;
        $prev_encrypted = $this->encrypted;
        $this->encrypted = false;
        // set XMP data
        $xmp = '<?xpacket begin="' . TCPDF_FONTS::unichr(0xfeff, $this->isunicode) . '" id="W5M0MpCehiHzreSzNTczkc9d"?>' . "\n";
        if ($this->isXmpEnabled) {
            if ($this->overwriteXmp) {
                $xmp .= $this->overwriteXmp;
            } else {
                $xmp .= '<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="' . $xmpToolkit . '">' . "\n";
                $xmp .= "\t" . '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";
                $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:dc="http://purl.org/dc/elements/1.1/">' . "\n";
                $xmp .= "\t\t\t" . '<dc:format>application/pdf</dc:format>' . "\n";
                $xmp .= "\t\t\t" . '<dc:title>' . "\n";
                $xmp .= "\t\t\t\t" . '<rdf:Alt>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li xml:lang="x-default">' . TCPDF_STATIC::_escapeXML($this->title) . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t" . '</rdf:Alt>' . "\n";
                $xmp .= "\t\t\t" . '</dc:title>' . "\n";
                $xmp .= "\t\t\t" . '<dc:creator>' . "\n";
                $xmp .= "\t\t\t\t" . '<rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li>' . TCPDF_STATIC::_escapeXML($this->author) . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t" . '</rdf:Seq>' . "\n";
                $xmp .= "\t\t\t" . '</dc:creator>' . "\n";
                $xmp .= "\t\t\t" . '<dc:description>' . "\n";
                $xmp .= "\t\t\t\t" . '<rdf:Alt>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li xml:lang="x-default">' . TCPDF_STATIC::_escapeXML($this->subject) . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t" . '</rdf:Alt>' . "\n";
                $xmp .= "\t\t\t" . '</dc:description>' . "\n";
                $xmp .= "\t\t\t" . '<dc:subject>' . "\n";
                $xmp .= "\t\t\t\t" . '<rdf:Bag>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li>' . TCPDF_STATIC::_escapeXML($this->keywords) . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t" . '</rdf:Bag>' . "\n";
                $xmp .= "\t\t\t" . '</dc:subject>' . "\n";
                $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                // convert doc creation date format
                $dcdate = TCPDF_STATIC::getFormattedDate($this->doc_creation_timestamp);
                $doccreationdate = substr($dcdate, 0, 4) . '-' . substr($dcdate, 4, 2) . '-' . substr($dcdate, 6, 2);
                $doccreationdate .= 'T' . substr($dcdate, 8, 2) . ':' . substr($dcdate, 10, 2) . ':' . substr($dcdate, 12, 2);
                $doccreationdate .= substr($dcdate, 14, 3) . ':' . substr($dcdate, 18, 2);
                $doccreationdate = TCPDF_STATIC::_escapeXML($doccreationdate);
                // convert doc modification date format
                $dmdate = TCPDF_STATIC::getFormattedDate($this->doc_modification_timestamp);
                $docmoddate = substr($dmdate, 0, 4) . '-' . substr($dmdate, 4, 2) . '-' . substr($dmdate, 6, 2);
                $docmoddate .= 'T' . substr($dmdate, 8, 2) . ':' . substr($dmdate, 10, 2) . ':' . substr($dmdate, 12, 2);
                $docmoddate .= substr($dmdate, 14, 3) . ':' . substr($dmdate, 18, 2);
                $docmoddate = TCPDF_STATIC::_escapeXML($docmoddate);
                $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:xmp="http://ns.adobe.com/xap/1.0/">' . "\n";
                $xmp .= "\t\t\t" . '<xmp:CreateDate>' . $doccreationdate . '</xmp:CreateDate>' . "\n";
                $xmp .= "\t\t\t" . '<xmp:CreatorTool>' . $this->creator . '</xmp:CreatorTool>' . "\n";
                $xmp .= "\t\t\t" . '<xmp:ModifyDate>' . $docmoddate . '</xmp:ModifyDate>' . "\n";
                $xmp .= "\t\t\t" . '<xmp:MetadataDate>' . $doccreationdate . '</xmp:MetadataDate>' . "\n";
                $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:pdf="http://ns.adobe.com/pdf/1.3/">' . "\n";
                $xmp .= "\t\t\t" . '<pdf:Keywords>' . TCPDF_STATIC::_escapeXML($this->keywords) . '</pdf:Keywords>' . "\n";
                $xmp .= "\t\t\t" . '<pdf:Producer>' . TCPDF_STATIC::_escapeXML($this->producer ?: TCPDF_STATIC::getTCPDFProducer()) . '</pdf:Producer>' . "\n";
                $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:xmpMM="http://ns.adobe.com/xap/1.0/mm/">' . "\n";
                $uuid = 'uuid:' . substr($this->file_id, 0, 8) . '-' . substr($this->file_id, 8, 4) . '-' . substr($this->file_id, 12, 4) . '-' . substr($this->file_id, 16, 4) . '-' . substr($this->file_id, 20, 12);
                $xmp .= "\t\t\t" . '<xmpMM:DocumentID>' . $uuid . '</xmpMM:DocumentID>' . "\n";
                $xmp .= "\t\t\t" . '<xmpMM:InstanceID>' . $uuid . '</xmpMM:InstanceID>' . "\n";
                $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                if ($this->pdfa_mode) {
                    $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:pdfaid="http://www.aiim.org/pdfa/ns/id/">' . "\n";
                    $xmp .= "\t\t\t" . '<pdfaid:part>' . $this->pdfa_version . '</pdfaid:part>' . "\n";
                    $xmp .= "\t\t\t" . '<pdfaid:conformance>B</pdfaid:conformance>' . "\n";
                    $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                }
                // XMP extension schemas
                $xmp .= "\t\t" . '<rdf:Description rdf:about="" xmlns:pdfaExtension="http://www.aiim.org/pdfa/ns/extension/" xmlns:pdfaSchema="http://www.aiim.org/pdfa/ns/schema#" xmlns:pdfaProperty="http://www.aiim.org/pdfa/ns/property#">' . "\n";
                $xmp .= "\t\t\t" . '<pdfaExtension:schemas>' . "\n";
                $xmp .= "\t\t\t\t" . '<rdf:Bag>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:namespaceURI>http://ns.adobe.com/pdf/1.3/</pdfaSchema:namespaceURI>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:prefix>pdf</pdfaSchema:prefix>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:schema>Adobe PDF Schema</pdfaSchema:schema>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '<rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:category>internal</pdfaProperty:category>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:description>Adobe PDF Schema</pdfaProperty:description>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:name>InstanceID</pdfaProperty:name>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:valueType>URI</pdfaProperty:valueType>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '</rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '</pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:namespaceURI>http://ns.adobe.com/xap/1.0/mm/</pdfaSchema:namespaceURI>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:prefix>xmpMM</pdfaSchema:prefix>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:schema>XMP Media Management Schema</pdfaSchema:schema>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '<rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:category>internal</pdfaProperty:category>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:description>UUID based identifier for specific incarnation of a document</pdfaProperty:description>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:name>InstanceID</pdfaProperty:name>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:valueType>URI</pdfaProperty:valueType>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '</rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '</pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:namespaceURI>http://www.aiim.org/pdfa/ns/id/</pdfaSchema:namespaceURI>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:prefix>pdfaid</pdfaSchema:prefix>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:schema>PDF/A ID Schema</pdfaSchema:schema>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '<pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '<rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:category>internal</pdfaProperty:category>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:description>Part of PDF/A standard</pdfaProperty:description>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:name>part</pdfaProperty:name>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:valueType>Integer</pdfaProperty:valueType>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:category>internal</pdfaProperty:category>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:description>Amendment of PDF/A standard</pdfaProperty:description>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:name>amd</pdfaProperty:name>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:valueType>Text</pdfaProperty:valueType>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '<rdf:li rdf:parseType="Resource">' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:category>internal</pdfaProperty:category>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:description>Conformance level of PDF/A standard</pdfaProperty:description>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:name>conformance</pdfaProperty:name>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t\t" . '<pdfaProperty:valueType>Text</pdfaProperty:valueType>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t\t\t\t" . '</rdf:Seq>' . "\n";
                $xmp .= "\t\t\t\t\t\t" . '</pdfaSchema:property>' . "\n";
                $xmp .= "\t\t\t\t\t" . '</rdf:li>' . "\n";
                $xmp .= "\t\t\t\t" . '</rdf:Bag>' . "\n";
                $xmp .= "\t\t\t" . '</pdfaExtension:schemas>' . "\n";
                $xmp .= "\t\t" . '</rdf:Description>' . "\n";
                $xmp .= $this->custom_xmp_rdf;
                $xmp .= "\t" . '</rdf:RDF>' . "\n";
                $xmp .= $this->custom_xmp;
                $xmp .= '</x:xmpmeta>' . "\n";
            }
        }

        $xmp .= '<?xpacket end="w"?>';
        $out = '<< /Type /Metadata /Subtype /XML /Length ' . strlen($xmp) . ' >> stream' . "\n" . $xmp . "\n" . 'endstream' . "\n" . 'endobj';
        // restore previous isunicode value
        $this->isunicode = $prev_isunicode;
        $this->encrypted = $prev_encrypted;
        $this->_out($out);

        return $oid;
    }

    /**
     * Terminates the PDF document.
     * It is not necessary to call this method explicitly because Output() does it automatically.
     * If the document contains no page, AddPage() is called to prevent from getting an invalid document.
     *
     * @public
     *
     * @since 1.0
     * @see Open(), Output()
     */
    public function Close() {
        if ($this->state == 3) {
            return;
        }
        if ($this->page == 0) {
            $this->AddPage();
        }
        $this->endLayer();
        if ($this->tcpdflink) {
            // save current graphic settings
            $gvars = $this->getGraphicVars();
            $this->setEqualColumns();
            $this->lastpage(true);
            $this->setAutoPageBreak(false);
            $this->x = 0;
            $this->y = $this->h - (1 / $this->k);
            $this->lMargin = 0;
            $this->_outSaveGraphicsState();
            // $font = defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'helvetica';
            // $this->setFont($font, '', 1);
            $this->setTextRenderingMode(0, false, false);
            // $msg = "\x50\x6f\x77\x65\x72\x65\x64\x20\x62\x79\x20\x54\x43\x50\x44\x46\x20\x28\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67\x29";
            // $lnk = "\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67";

            // $this->Cell(0, 0, $msg, 0, 0, 'L', 0, $lnk, 0, false, 'D', 'B');
            $this->_outRestoreGraphicsState();
            // restore graphic settings
            $this->setGraphicVars($gvars);
        }
        // close page
        $this->endPage();
        // close document
        $this->_enddoc();
        // unset all class variables (except critical ones)
        $this->_destroy(false);
    }

    public function raw($s) {
        $this->_out($s);
    }

    public function setLayoutMode($mode) {
        $this->LayoutMode = $mode;

        return $this;
    }

    public function setPageMode($mode) {
        $this->PageMode = $mode;

        return $this;
    }
}
