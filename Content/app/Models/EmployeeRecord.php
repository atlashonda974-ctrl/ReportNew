<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRecord extends Model
{
    use HasFactory;

    protected $table = 'employee_records';

    protected $fillable = [
        'record_type',
        'transaction_id',
        'client_name',
        'policy_no',

        'employee_name',
        'gender',
        'employee_code',
        'folio_no',

        'employee_id',
        'plan',
        'dob',
        'age',
        'cnic_no',
        'relation',
        'effective_date',

        'dependent_name',
        'required_document',

        'effective_date_of_deletion',

        'existing_details',
        'new_details',

        'remarks',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'dob',
        'effective_date',
        'effective_date_of_deletion',
        'created_at',
        'updated_at'
    ];
}
