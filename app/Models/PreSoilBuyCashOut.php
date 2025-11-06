<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreSoilBuyCashOut extends Model
{
    use HasFactory;

    protected $table = 'pre_soil_buy_cash_out';

    protected $fillable = [
        'pre_soil_buy_id',
        'responded_by',
        'status',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function preSoilBuy()
    {
        return $this->belongsTo(PreSoilBuy::class, 'pre_soil_buy_id');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }

    // Accessors
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    public function getFormattedRespondedAtAttribute()
    {
        return $this->responded_at ? $this->responded_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDone()
    {
        return $this->status === 'done';
    }

    // Cash Out method
    public function cashOut()
    {
        return $this->update([
            'status' => 'done',
            'responded_by' => Auth::id(),
            'responded_at' => now()
        ]);
    }
}