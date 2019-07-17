<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArchitectRequest extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];


    public function getFiles(){
        return $this->hasMany(ArchitectRequestFile::class, 'request_id', 'id');
    }
}
