<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'requested_by',
        'approved_by',
        'status',
        'reason',
        'change_type',
        'old_values',
        'new_values',
        'approved_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Change type constants
    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
 

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function getChangeTypeOptions()
    {
        return [
            self::TYPE_CREATE => 'Create',
            self::TYPE_UPDATE => 'Update',
            self::TYPE_DELETE => 'Delete',
        ];
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getChangeTypeBadgeColorAttribute()
    {
        return match($this->change_type) {
            self::TYPE_CREATE => 'bg-blue-100 text-blue-800',
            self::TYPE_UPDATE => 'bg-purple-100 text-purple-800',
            self::TYPE_DELETE => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Relationships
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Approve the request
    public function approve($userId, $reason = null)
    {
        \DB::transaction(function () use ($userId, $reason) {
            $this->update([
                'status' => self::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
                'reason' => $reason,
            ]);

            // Apply changes based on change type
            $this->applyChanges();
        });
    }

    // Reject the request
    public function reject($userId, $reason)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $userId,
            'approved_at' => now(),
            'reason' => $reason,
        ]);
    }

    // Apply approved changes
    private function applyChanges()
    {
        if ($this->change_type === self::TYPE_CREATE) {
            // Create new field
            $field = Field::create(array_merge($this->new_values, [
                'status' => Field::STATUS_ACTIVE,
            ]));
            
            // Update approval with field_id
            $this->update(['field_id' => $field->id]);
            
        } elseif ($this->change_type === self::TYPE_UPDATE && $this->field) {
            // Update existing field
            $this->field->historyLogging = true;
            $this->field->update($this->new_values);
            $this->field->historyLogging = false;
            
            // Log approved history
            $this->field->logHistory('updated', $this->approved_by, $this->id);
            
        } elseif ($this->change_type === self::TYPE_DELETE && $this->field) {
            // Soft delete field (set status to inactive)
            $this->field->historyLogging = true;
            $this->field->update([
                'status' => Field::STATUS_INACTIVE,
                'reason_delete' => $this->new_values['deletion_reason'] ?? null,
            ]);
            $this->field->delete(); // Soft delete
            $this->field->historyLogging = false;
            
            // Log deletion history
            $this->field->logHistory('deleted', $this->approved_by, $this->id);
            
        }
    }

    // Get changes summary
    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values && !$this->new_values) {
            return [];
        }

        $changes = [];
        
        if ($this->change_type === self::TYPE_CREATE) {
            return $this->new_values;
        }
        
        if ($this->change_type === self::TYPE_DELETE) {
            return [
                'reason' => $this->new_values['deletion_reason'] ?? 'No reason provided'
            ];
        }

        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    // Format timestamps
    public function getFormattedApprovedAtAttribute()
    {
        if (!$this->approved_at) return null;
        return $this->approved_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)';
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)';
    }
}