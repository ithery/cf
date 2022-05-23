const implode = (delimiter, array) => array.join(delimiter);

const first = (array)  => array.valueOf()[0];
const last = (array)  => array.valueOf()[array.length - 1];

export default {
    implode,
    first,
    last
}
