<?php

/**
 * @property-read int     $access_token_id
 * @property-read string  $name
 * @property-read string  $token
 * @property-read array   $abilities
 * @property-read CCarbon $last_used_at
 */
class CModel_AccessToken_AccessTokenModel extends CModel implements CModel_AccessToken_Contract_HasAbilitiesInterface {
    use CModel_AccessToken_AccessTokenModelTrait;

    protected $table = 'access_token';

    protected $guarded = ['access_token_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'abilities' => 'json',
        'last_used_at' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'token',
    ];
}
