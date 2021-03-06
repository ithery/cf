<?php

/*
 * (c) Markus Lanthaler <mail@markus-lanthaler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ML\JsonLD;

use ML\IRI\IRI;

/**
 * JSON-LD document interface
 *
 * @author Markus Lanthaler <mail@markus-lanthaler.com>
 */
interface DocumentInterface {
    /**
     * Set the document's IRI
     *
     * @param string|IRI the IRI
     * @param mixed $iri
     *
     * @return self
     */
    public function setIri($iri);

    /**
     * Get the document's IRI
     *
     * @param bool $asObject if set to true, the return value will be an
     *                       {@link IRI} object; otherwise a string
     *
     * @return string|IRI the document's IRI (might be empty)
     */
    public function getIri($asObject = false);

    /**
     * Creates a new graph which is linked to this document
     *
     * If there exists already a graph with the passed name in the document,
     * that graph will be returned instead of creating a new one.
     *
     * @param string|IRI $name the graph's name
     *
     * @return GraphInterface the newly created graph
     */
    public function createGraph($name);

    /**
     * Get a graph by name
     *
     * @param null|string $name The name of the graph to retrieve. If null
     *                          is passed, the default will be returned.
     *
     * @return GraphInterface|null returns the graph if found; null otherwise
     */
    public function getGraph($name = null);

    /**
     * Get graph names
     *
     * @return string[] returns the names of all graphs in the document
     */
    public function getGraphNames();

    /**
     * Check whether the document contains a graph with the specified name
     *
     * @param string $name the graph name
     *
     * @return bool returns true if the document contains a graph with the
     *              specified name; false otherwise
     */
    public function containsGraph($name);

    /**
     * Removes a graph from the document
     *
     * @param null|string|GraphInterface $graph The graph (or its name) to
     *                                          remove. If null is passed,
     *                                          the default will be reset.
     *
     * @return self
     */
    public function removeGraph($graph = null);
}
