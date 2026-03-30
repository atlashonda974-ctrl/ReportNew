<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMedicalClaim extends Model
{
    // Table name (optional if it follows Laravel convention)
    protected $table = 'employee_medical_claims';

    // Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // Use timestamps (Laravel expects created_at and updated_at as TIMESTAMP/DATETIME)
    public $timestamps = true;

    // Mass assignable attributes
    protected $fillable = [
        'employee_name',
        'sex',
        'parent_name',
        'marital_status',
        'date_of_birth',
        'id_number',
        'policy_holder_name',
        'home_address',
        'telephone',
        'occupation',
        'designation',
        'employer_name',
        'business_address',
        'business_telephone',
        'dependents', // JSON field
        'q1a',
        'q1b',
        'q2a',
        'q2b',
        'q3',
        'q4a',
        'q4b',
        'medical_details',
        'created_by',
        'created_at',
        'updated_at',
    ];

    // Cast JSON column to array automatically
    protected $casts = [
        'dependents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
