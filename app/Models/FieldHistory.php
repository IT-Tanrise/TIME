<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Action constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_RESTORED = 'restored';

    public static function getActionOptions()
    {
        return [
            self::ACTION_CREATED => 'Created',
            self::ACTION_UPDATED => 'Updated',
            self::ACTION_DELETED => 'Deleted',
            self::ACTION_RESTORED => 'Restored',
        ];
    }

    public function getActionBadgeColorAttribute()
    {
        return match($this->action) {
            self::ACTION_CREATED => 'bg-green-100 text-green-800',
            self::ACTION_UPDATED => 'bg-blue-100 text-blue-800',
            self::ACTION_DELETED => 'bg-red-100 text-red-800',
            self::ACTION_RESTORED => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Relationships
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get changes summary
    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values && !$this->new_values) {
            return [];
        }

        $changes = [];
        
        if ($this->action === self::ACTION_CREATED) {
            return $this->new_values;
        }
        
        if ($this->action === self::ACTION_DELETED) {
            return $this->old_values;
        }

        // For updates, show what changed
        if ($this->action === self::ACTION_UPDATED && $this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        return $changes;
    }

    // Format field names
    public function getFormattedFieldName($fieldName)
    {
        $fieldNames = [
            'nama_bidang' => 'Nama Bidang',
            'business_unit_id' => 'Business Unit',
            'nomor_bidang' => 'Nomor Bidang',
            'status' => 'Status',
            'reason_delete' => 'Reason Delete',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
        ];

        return $fieldNames[$fieldName] ?? ucwords(str_replace('_', ' ', $fieldName));
    }

    // Format timestamps
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') . ' (GMT+7)';
    }

    // Get user information
    public function getUserInfoAttribute()
    {
        if (!$this->user) return 'System';
        return $this->user->name . ' (' . $this->user->email . ')';
    }

    // Check if this is an approved action
    public function isApprovedAction()
    {
        if (!$this->new_values) return false;
        return isset($this->new_values['_approval_metadata']['is_approved_change']);
    }

    // Get approval metadata
    public function getApprovalMetadata()
    {
        if (!$this->new_values) return null;
        return $this->new_values['_approval_metadata'] ?? null;
    }
}