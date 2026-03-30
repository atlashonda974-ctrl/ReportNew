<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostDatedCheque extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'branch_name', 'branch_email', 'branch_user', 'insured_name',
    'bank_name', 'cheque_number', 'cheque_date', 'cheque_amount', 'status', 'picture', 'created_by', 'updated_by'];

    
}
