import stripAndCollapse from "../core/stripAndCollapse";
import getClass from "./getClass";
const hasClass = ( elem,selector ) => {
    const className = " " + selector + " ";

    if ( elem.nodeType === 1 &&
        ( " " + stripAndCollapse( getClass( elem ) ) + " " ).indexOf( className ) > -1 ) {
        return true;
    }

    return false;
}

export default hasClass;
