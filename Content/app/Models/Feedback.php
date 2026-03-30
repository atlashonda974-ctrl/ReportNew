<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    public $timestamps = true;

    protected $fillable = [
        'uw_doc',
        'curdatetime',
        'surv_prof',
        'surv_resp',
        'surv_acc',
        'surv_overall',
        'clt_req',
        'clt_info',
        'clt_coop',
        'clt_overall',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'curdatetime' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
}
