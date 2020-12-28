<?php

class CParser_HtmlParser_Tokenizer_State {
    const Text = 1;
    const BeforeTagName = 2; //after <
    const InTagName = 3;
    const InSelfClosingTag = 4;
    const BeforeClosingTagName = 5;
    const InClosingTagName = 6;
    const AfterClosingTagName = 7;
    //attributes
    const BeforeAttributeName = 8;
    const InAttributeName = 9;
    const AfterAttributeName = 10;
    const BeforeAttributeValue = 11;
    const InAttributeValueDq = 12; // "
    const InAttributeValueSq = 13; // '
    const InAttributeValueNq = 14;
    //declarations
    const BeforeDeclaration = 15; // !
    const InDeclaration = 16;
    //processing instructions
    const InProcessingInstruction = 17; // ?
    //comments
    const BeforeComment = 18;
    const InComment = 19;
    const AfterComment1 = 20;
    const AfterComment2 = 21;
    //cdata
    const BeforeCdata1 = 22; // [
    const BeforeCdata2 = 23; // C
    const BeforeCdata3 = 24; // D
    const BeforeCdata4 = 25; // A
    const BeforeCdata5 = 26; // T
    const BeforeCdata6 = 27; // A
    const InCdata = 28; // [
    const AfterCdata1 = 29; // ]
    const AfterCdata2 = 30; // ]
    //special tags
    const BeforeSpecial = 31; //S
    const BeforeSpecialEnd = 32; //S
    const BeforeScript1 = 33; //C
    const BeforeScript2 = 34; //R
    const BeforeScript3 = 35; //I
    const BeforeScript4 = 36; //P
    const BeforeScript5 = 37; //T
    const AfterScript1 = 38; //C
    const AfterScript2 = 39; //R
    const AfterScript3 = 40; //I
    const AfterScript4 = 41; //P
    const AfterScript5 = 42; //T
    const BeforeStyle1 = 43; //T
    const BeforeStyle2 = 44; //Y
    const BeforeStyle3 = 45; //L
    const BeforeStyle4 = 46; //E
    const AfterStyle1 = 47; //T
    const AfterStyle2 = 48; //Y
    const AfterStyle3 = 49; //L
    const AfterStyle4 = 50; //E
    const BeforeEntity = 51; //&
    const BeforeNumericEntity = 52; //#
    const InNamedEntity = 53;
    const InNumericEntity = 54;
    const InHexEntity = 55; //X
}
