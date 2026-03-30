<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalPreAuthRequest extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel's naming convention)
    protected $table = 'medical_pre_auth_requests';

    // Mass assignable fields
    protected $fillable = [
        'health_card_no',
        'policy_holder_name',
        'policy_no',
        'employee_name',
        'patient_name_age_relation',
        'hospital_name',
        'mr_patient_no',
        'expected_admission_date',
        'room_bed_no',
        'presenting_complaints',
        'history_illness',
        'diagnosis_procedure',
        'expected_stay',
        'expected_cost',
        'doctor_name_contact',
        'doctor_signature',
        'created_by',
        'created_at',
        'updated_at'
    ];

    // If you want to disable timestamps (because we are using nullable TIMESTAMPs)
    public $timestamps = false;
}
