
const rhtmlSuffix = /HTML$/i;

const isXMLDoc= ( elem ) => {
    var namespace = elem && elem.namespaceURI,
        docElem = elem && ( elem.ownerDocument || elem ).documentElement;

    // Assume HTML when documentElement doesn't yet exist, such as inside
    // document fragments.
    return !rhtmlSuffix.test( namespace || docElem && docElem.nodeName || "HTML" );
};

export default isXMLDoc;
