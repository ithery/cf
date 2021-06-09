<?php

class CMage_Request extends CHTTP_FormRequest {
    use CMage_Request_Trait_InteractsWithMageTrait;
    use CMage_Request_Trait_InteractsWithRelatedMageTrait;
    //use MemoizesMethods;

    /**
     * Determine if this request is via a many to many relationship.
     *
     * @return bool
     */
    public function viaManyToMany() {
        return in_array(
            $this->relationshipType,
            ['belongsToMany', 'morphToMany']
        );
    }
}
