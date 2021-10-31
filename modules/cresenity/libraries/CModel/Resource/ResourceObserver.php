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

            $filesystem->syncFileNames($resource);
        }
    }

    public function updated(CModel $media) {
        if (is_null($media->getOriginal('model_id'))) {
            return;
        }

        $original = $media->getOriginal('manipulations');

        $original = json_decode($original, true);

        if ($media->manipulations !== $original) {
            $eventDispatcher = CModel::getEventDispatcher();
            CModel::unsetEventDispatcher();

            /** @var \Spatie\MediaLibrary\Conversions\FileManipulator $fileManipulator */
            $fileManipulator = CResources_Factory::createFileManipulator();
            $fileManipulator->createDerivedFiles($media);

            CModel::setEventDispatcher($eventDispatcher);
        }
    }

    public function deleted(CModel $media) {
        if ($media->usesSoftDelete()) {
            if (!$media->isForceDeleting()) {
                return;
            }
        }

        /** @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = CResources_Factory::createFileSystem();

        $filesystem->removeAllFiles($media);
    }
}
