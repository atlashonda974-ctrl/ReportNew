<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdClaim extends Model
{
    // Table name (optional if it follows Laravel convention)
    protected $table = 'opd_claims';

    // Primary key (optional if 'id')
    protected $primaryKey = 'id';

    // Disable auto-incrementing if needed (id is auto-increment here, so no need)
    // public $incrementing = true;

    // Disable timestamps if you're using string 'created_at' & 'updated_at' (Laravel expects datetime)
    public $timestamps = false;

    // Mass assignable attributes
    protected $fillable = [
        'employer_name',
        'employee_name',
        'claimant_name',
        'patient_name',
        'patient_relationship',
        'patient_relationship_other',
        'health_card_no',
        'clinic_hospital_doctor',
        'consultation_fee',
        'cost_of_medicines',
        'cost_of_investigation',
        'total_cost',
        'specialized_hospital_name',
        'referring_specialist_name',
        'investigation_procedure_name',
        'cat_scan',
        'mri',
        'nuclear_scan',
        'angiography',
        'ercp',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
