const getClass = ( elem ) => {
	return elem.getAttribute && elem.getAttribute( "class" ) || "";
}

export default getClass;
