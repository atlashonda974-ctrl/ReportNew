<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReinsLog extends Model
{
    use HasFactory;
    protected $table = 'reinslogs';
    // protected $fillable = [
    //     'uw_doc',
    //     'dept', 
    //     'riskMarked',  Y
    //     'noti_att', N
    //     'created_by', 
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    // ];
    //   protected $fillable = [
    //     'uw_doc',
    //     'dept', 
    //     'riskMarked', 
    //     'noti_att', 
    //     'created_by', 
    //     'updated_by',
    //     'created_at',
    //     'updated_at',
    // ];
    protected $fillable = [
    'uw_doc',
    'report_name',
    'dept',
    'issue_date',
    'comm_date',
    'expiry_date',
    'insured',
    'location',
    'business_class',
    'sum_insured',
    'gross_premium',
    'net_premium',
    'riskMarked',
    'noti_att',
    'created_by',
    'updated_by',
    'created_at',
    'updated_at',
    'remarks',
     'GDH_POSTING_DATE',
     'PII_DESC',
     'GDH_BASEDOCUMENTNO',
     'RQN_STS',


];

    protected $guarded = [];

    public $timestamps = true;
}