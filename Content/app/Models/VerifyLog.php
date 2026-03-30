<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyLog extends Model
{
    use HasFactory;

    protected $table = 'verifylogs';
    public $timestamps = false;

    // Define the fillable attributes
    protected $fillable = [
        'GCP_DOC_REFERENCENO',
        'PDP_DEPT_CODE',
        'GCP_SERIALNO',
        'GCP_ISSUEDATE',
        'GCP_COMMDATE',
        'GCP_EXPIRYDATE',
        'GCP_REINSURER',
        'GCP_REISSUEDATE',
        'GCP_RECOMMDATE',
        'GCP_REEXPIRYDATE',
        'GCP_COTOTALSI',
        'GCP_COTOTALPREM',
        'GCP_REINSI',
        'GCP_REINPREM',
        'GCP_COMMAMOUNT',
        'GCP_POSTINGTAG',
        'GCP_CANCELLATIONTAG',
        'GCP_POST_USER',
        'GCT_THEIR_REF_NO',
        'avatar',
        'datetime',
        'sent_to',
        'sent_cc',
        'subject',
        'body',
        'created_by',
        'updated_by',
    ];
}