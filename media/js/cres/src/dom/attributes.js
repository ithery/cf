import isXMLDoc from "../core/isXMLDoc";
import rnothtmlwhite from "../core/var/rnothtmlwhite";
const attrHooks = {};
export const attr = ( elem, name, value ) => {
    var ret, hooks,
        nType = elem.nodeType;

    // Don't get/set attributes on text, comment and attribute nodes
    if ( nType === 3 || nType === 8 || nType === 2 ) {
        return;
    }

    // Fallback to prop when attributes are not supported
    if ( typeof elem.getAttribute === "undefined" ) {
        return jQuery.prop( elem, name, value );
    }

    // Attribute hooks are determined by the lowercase version
    // Grab necessary hook if one is defined
    if ( nType !== 1 || !isXMLDoc( elem ) ) {
        hooks = attrHooks[ name.toLowerCase() ];
    }

    if ( value !== undefined ) {
        if ( value === null ) {
            jQuery.removeAttr( elem, name );
            return;
        }

        if ( hooks && "set" in hooks &&
            ( ret = hooks.set( elem, value, name ) ) !== undefined ) {
            return ret;
        }

        elem.setAttribute( name, value );
        return value;
    }

    if ( hooks && "get" in hooks && ( ret = hooks.get( elem, name ) ) !== null ) {
        return ret;
    }

    ret = elem.getAttribute( name );

    // Non-existent attributes return null, we normalize to undefined
    return ret == null ? undefined : ret;
};


export const removeAttr = ( elem, value ) => {
    var name,
        i = 0,

        // Attribute names can contain non-HTML whitespace characters
        // https://html.spec.whatwg.org/multipage/syntax.html#attributes-2
        attrNames = value && value.match( rnothtmlwhite );

    if ( attrNames && elem.nodeType === 1 ) {
        while ( ( name = attrNames[ i++ ] ) ) {
            elem.removeAttribute( name );
        }
    }
}
