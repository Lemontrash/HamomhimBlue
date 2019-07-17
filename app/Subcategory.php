<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $guarded = ['id'];

    public function getCategory(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
