<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LandHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'land_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function land(): BelongsTo
    {
        return $this->belongsTo(Land::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getActionDisplayAttribute()
    {
        $actions = [
            'created' => 'Record Created',
            'updated' => 'Record Updated',
            'deleted' => 'Record Deleted',
            'approved_update' => 'Update Approved & Applied',
            'approved_deletion' => 'Deletion Approved & Applied',
            'approved_creation' => 'Creation Approved & Applied',
            'rejected' => 'Request Rejected',
        ];

        return $actions[$this->action] ?? ucfirst($this->action);
    }

    public function getUserDisplayAttribute()
    {
        return $this->user ? $this->user->name : 'System';
    }

    public function getChangesSummaryAttribute()
    {
        if (!$this->old_values || !$this->new_values) {
            return 'No changes detected';
        }

        $changes = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[] = $this->formatFieldName($key);
            }
        }

        $count = count($changes);
        if ($count === 0) return 'No changes';
        if ($count === 1) return "Changed: {$changes[0]}";
        if ($count <= 3) return "Changed: " . implode(', ', $changes);
        
        return "Changed {$count} fields: " . implode(', ', array_slice($changes, 0, 2)) . ", and more...";
    }

    // Helper method to check if this is an approved change
    public function isApprovedChange()
    {
        return in_array($this->action, ['approved_update', 'approved_deletion', 'approved_creation']);
    }

    // Get approver information from metadata
    public function getApprovalMetadata()
    {
        return $this->metadata ?? null;
    }

    // Format field names for display
    private function formatFieldName($key)
    {
        $labels = [
            'lokasi_lahan' => 'Location',
            'tahun_perolehan' => 'Acquisition Year',
            'nilai_perolehan' => 'Acquisition Value',
            'alamat' => 'Address',
            'link_google_maps' => 'Google Maps Link',
            'kota_kabupaten' => 'City/Regency',
            'status' => 'Status',
            'keterangan' => 'Notes',
            'nominal_b' => 'Nominal B',
            'njop' => 'NJOP',
            'est_harga_pasar' => 'Est. Market Price',
        ];

        return $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
    }

    // Static method to record history
    public static function recordHistory($landId, $action, $oldValues = null, $newValues = null, $metadata = null)
    {
        return self::create([
            'land_id' => $landId,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}