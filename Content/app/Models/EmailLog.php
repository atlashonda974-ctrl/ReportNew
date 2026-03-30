<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'emaillogs';

    protected $fillable = [
        'reqnote',
        'repname',
        'datetime',
        'created_by',
        'updated_by',
        'sent_to',
        'sent_cc',
        'subject',
        'body',
        'doc_date',
        'dept',
        'business_desc',
        'insured',
        'reins_party',
        'total_sum_ins',
        'ri_sum_ins',
        'share',
        'total_premium',
        'ri_premium',
        'comm_date',
        'expiry_date',
        'cp',
        'conv_takaful',
        'posted',
        'user_name',
        'acceptance_date',
        'warranty_period',
        'commission_percent',
        'commission_amount',
        'acceptance_no',
        'created_at',
        'doc_num', 
    ];
    public $timestamps = false;
}
