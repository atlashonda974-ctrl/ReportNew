<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReqnoteMark extends Model
{
    use HasFactory;

    // Specify the table name
    protected $table = 'reqnote_mark';

    // Fillable fields for mass assignment
    protected $fillable = [
        'GRH_REFERENCE_NO',
        'upload_file',
        'tag_action',
        'remarks',
        'report_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Automatically handle created_at and updated_at as Carbon instances
    protected $dates = ['created_at', 'updated_at'];
}