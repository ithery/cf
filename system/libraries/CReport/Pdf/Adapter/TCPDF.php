<?php

//@codingStandardsIgnoreStart
class CReport_Pdf_Adapter_TCPDF extends \TCPDF {
    protected $producer;

    protected $isXmpEnabled = true;

    public function setProducer($producer) {
        $this->producer = $producer;
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

    /**
     * Put XMP data object and return ID.
     *
     * @return int the object ID
     *
     * @since 5.9.121 (2011-09-28)
     * @protected
     */
    protected function _putXMP() {
        $oid = $this->_newobj();
        // store current isunicode value
        $prev_isunicode = $this->isunicode;
        $this->isunicode = true;
        $prev_encrypted = $this->encrypted;
        $this->encrypted = false;
        // set XMP data
        $xmp = '<?xpacket begin="' . TCPDF_FONTS::unichr(0xfeff, $this->isunicode) . '" id="W5M0MpCehiHzreSzNTczkc9d"?>' . "\n";
        $xmp .= '<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 4.2.1-c043 52.372728, 2009/01/18-15:08:04">' . "\n";
        if ($this->isXmpEnabled) {
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
        }
        $xmp .= '</x:xmpmeta>' . "\n";
        $xmp .= '<?xpacket end="w"?>';
        $out = '<< /Type /Metadata /Subtype /XML /Length ' . strlen($xmp) . ' >> stream' . "\n" . $xmp . "\n" . 'endstream' . "\n" . 'endobj';
        // restore previous isunicode value
        $this->isunicode = $prev_isunicode;
        $this->encrypted = $prev_encrypted;
        $this->_out($out);

        return $oid;
    }
}
