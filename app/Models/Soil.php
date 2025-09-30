<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\SoilHistory;

class Soil extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'land_id',
        'business_unit_id',
        'nama_penjual',
        'alamat_penjual',
        'nomor_ppjb',
        'tanggal_ppjb',
        'letak_tanah',
        'luas',
        'harga',
        'bukti_kepemilikan',
        'bukti_kepemilikan_details',
        'atas_nama',
        'nop_pbb',
        'nama_notaris_ppat',
        'keterangan',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tanggal_ppjb' => 'date',
        'luas' => 'integer',
        'harga' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public $historyLogging = false; // Changed from private to public for external access

    public function isHistoryLogging()
    {
        return $this->historyLogging;
    }

    // Add these boot events to automatically log history
    protected static function boot()
    {
        parent::boot();

        static::created(function ($soil) {;
            // Only log if not already in history logging process
            if (!$soil->isHistoryLogging()) {
                $soil->logHistory('created');
            }
        });

        static::updated(function ($soil) {
            // Only log history if there are actual meaningful changes 
            // AND we're not already in a history logging process
            if ($soil->isDirty() && !$soil->isHistoryLogging()) {
                // Additional check: don't log if only timestamps or user tracking fields changed
                $dirtyFields = $soil->getDirty();
                unset($dirtyFields['updated_at'], $dirtyFields['updated_by'], $dirtyFields['created_at'], $dirtyFields['created_by']);
                
                if (!empty($dirtyFields)) {
                    $soil->logHistory('updated');
                }
            }
        });

        static::creating(function ($soil) {
            if (Auth::check()) {
                $soil->created_by = Auth::id();
                $soil->updated_by = Auth::id();
            }
        });

        static::updating(function ($soil) {
            if (Auth::check()) {
                $soil->updated_by = Auth::id();
            }
        });

        // Add deleting event to log deletion history
        static::deleting(function ($soil) {
            // Only log if not already in history logging process
            if (!$soil->isHistoryLogging()) {
                $soil->logHistory('deleted');
            }
        });
    }

    // Relationships
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function biayaTambahanSoils()
    {
        return $this->hasMany(BiayaTambahanSoil::class)->orderBy('date_cost', 'asc');
    }

    public function approvals()
    {
        return $this->hasMany(SoilApproval::class);
    }

    public function pendingApprovals()
    {
        return $this->hasMany(SoilApproval::class)->where('status', 'pending');
    }

    // Check if soil has any pending approvals
    public function hasPendingApprovals()
    {
        return $this->pendingApprovals()->exists();
    }

    // Get pending approvals by type
    public function getPendingApprovalsByType($type)
    {
        return $this->pendingApprovals()->where('change_type', $type)->get();
    }

    // Accessors
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedLuasAttribute()
    {
        return number_format($this->luas, 0, ',', '.') . ' m²';
    }

    public function getHargaPerMeterAttribute()
    {
        if ($this->luas > 0) {
            return $this->harga / $this->luas;
        }
        return 0;
    }

    public function getFormattedHargaPerMeterAttribute()
    {
        return 'Rp ' . number_format($this->harga_per_meter, 0, ',', '.');
    }

    public function getTotalBiayaTambahanAttribute()
    {
        return $this->biayaTambahanSoils->sum('harga');
    }

    public function getFormattedTotalBiayaTambahanAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya_tambahan, 0, ',', '.');
    }

    public function getTotalBiayaKeseluruhanAttribute()
    {
        return $this->harga + $this->total_biaya_tambahan;
    }

    public function getFormattedTotalBiayaKeseluruhanAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya_keseluruhan, 0, ',', '.');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(SoilHistory::class)->orderBy('created_at', 'desc');
    }

    // GMT+7 timezone accessors
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

    // ENHANCED: Method to log history with approval information
    public function logHistory($action, $changes = null, $approvedBy = null, $approvalId = null)
    {
        // Prevent recursive calls
        if ($this->historyLogging) {
            return;
        }
        
        $this->historyLogging = true;
        
        try {
            $oldValues = null;
            $newValues = null;
            $changedFields = [];

            if ($action === 'updated' || $action === 'approved_update') {
                if ($changes && is_array($changes)) {
                    // For approved updates, we already have the changes
                    $newValues = $changes;
                    $changedFields = array_keys($changes);
                } else {
                    // For regular updates, check dirty fields
                    $dirtyFields = $this->getDirty();
                    
                    // Remove timestamps and user tracking fields from changes
                    unset($dirtyFields['updated_at'], $dirtyFields['updated_by']);
                    
                    if (empty($dirtyFields)) {
                        return; // No meaningful changes to track
                    }
                    
                    $changedFields = array_keys($dirtyFields);
                    $oldValues = [];
                    $newValues = [];
                    
                    foreach ($changedFields as $field) {
                        $oldValues[$field] = $this->getOriginal($field);
                        $newValues[$field] = $this->getAttribute($field);
                    }
                }
            } elseif ($action === 'created') {
                $newValues = $this->getAttributes();
            } elseif ($action === 'deleted' || $action === 'approved_deletion') {
                // For deletion, store the data that's being deleted
                $oldValues = $this->getAttributes();
                if ($changes && is_array($changes)) {
                    // Include additional deletion info like reason
                    $newValues = $changes;
                }
            }

            // Determine the user ID for history
            $historyUserId = $approvedBy ?? Auth::id();

            // Add approval metadata if this is an approved action
            $historyMetadata = null;
            if ($approvedBy && $approvalId) {
                $historyMetadata = [
                    'approved_by' => $approvedBy,
                    'approval_id' => $approvalId,
                    'is_approved_change' => true
                ];
                
                // Merge metadata with new_values
                if ($newValues) {
                    $newValues = array_merge($newValues, ['_approval_metadata' => $historyMetadata]);
                } else {
                    $newValues = ['_approval_metadata' => $historyMetadata];
                }
            }

            SoilHistory::create([
                'soil_id' => $this->id,
                'user_id' => $historyUserId,
                'action' => $action,
                'changes' => $changedFields,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create soil history: ' . $e->getMessage(), [
                'soil_id' => $this->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->historyLogging = false;
        }
    }

    // ENHANCED: Create history for additional cost changes with approval info
    public function logAdditionalCostHistory($action, $costData = [], $oldCostData = [], $approvedBy = null, $approvalId = null)
    {
        $historyAction = 'additional_cost_' . $action;
        
        $newValues = [];
        $oldValues = [];
        
        if ($action === 'added' && $costData) {
            $newValues = [
                'description' => $costData['description'] ?? '',
                'harga' => $costData['harga'] ?? 0,
                'cost_type' => $costData['cost_type'] ?? 'standard',
                'date_cost' => $costData['date_cost'] ?? null,
            ];
        } elseif ($action === 'updated' && $costData && $oldCostData) {
            $newValues = [
                'description' => $costData['description'] ?? '',
                'harga' => $costData['harga'] ?? 0,
                'cost_type' => $costData['cost_type'] ?? 'standard',
                'date_cost' => $costData['date_cost'] ?? null,
            ];
            $oldValues = [
                'description' => $oldCostData['description'] ?? '',
                'harga' => $oldCostData['harga'] ?? 0,
                'cost_type' => $oldCostData['cost_type'] ?? 'standard',
                'date_cost' => $oldCostData['date_cost'] ?? null,
            ];
        } elseif ($action === 'deleted' && $oldCostData) {
            $oldValues = [
                'description' => $oldCostData['description'] ?? '',
                'harga' => $oldCostData['harga'] ?? 0,
                'cost_type' => $oldCostData['cost_type'] ?? 'standard',
                'date_cost' => $oldCostData['date_cost'] ?? null,
            ];
        } elseif ($action === 'approved') {
            $newValues = $costData;
        }

        // Determine the user ID for history
        $historyUserId = $approvedBy ?? Auth::id();

        // Add approval metadata if this is an approved action
        if ($approvedBy && $approvalId) {
            $historyMetadata = [
                'approved_by' => $approvedBy,
                'approval_id' => $approvalId,
                'is_approved_change' => true
            ];
            
            // Merge metadata with new_values
            if ($newValues) {
                $newValues = array_merge($newValues, ['_approval_metadata' => $historyMetadata]);
            } else {
                $newValues = ['_approval_metadata' => $historyMetadata];
            }
        }

        try {
            SoilHistory::create([
                'soil_id' => $this->id,
                'user_id' => $historyUserId,
                'action' => $historyAction,
                'changes' => [],
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            \Log::info('Soil History '. $action . ': ', [
                'soil_id' => $this->id,
                'user_id' => $historyUserId,
                'action' => $historyAction,
                'changes' => [],
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't prevent the main operation
            \Log::error('Failed to create additional cost history: ' . $e->getMessage(), [
                'soil_id' => $this->id,
                'action' => $historyAction,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logApprovedHistory($action, $oldValues, $newValues, $approvedBy, $approvalId)
    {
        if ($this->historyLogging) {
            return;
        }
        
        $this->historyLogging = true;
        
        try {
            // Determine changed fields
            $changedFields = [];
            foreach ($newValues as $field => $newValue) {
                $oldValue = $oldValues[$field] ?? null;
                if ($oldValue != $newValue) {
                    $changedFields[] = $field;
                }
            }
            
            // Add approval metadata
            $historyMetadata = [
                'approved_by' => $approvedBy,
                'approval_id' => $approvalId,
                'is_approved_change' => true
            ];
            
            $newValuesWithMetadata = array_merge($newValues, ['_approval_metadata' => $historyMetadata]);

            SoilHistory::create([
                'soil_id' => $this->id,
                'user_id' => $approvedBy,
                'action' => $action,
                'changes' => $changedFields,
                'old_values' => $oldValues,
                'new_values' => $newValuesWithMetadata,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create approved soil history: ' . $e->getMessage(), [
                'soil_id' => $this->id,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
        } finally {
            $this->historyLogging = false;
        }
    }
}