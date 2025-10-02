<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LandApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'requested_by',
        'approved_by',
        'change_type',
        'old_data',
        'new_data',
        'status',
        'reason'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // Relationships
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M d, Y H:i');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at->format('M d, Y H:i');
    }

    // Helper methods
    public function getDeletionReason()
    {
        if ($this->change_type === 'delete' && $this->new_data) {
            return $this->new_data['deletion_reason'] ?? null;
        }
        return null;
    }

    public function approve($approverId, $reason = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approverId,
            'reason' => $reason,
        ]);

        // Apply the changes based on change_type
        if ($this->change_type === 'details') {
            $this->land->update($this->new_data);
        } elseif ($this->change_type === 'delete') {
            $this->land->delete();
        } elseif ($this->change_type === 'create') {
            Land::create($this->new_data);
        }
    }

    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'reason' => $reason,
        ]);
    }
}