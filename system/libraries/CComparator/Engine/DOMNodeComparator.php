<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compares DOMNode instances for equality.
 */
class CComparator_Engine_DOMNodeComparator extends CComparator_Engine_ObjectComparator {

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return $expected instanceof \DOMNode && $actual instanceof \DOMNode;
    }

    /**
     * Asserts that two values are equal.
     *
     * @param mixed $expected     First value to compare
     * @param mixed $actual       Second value to compare
     * @param float $delta        Allowed numerical distance between two values to consider them equal
     * @param bool  $canonicalize Arrays are sorted before comparison when set to true
     * @param bool  $ignoreCase   Case is ignored when set to true
     * @param array $processed    List of already processed elements (used to prevent infinite recursion)
     *
     * @throws ComparisonFailure
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false, array &$processed = []) {
        $expectedAsString = $this->nodeToText($expected, true, $ignoreCase);
        $actualAsString = $this->nodeToText($actual, true, $ignoreCase);
        if ($expectedAsString !== $actualAsString) {
            $type = $expected instanceof \DOMDocument ? 'documents' : 'nodes';
            throw new CComparator_Exception_ComparisonFailureException(
            $expected, $actual, $expectedAsString, $actualAsString, false, \sprintf("Failed asserting that two DOM %s are equal.\n", $type)
            );
        }
    }

    /**
     * Returns the normalized, whitespace-cleaned, and indented textual
     * representation of a DOMNode.
     */
    private function nodeToText(DOMNode $node, $canonicalize, $ignoreCase) {
        if ($canonicalize) {
            $document = new \DOMDocument;
            @$document->loadXML($node->C14N());
            $node = $document;
        }
        $document = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
        $document->formatOutput = true;
        $document->normalizeDocument();
        $text = $node instanceof \DOMDocument ? $node->saveXML() : $document->saveXML($node);
        return $ignoreCase ? \strtolower($text) : $text;
    }

}
