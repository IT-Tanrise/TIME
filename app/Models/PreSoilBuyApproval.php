<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PreSoilBuyCashOut;

class PreSoilBuyApproval extends Model
{
    use HasFactory;


    protected $table = 'pre_soil_buy_approvals';

    protected $fillable = [
        'pre_soil_buy_id',
        'requested_by',
        'responded_by',
        'change_type',
        'status',
        'reason',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function preSoilBuy()
    {
        return $this->belongsTo(PreSoilBuy::class, 'pre_soil_buy_id');
    }



    // Scopes
    public function scopePending($query)
    {
        return $query->where('pre_soil_buy_approval.status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('pre_soil_buy_approval.status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('pre_soil_buy_approval.status', 'rejected');
    }

    // Accessors
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    public function getFormattedApprovedAtAttribute()
    {
        return $this->approved_at ? $this->approved_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)' : null;
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }


    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'responded_by' => $approverId,
            'responded_at' => now(),
            'reason' => $reason
        ]);
    }


    public function approve($approverId, $reason = null)
    {
        $this->update([
            'status' => 'approved',
            'responded_by' => $approverId,
            'responded_at' => now(),
            'reason' => $reason,
        ]);


        if ($this->change_type === 'create') {
            PreSoilBuyCashOut::create([
                'pre_soil_buy_id' => $this->pre_soil_buy_id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
                'responded_at' => null,
            ]);
        }
    }
}
