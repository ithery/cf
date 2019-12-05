<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class COAuth_Model_Client extends COAuth_Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_client';
    
    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'secret',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'grant_types' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
    ];

}