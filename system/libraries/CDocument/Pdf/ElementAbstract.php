<?php

abstract class CDocument_Pdf_ElementAbstract {
    /**
     * Processes the element by adding it (or the different parts) to an <CODE> ElementListener</CODE>.
     *
     * @param null|CEvent_Dispatcher
     *
     * @return bool
     */
    abstract public function process(CEvent_Dispatcher $event = null);

    /**
     * Gets the type of the text element.
     *
     * @return int
     */
    abstract public function type();

    /**
     * Checks if this element is a content object. If not, it's a metadata object.
     * return true if this is a 'content' element; false if this is a 'metadata' element.
     *
     * @return bool
     */
    abstract public function isContent();

    /**
     * Checks if this element is nestable.
     * return true if this element can be nested inside other elements.
     *
     * @return bool
     */
    abstract public function isNestable();

    /**
     * Gets all the chunks in this element.
     *
     * @return CDocument_Pdf_ElementAbstract[]
     */
    abstract public function getChunks();

    /**
     * Gets the content of the text element.
     *
     * @return string
     */
    abstract public function __toString();
}
