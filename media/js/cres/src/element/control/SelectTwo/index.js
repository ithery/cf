import SelectTwo from './SelectTwo';

const initSelectTwo = (element) => {
    if (!element.getAttribute('data-cres-initialized')) {
        return new SelectTwo(element);
    }
};

export {
    SelectTwo,
    initSelectTwo
};
