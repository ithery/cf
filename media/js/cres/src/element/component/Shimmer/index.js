import Shimmer from './Shimmer';
import './index.scss';
const initShimmer = (element) => {
    return new Shimmer(element);
};

export {
    Shimmer,
    initShimmer
};
