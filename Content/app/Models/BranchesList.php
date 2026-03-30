<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchesList extends Model
{
    // Specify the exact table name
    protected $table = 'branches_lists';

    // Guard all columns from mass assignment
    protected $guarded = [];
}
