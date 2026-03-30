<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlDoc extends Model
{
    use HasFactory;

    protected $table = 'gl_docs';

    protected $fillable = [
        'claim_doc_id',
        'doc_num',
        'voucher_serial',
        'voucher_no',
        'location_code',
        'amount',
        'code',
        'ledger_type',
        'party_description',
        'gl_file_name',
        'gl_file_names',   // JSON array of ALL uploaded filenames
        'uploaded_at',
        'uploaded_by',
        'is_uploaded',
        'dept', 
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'is_uploaded'   => 'boolean',
        'uploaded_at'   => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'gl_file_names' => 'array',    // auto encode/decode JSON
    ];

    /**
     * Relationship back to main document
     */
    public function claimDoc()
    {
        return $this->belongsTo(ClaimDoc::class, 'claim_doc_id');
    }

    /**
     * Mark this payment as uploaded.
     * 
     */
    public function markAsUploaded(string $fileName, ?string $uploadedBy = null): bool
    {
        $this->gl_file_name  = $fileName;
        $this->gl_file_names = [$fileName];   // start the array with the first file
        $this->is_uploaded   = true;
        $this->uploaded_at   = now();
        $this->uploaded_by   = $uploadedBy;

        $saved = $this->save();

        // Refresh parent document's paid status
        if ($saved && $this->claimDoc) {
            $this->claimDoc->refreshPaidStatus();
        }

        return $saved;
    }

    /**
     * Append an additional filename to gl_file_names JSON array.
     * 
     */
    public function appendFileName(string $fileName): bool
    {
        $names   = $this->gl_file_names ?? [];
        $names[] = $fileName;
        return $this->update(['gl_file_names' => $names]);
    }

    /**
     * Get file storage path for the primary file
     */
    public function getStoragePathAttribute(): ?string
    {
        if (!$this->gl_file_name) return null;
        return "public/claims/{$this->doc_num}/{$this->gl_file_name}";
    }

    /**
     * Get all file storage paths
     */
    public function getAllStoragePathsAttribute(): array
    {
        $names = $this->gl_file_names ?? [];
        return array_map(fn($name) => "public/claims/{$this->doc_num}/{$name}", $names);
    }

    /**
     * Scope: pending (not yet uploaded) payments
     */
    public function scopePending($query)
    {
        return $query->where('is_uploaded', false);
    }

    /**
     * Scope: uploaded payments
     */
    public function scopeUploaded($query)
    {
        return $query->where('is_uploaded', true);
    }

    /**
     * Scope: payments for a specific document number
     */
    public function scopeForDocument($query, string $docNum)
    {
        return $query->where('doc_num', $docNum);
    }
}