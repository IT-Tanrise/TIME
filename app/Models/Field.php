<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Field extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_bidang',
        'business_unit_id',
        'nomor_bidang',
        'status',
        'reason_delete',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public $historyLogging = false;

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_PENDING => 'Pending',
        ];
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_INACTIVE => 'bg-red-100 text-red-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function isHistoryLogging()
    {
        return $this->historyLogging;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($field) {
            if (!$field->isHistoryLogging()) {
                $field->logHistory('created');
            }
        });

        static::updated(function ($field) {
            if ($field->isDirty() && !$field->isHistoryLogging()) {
                $dirtyFields = $field->getDirty();
                unset($dirtyFields['updated_at'], $dirtyFields['updated_by'], $dirtyFields['created_at'], $dirtyFields['created_by']);
                
                if (!empty($dirtyFields)) {
                    $field->logHistory('updated');
                }
            }
        });

        static::creating(function ($field) {
            if (Auth::check()) {
                $field->created_by = Auth::id();
                $field->updated_by = Auth::id();
            }
            
            // Set default status to pending for new records
            if (empty($field->status)) {
                $field->status = self::STATUS_PENDING;
            }
        });

        static::updating(function ($field) {
            if (Auth::check()) {
                $field->updated_by = Auth::id();
            }
        });

        static::deleting(function ($field) {
            if (!$field->isHistoryLogging()) {
                $field->logHistory('deleted');
            }
        });
    }

    // Relationships
    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvals()
    {
        return $this->hasMany(FieldApproval::class);
    }

    public function pendingApprovals()
    {
        return $this->hasMany(FieldApproval::class)->where('status', 'pending');
    }

    public function histories()
    {
        return $this->hasMany(FieldHistory::class)->orderBy('created_at', 'desc');
    }

    // Check if field has any pending approvals
    public function hasPendingApprovals()
    {
        return $this->pendingApprovals()->exists();
    }

    // Get pending approvals by type
    public function getPendingApprovalsByType($type)
    {
        return $this->pendingApprovals()->where('change_type', $type)->get();
    }

    // Log history
    public function logHistory($action, $approvedBy = null, $approvalId = null)
    {
        if ($this->historyLogging) {
            return;
        }
        
        $this->historyLogging = true;
        
        try {
            $oldValues = null;
            $newValues = null;

            if ($action === 'updated') {
                $dirtyFields = $this->getDirty();
                unset($dirtyFields['updated_at'], $dirtyFields['updated_by']);
                
                if (empty($dirtyFields)) {
                    return;
                }
                
                $oldValues = [];
                $newValues = [];
                
                foreach (array_keys($dirtyFields) as $field) {
                    $oldValues[$field] = $this->getOriginal($field);
                    $newValues[$field] = $this->getAttribute($field);
                }
            } elseif ($action === 'created') {
                $newValues = $this->getAttributes();
            } elseif ($action === 'deleted') {
                $oldValues = $this->getAttributes();
            }

            $historyUserId = $approvedBy ?? Auth::id();

            FieldHistory::create([
                'field_id' => $this->id,
                'user_id' => $historyUserId,
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create field history: ' . $e->getMessage(), [
                'field_id' => $this->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->historyLogging = false;
        }
    }

    // Generate nomor bidang
    public static function generateNomorBidang($businessUnitId)
    {
        $businessUnit = BusinessUnit::find($businessUnitId);
        if (!$businessUnit) {
            throw new \Exception('Business unit not found');
        }

        // Get business unit code (first 4 chars or create acronym)
        $buCode = strtoupper(substr($businessUnit->code ?? $businessUnit->name, 0, 4));
        
        // Get last number for this business unit
        $lastField = self::where('business_unit_id', $businessUnitId)
            ->where('nomor_bidang', 'like', $buCode . '%')
            ->orderBy('nomor_bidang', 'desc')
            ->first();
        
        $nextNumber = 1;
        if ($lastField) {
            // Extract number from format: XXXX/YYY/NNN
            $parts = explode('/', $lastField->nomor_bidang);
            if (count($parts) === 3) {
                $nextNumber = intval($parts[2]) + 1;
            }
        }
        
        // Format: BUCODE/LB/001
        return sprintf('%s/LB/%03d', $buCode, $nextNumber);
    }

    // Format timestamps
    public function getCreatedAtGmt7Attribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta') : null;
    }

    public function getUpdatedAtGmt7Attribute()
    {
        return $this->updated_at ? $this->updated_at->setTimezone('Asia/Jakarta') : null;
    }

    public function getFormattedCreatedAtAttribute()
    {
        if (!$this->created_at) return null;
        
        return $this->created_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)' . 
               ($this->createdBy ? ' - ' . $this->createdBy->name : '');
    }

    public function getFormattedUpdatedAtAttribute()
    {
        if (!$this->updated_at) return null;
        
        return $this->updated_at_gmt7->format('d/m/Y H:i') . ' (GMT+7)' . 
               ($this->updatedBy ? ' - ' . $this->updatedBy->name : '');
    }
}