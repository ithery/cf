<?php

/**
 * @Usage : $arr = Xml::createArray($xml);
 */
class CVendor_Namecheap_Xml {
    private static $xml = null;

    private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional].
     *
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Convert an XML string or DOMDocument to an Array.
     *
     * @param $input_xml string|DOMDocument
     *
     * @throws Exception
     *
     * @return array
     */
    public static function &createArray($input_xml) {
        $xml = self::getXMLRoot();
        if (is_string($input_xml)) {
            $parsed = $xml->loadXML($input_xml);
            if (!$parsed) {
                throw new Exception('[XML2Array] Error parsing the XML string.');
            }
        } else {
            if (!$input_xml instanceof DOMDocument) {
                throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
            }
            $xml = self::$xml = $input_xml;
        }
        $array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.

        return $array;
    }

    /**
     * Convert an Array to XML.
     *
     * @param mixed $node - XML as a string or as an object of DOMDocument
     *
     * @return mixed
     */
    private static function &convert($node) {
        $output = [];
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);

                break;
            case XML_ELEMENT_NODE:
                // for each child node, call the covert function recursively
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::convert($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }
                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }
                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = [];
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output) && !empty($output)) {
                        $output = ['__text' => $output];
                        if (count($a)) {
                            foreach ($a as $kk => $vv) {
                                $output['_' . $kk] = $vv;
                            }
                        }
                    } else {
                        if (is_string($output) && empty($output)) {
                            $output = [];
                        }

                        foreach ($a as $k => $v) {
                            $output['_' . $k] = $v;
                        }
                    }
                }

                break;
        }

        return $output;
    }

    /**
     * Get the root XML node, if there isn't one, create it.
     *
     * @return DOMDocument
     */
    private static function getXMLRoot() {
        if (empty(self::$xml)) {
            self::init();
        }

        return self::$xml;
    }
}
