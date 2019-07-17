<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    public function getAllSubcategories(){
        return $this->hasMany(Subcategory::class, 'category_id', 'id');
    }

}
