<?php

namespace Cresenity\Testing;

class MemberModel extends \CModel {

    protected $table = 'member';
    protected $guarded = ['member_id'];

    protected $casts = [
        'birthdate'=>'date',
    ];
    public static function search($query) {
        
        return empty($query) ? static::query() : static::where('name', 'like', '%' . $query . '%')
                        ->orWhere('email', 'like', '%' . $query . '%');
    }

}
