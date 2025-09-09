<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'land_id',
        'nama_project',
        'tgl_awal',
        'tgl_update',
        'land_acquisition_status',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tgl_awal' => 'date',
        'tgl_update' => 'date',
    ];

    /**
     * Get the land that owns the project.
     */
    public function land()
    {
        return $this->belongsTo(Land::class);
    }

    /**
     * Get the formatted start date.
     */
    public function getFormattedTglAwalAttribute()
    {
        return $this->tgl_awal ? $this->tgl_awal->format('d/m/Y') : null;
    }

    /**
     * Get the formatted update date.
     */
    public function getFormattedTglUpdateAttribute()
    {
        return $this->tgl_update ? $this->tgl_update->format('d/m/Y') : null;
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by land acquisition status.
     */
    public function scopeByLandAcquisitionStatus($query, $status)
    {
        return $query->where('land_acquisition_status', $status);
    }
}