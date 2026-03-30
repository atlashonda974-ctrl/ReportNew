<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlApproval extends Model
{
    use HasFactory;

    protected $table = 'gl_approval';

    // Specify the fillable fields
    protected $fillable = [
        'doc',
        'approve',
        'remakrs',
        'in_range',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Optionally, you might want to handle date formats
    protected $dates = ['created_at', 'updated_at'];
}