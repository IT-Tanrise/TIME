<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BiayaTambahanSoil extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_id',
        'description_id',
        'harga',
        'cost_type',
        'date_cost'
    ];

    protected $casts = [
        'harga' => 'integer',
        'date_cost' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Add boot method to log history when additional costs change
    protected static function boot()
    {
        parent::boot();

        static::created(function ($biayaTambahan) {
            $biayaTambahan->logSoilHistory('additional_cost_added');
        });

        static::updated(function ($biayaTambahan) {
            $biayaTambahan->logSoilHistory('additional_cost_updated');
        });

        static::deleted(function ($biayaTambahan) {
            $biayaTambahan->logSoilHistory('additional_cost_deleted');
        });
    }

    // Relationship to Soil
    public function soil()
    {
        return $this->belongsTo(Soil::class);
    }

    // Relationship to Description
    public function description()
    {
        return $this->belongsTo(DescriptionBiayaTambahanSoil::class, 'description_id');
    }

    // Format harga untuk display
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Get cost type options
    public static function getCostTypeOptions()
    {
        return [
            'standard' => 'Standard',
            'non_standard' => 'Non Standard'
        ];
    }

    // Format harga for input (with thousand separator)
    public function getFormattedInputHargaAttribute()
    {
        return number_format($this->harga, 0, ',', '.');
    }

    // Method to log history to the parent soil record
    public function logSoilHistory($action)
    {
        if (!$this->soil) {
            return;
        }

        $changes = [];
        $oldValues = null;
        $newValues = null;

        // Prepare change data based on action
        switch ($action) {
            case 'additional_cost_added':
                $changes = ['additional_cost_added'];
                $newValues = [
                    'additional_cost_description' => $this->description->description ?? 'Unknown',
                    'additional_cost_amount' => $this->harga,
                    'additional_cost_type' => $this->cost_type,
                    'additional_cost_date' => $this->date_cost?->format('Y-m-d'),
                ];
                break;

            case 'additional_cost_updated':
                $changes = ['additional_cost_updated'];
                if ($this->isDirty()) {
                    $oldValues = [];
                    $newValues = [];
                    
                    foreach ($this->getDirty() as $field => $newValue) {
                        $oldValue = $this->getOriginal($field);
                        
                        if ($field === 'description_id') {
                            $oldDescription = \App\Models\DescriptionBiayaTambahanSoil::find($oldValue);
                            $newDescription = \App\Models\DescriptionBiayaTambahanSoil::find($newValue);
                            $oldValues['additional_cost_description'] = $oldDescription?->description ?? 'Unknown';
                            $newValues['additional_cost_description'] = $newDescription?->description ?? 'Unknown';
                        } else {
                            $oldValues["additional_cost_{$field}"] = $oldValue;
                            $newValues["additional_cost_{$field}"] = $newValue;
                        }
                    }
                }
                break;

            case 'additional_cost_deleted':
                $changes = ['additional_cost_deleted'];
                $oldValues = [
                    'additional_cost_description' => $this->description->description ?? 'Unknown',
                    'additional_cost_amount' => $this->harga,
                    'additional_cost_type' => $this->cost_type,
                    'additional_cost_date' => $this->date_cost?->format('Y-m-d'),
                ];
                break;
        }

        // Create history record
        \App\Models\SoilHistory::create([
            'soil_id' => $this->soil_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => $changes,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}