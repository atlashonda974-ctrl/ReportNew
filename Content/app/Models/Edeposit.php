<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Edeposit extends Model
{
    use HasFactory;

    protected $fillable = ['submission_date', 'dealer_code', 'dealer_name', 'slip_number', 'company_code',
    'credited_to', 'branch_name', 'branch_code', 'prp_number', 'depositor_contact', 'instrument_no',
    'depositor_date', 'drawn_on', 'amount', 'total_amount_words', 'five_thousand',
    'thousand', 'five_hundred', 'hundred', 'fifty', 'twenty', 'ten', 'five', 'two', 'one',
    'created_by', 'updated_by'];
    
}
