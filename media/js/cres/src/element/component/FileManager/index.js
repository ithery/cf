import FileManager from './FileManager';
import './index.scss';
const initFileManager = (element) => {
    return new FileManager(element);
};

export {
    FileManager,
    initFileManager
};
