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
            // Get old data before update
            $oldData = $this->land->only(array_keys($this->new_data));
            
            // Update land (this won't trigger history due to permission check in boot)
            $this->land->update($this->new_data);
            
            // Manually record approved update in history
            LandHistory::recordHistory(
                $this->land_id,
                'approved_update',
                $oldData,
                $this->new_data,
                [
                    'approval_id' => $this->id,
                    'approved_by' => $approverId,
                    'requested_by' => $this->requested_by,
                    'approval_reason' => $reason
                ]
            );
            
        } elseif ($this->change_type === 'delete') {
            // Record approved deletion in history BEFORE deleting
            LandHistory::recordHistory(
                $this->land_id,
                'approved_deletion',
                $this->old_data,
                $this->new_data, // Contains deletion_reason
                [
                    'approval_id' => $this->id,
                    'approved_by' => $approverId,
                    'requested_by' => $this->requested_by,
                    'approval_reason' => $reason
                ]
            );
            
            // Then delete the land
            $this->land->delete();
            
        } elseif ($this->change_type === 'create') {
            // Create new land (this won't trigger history due to permission check)
            $newLand = Land::create($this->new_data);
            
            // Manually record approved creation in history
            LandHistory::recordHistory(
                $newLand->id,
                'approved_creation',
                null,
                $this->new_data,
                [
                    'approval_id' => $this->id,
                    'approved_by' => $approverId,
                    'requested_by' => $this->requested_by,
                    'approval_reason' => $reason
                ]
            );
        }
    }

    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approverId,
            'reason' => $reason,
        ]);

        // Record rejection in history (if land_id exists)
        if ($this->land_id) {
            LandHistory::recordHistory(
                $this->land_id,
                'rejected',
                $this->old_data,
                $this->new_data,
                [
                    'approval_id' => $this->id,
                    'rejected_by' => $approverId,
                    'requested_by' => $this->requested_by,
                    'rejection_reason' => $reason,
                    'change_type' => $this->change_type
                ]
            );
        }
    }
}