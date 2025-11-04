<?php

class CModel_Scout_RemoveableScoutCollection extends CCollection {
    /**
     * Get the Scout identifiers for all of the entities.
     *
     * @return array
     */
    public function getQueueableIds() {
        if ($this->isEmpty()) {
            return [];
        }

        return in_array(CModel_Scout_SearchableTrait::class, c::classUsesRecursive($this->first()))
            ? $this->map->getScoutKey()->all()
            : parent::getQueueableIds();
    }
}
