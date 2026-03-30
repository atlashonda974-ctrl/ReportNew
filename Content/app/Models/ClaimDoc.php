<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClaimDoc extends Model
{
    use HasFactory;

    protected $table = 'claim_docs';

    protected $fillable = [
        'repname',
        'doc_num',
        'file_name',
        'amount',
        'remarks',
        'bank1',
        'bank2',
        'created_at',
        'updated_at',
        'updated_by',
        'docref',
        'remarks',
        'paid',
    ];

    public $timestamps = false;
    /**
 * A claim document has many GL entries (payments)
 */
public function glDocs()
{
    return $this->hasMany(GlDoc::class, 'claim_doc_id');
}

/**
 * Get uploaded payments count
 */
public function getUploadedCountAttribute(): int
{
    return $this->glDocs()->where('is_uploaded', true)->count();
}

/**
 * Get total payments count
 */
public function getTotalPaymentsCountAttribute(): int
{
    return $this->glDocs()->count();
}

/**
 * Check if all payments are uploaded
 */
public function getIsFullyPaidAttribute(): bool
{
    $total = $this->total_payments_count;
    if ($total === 0) return false;
    
    return $this->uploaded_count === $total;
}

/**
 * Refresh paid status based on all payments
 */
public function refreshPaidStatus(): void
{
    $this->paid = $this->is_fully_paid ? 'Y' : 'N';
    $this->save();
}

/**
 * Get pending payments
 */
public function pendingGlDocs()
{
    return $this->glDocs()->where('is_uploaded', false);
}

/**
 * Get uploaded payments
 */
public function uploadedGlDocs()
{
    return $this->glDocs()->where('is_uploaded', true);
}
}
