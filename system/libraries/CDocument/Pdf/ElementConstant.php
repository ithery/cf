<?php

class CDocument_Pdf_ElementConstant {
    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const HEADER = 0;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const TITLE = 1;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const SUBJECT = 2;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const KEYWORDS = 3;

    /**
     * This is a possible type of <CODE>Element </CODE>.
     */
    const AUTHOR = 4;

    /**
     * This is a possible type of <CODE>Element </CODE>.
     */
    const PRODUCER = 5;

    /**
     * This is a possible type of <CODE>Element </CODE>.
     */
    const CREATIONDATE = 6;

    /**
     * This is a possible type of <CODE>Element </CODE>.
     */
    const CREATOR = 7;

    /**
     * This is a possible type of <CODE>Element </CODE>.
     */
    const MODIFICATIONDATE = 8;

    // static membervariables (content)

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const CHUNK = 10;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const PHRASE = 11;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const PARAGRAPH = 12;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const SECTION = 13;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const LIST = 14;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const LISTITEM = 15;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const CHAPTER = 16;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const ANCHOR = 17;

    // static membervariables (tables)

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const CELL = 20;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const ROW = 21;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const TABLE = 22;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const PTABLE = 23;

    // static membervariables (annotations)

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const ANNOTATION = 29;

    // static membervariables (geometric figures)

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const RECTANGLE = 30;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const JPEG = 32;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const JPEG2000 = 33;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const IMGRAW = 34;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const IMGTEMPLATE = 35;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     *
     * @since 2.1.5
     */
    const JBIG2 = 36;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const MULTI_COLUMN_TEXT = 40;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const MARKED = 50;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     *
     * @since 2.1.2
     */
    const YMARK = 55;

    /**
     * This is a possible type of <CODE>Element</CODE>.
     */
    const FOOTNOTE = 56;

    // static membervariables (alignment)

    /**
     * A possible value for paragraph alignment. This specifies that the text is aligned to the left indent and extra
     * whitespace should be placed on the right.
     */
    const ALIGN_UNDEFINED = -1;

    /**
     * A possible value for paragraph alignment. This specifies that the text is aligned to the left indent and extra
     * whitespace should be placed on the right.
     */
    const ALIGN_LEFT = 0;

    /**
     * A possible value for paragraph alignment. This specifies that the text is aligned to the center and extra
     * whitespace should be placed equally on the left and right.
     */
    const ALIGN_CENTER = 1;

    /**
     * A possible value for paragraph alignment. This specifies that the text is aligned to the right indent and extra
     * whitespace should be placed on the left.
     */
    const ALIGN_RIGHT = 2;

    /**
     * A possible value for paragraph alignment. This specifies that extra whitespace should be spread out through the
     * rows of the paragraph with the text lined up with the left and right indent except on the last line which should
     * be aligned to the left.
     */
    const ALIGN_JUSTIFIED = 3;

    /**
     * A possible value for vertical alignment.
     */
    const ALIGN_TOP = 4;

    /**
     * A possible value for vertical alignment.
     */
    const ALIGN_MIDDLE = 5;

    /**
     * A possible value for vertical alignment.
     */
    const ALIGN_BOTTOM = 6;

    /**
     * A possible value for vertical alignment.
     */
    const ALIGN_BASELINE = 7;

    /**
     * Does the same as ALIGN_JUSTIFIED but the last line is also spread out.
     */
    const ALIGN_JUSTIFIED_ALL = 8;

    // static member variables for CCITT compression

    /**
     * Pure two-dimensional encoding (Group 4).
     */
    const CCITTG4 = 0x100;

    /**
     * Pure one-dimensional encoding (Group 3, 1-D).
     */
    const CCITTG3_1D = 0x101;

    /**
     * Mixed one- and two-dimensional encoding (Group 3, 2-D).
     */
    const CCITTG3_2D = 0x102;

    /**
     * A flag indicating whether 1-bits are to be consterpreted as black pixels and 0-bits as white pixels,.
     */
    const CCITT_BLACKIS1 = 1;

    /**
     * A flag indicating whether the filter expects extra 0-bits before each encoded line so that the line begins on a
     * byte boundary.
     */
    const CCITT_ENCODEDBYTEALIGN = 2;

    /**
     * A flag indicating whether end-of-line bit patterns are required to be present in the encoding.
     */
    const CCITT_ENDOFLINE = 4;

    /**
     * A flag indicating whether the filter expects the encoded data to be terminated by an end-of-block pattern,
     * overriding the Rows parameter. The use of this flag will set the key /EndOfBlock to false.
     */
    const CCITT_ENDOFBLOCK = 8;
}
