<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CMage_Request extends CHTTP_FormRequest
{
    use CMage_Request_Trait_InteractsWithMageTrait;
    use CMage_Request_Trait_InteractsWithRelatedMageTrait;
    //use MemoizesMethods;

    /**
     * Determine if this request is via a many to many relationship.
     *
     * @return bool
     */
    public function viaManyToMany()
    {
        return in_array(
            $this->relationshipType,
            ['belongsToMany', 'morphToMany']
        );
    }
}
