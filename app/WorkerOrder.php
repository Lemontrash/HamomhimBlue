<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerOrder extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];
}

