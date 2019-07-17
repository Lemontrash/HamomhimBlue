<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    public function getUser(){
        return $this->hasMany(User::class, 'id', 'userId');
    }
}
