// Strip and collapse whitespace according to HTML spec

import rnothtmlwhite from './var/rnothtmlwhite';

// https://infra.spec.whatwg.org/#strip-and-collapse-ascii-whitespace
function stripAndCollapse(value) {
    let tokens = value.match(rnothtmlwhite) || [];
    return tokens.join(' ');
}

export default stripAndCollapse;
