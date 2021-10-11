let Task = require('./Task');
let File = require('../File');
let FileCollection = require('../FileCollection');

class VersionFilesTask extends Task {
    /**
     * Run the task.
     */
    run() {
        this.files = new FileCollection(this.data.files);

        this.assets = this.data.files.map(file => {
            file = new File(file);

            this.cf.manifest.hash(file.pathFromPublic());

            return file;
        });
    }

    /**
     * Handle when a relevant source file is changed.
     *
     * @param {string} updatedFile
     */
    onChange(updatedFile) {
        this.cf.manifest.hash(new File(updatedFile).pathFromPublic()).refresh();
    }

    get cf() {
        return global.CF;
    }
}

module.exports = VersionFilesTask;
