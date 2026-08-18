import TreeView from './TreeView';
import './index.scss';
const initTreeView = (element) => {
    return new TreeView(element);
};

export {
    TreeView,
    initTreeView
};
