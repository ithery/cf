<?php

class CModel_Resource_ResourceObserver {
    public function creating(CModel $resource) {
        if ($resource->shouldSortWhenCreating()) {
            $resource->setHighestOrderNumber();
        }
    }

    public function updating(CModel $resource) {
        if ($resource->file_name !== $resource->getOriginal('file_name')) {
            /** @var CResources_Filesystem $filesystem */
            $filesystem = CResources_Factory::createFileSystem();
            /** @var CModel|CModel_Resource_ResourceInterface $resource */
            $filesystem->syncFileNames($resource);
        }
    }

    /**
     * @param CModel $resource
     *
     * @see CApp_Model_Resource
     *
     * @return void
     */
    public function updated(CModel $resource) {
        if (is_null($resource->getOriginal('model_id'))) {
            return;
        }

        $original = $resource->getOriginal('manipulations');

        if (is_string($original)) {
            $original = json_decode($original, true);
        }

        if ($resource->manipulations !== $original) {
            $eventDispatcher = CModel::getEventDispatcher();
            CModel::unsetEventDispatcher();

            /** @var \CResources_FileManipulator $fileManipulator */
            $fileManipulator = CResources_Factory::createFileManipulator();
            /** @var CModel|CModel_Resource_ResourceInterface $resource */
            $fileManipulator->createDerivedFiles($resource);

            CModel::setEventDispatcher($eventDispatcher);
        }
    }

    public function deleted(CModel $resource) {
        if ($resource->usesSoftDelete()) {
            if (!$resource->isForceDeleting()) {
                return;
            }
        }

        /** @var \CResources_Filesystem $filesystem */
        $filesystem = CResources_Factory::createFileSystem();
        /** @var CModel|CModel_Resource_ResourceInterface $resource */
        $filesystem->removeAllFiles($resource);
    }
}
