<?php

class CDocument_Pdf_Chunk {
    // public static membervariables

    /**
     * The character stand in for an image or a separator.
     */
    const OBJECT_REPLACEMENT_CHARACTER = "\ufffc";

    /**
     * Key for drawInterface of the Separator.
     *
     * @since 2.1.2
     */
    const SEPARATOR = 'SEPARATOR';

    // member variables
    /**
     * Key for drawInterface of the tab.
     *
     * @since 2.1.2
     */
    const TAB = 'TAB';

    /**
     * Key for text horizontal scaling.
     */
    const HSCALE = 'HSCALE';

    /**
     * Key for underline.
     */
    const UNDERLINE = 'UNDERLINE';

    // constructors
    /**
     * Key for sub/superscript.
     */
    const SUBSUPSCRIPT = 'SUBSUPSCRIPT';

    /**
     * Key for text skewing.
     */
    const SKEW = 'SKEW';

    /**
     * Key for background.
     */
    const BACKGROUND = 'BACKGROUND';

    /**
     * Key for text rendering mode.
     */
    const TEXTRENDERMODE = 'TEXTRENDERMODE';

    /**
     * Key for split character.
     */
    const SPLITCHARACTER = 'SPLITCHARACTER';

    /**
     * Key for hyphenation.
     */
    const HYPHENATION = 'HYPHENATION';

    /**
     * Key for remote goto.
     */
    const REMOTEGOTO = 'REMOTEGOTO';

    /**
     * Key for local goto.
     */
    const LOCALGOTO = 'LOCALGOTO';

    /**
     * Key for local destination.
     */
    const LOCALDESTINATION = 'LOCALDESTINATION';

    /**
     * Key for generic tag.
     */
    const GENERICTAG = 'GENERICTAG';

    /**
     * Key for image.
     */
    const IMAGE = 'IMAGE';

    /**
     * Key for Action.
     */
    const ACTION = 'ACTION';

    /**
     * Key for newpage.
     */
    const NEWPAGE = 'NEWPAGE';

    /**
     * Key for annotation.
     */
    const PDFANNOTATION = 'PDFANNOTATION';

    // implementation of the Element-methods
    /**
     * Key for color.
     */
    const COLOR = 'COLOR';

    /**
     * Key for encoding.
     */
    const ENCODING = 'ENCODING';

    /**
     * Key for character spacing.
     */
    const CHAR_SPACING = 'CHAR_SPACING';

    // methods that change the member variables

    /**
     * This is a Chunk containing a newline.
     */
    public static function newLine() {
        return new self("\n");
    }

    /**
     * This is a Chunk containing a newpage.
     */
    public static function nextPage() {
        return new self('');
    }
}
