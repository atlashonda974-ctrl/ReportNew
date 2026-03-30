<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalClaim extends Model
{
    use HasFactory;

    protected $table = 'medical_claims';

    protected $fillable = [
        // Section A – Policy / Employee
        'insured_name',
        'claimant_name',
        'employee_name',
        'employee_telephone',
        'policy_no',
        'health_card_no',

        // Patient details
        'patient_relationship',
        'relationship_other',
        'patient_name',
        'patient_dob',
        'patient_gender',
        'patient_cnic_passport',

        // Nationality / residence
        'country_residence',
        'nationality',

        // Employer
        'employer_address_line1',
        'employer_address_line2',
        'employer_telephone',

        // Illness / work
        'illness_description',
        'symptoms_first_date',
        'last_work_date',

        // Accident
        'is_accident',
        'accident_date',
        'accident_details',

        // Usual doctor
        'usual_doctor_name_address',
        'usual_doctor_address_line2',
        'usual_doctor_telephone',
        'consulted_other_doctor',

        // Patient contact
        'patient_location',
        'contact_for_exam',

        // Other insurance
        'other_insurance_details',

        // Continuation
        'is_continuation',
        'continuation_details',

        // Section B – Treating Physician
        'physician_duration',
        'records_back',
        'referring_physician',
        'referring_physician_line2',
        'first_consulted_date',

        // Diagnosis
        'diagnosis',
        'diagnosis_line2',

        // Accident details (physician section)
        'accident_happen_details',
        'accident_happen_details_line2',

        // Treatment
        'treatment_given',
        'treatment_given_line2',

        // Condition history
        'condition_history',

        // Maternity
        'maternity_type',
        'expected_delivery_date',
        'maternity_first_consulted',

        // Physician info
        'physician_name',
        'physician_address_line1',
        'physician_address_line2',
        'physician_telephone',

        // System
        'created_by',
    ];

    // Cast fields that may store arrays
    protected $casts = [
        'symptoms_first_date' => 'array',
        'accident_details' => 'array',
        'consulted_other_doctor' => 'array',
        'other_insurance_details' => 'array',
        'continuation_details' => 'array',
        'records_back' => 'array',
        'diagnosis' => 'array',
        'accident_happen_details' => 'array',
        'treatment_given' => 'array',
        'condition_history' => 'array',
    ];

    // Disable auto timestamps
    public $timestamps = false;
}
