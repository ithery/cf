<?php

trait CModel_Trait_HasVersion7Uuids {
    use CModel_Trait_HasUuids;

    /**
     * Generate a new UUID (version 7) for the model.
     *
     * @return string
     */
    public function newUniqueId() {
        return (string) cstr::uuid7();
    }
}
