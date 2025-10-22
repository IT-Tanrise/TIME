<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApCreditor extends Model
{
    protected $table = 'mgr_ap_creditor';
    protected $primaryKey = 'creditor_acct';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'creditor_acct',
        'assoc_id',
        'contra_acct',
        'name',
        'address1',
        'address2',
        'address3',
        'post_cd',
        'telephone_no',
        'telex_no',
        'fax_no',
        'contact_person',
        'type',
        'bal_amt',
        'claim_amt',
        'retention_amt',
        'ret_release_amt',
        'inv_amt',
        'payment_amt',
        'dn_amt',
        'cn_amt',
        'contra_amt',
        'deposit_amt',
        'advance_amt',
        'tax_amt',
        'forex_amt',
        'currency_cd',
        'tax_cd',
        'contractor_flag',
        'cr_term',
        'cr_limit',
        'remarks',
        'pay_to',
        'hold_flag',
        'markup',
        'audit_user',
        'audit_date',
        'reason_cd',
        'pay_to_address1',
        'pay_to_address2',
        'pay_to_address3',
        'pay_to_postcd',
        'tax_on_adv',
        'deduct_on_adv',
        'rowID',
        'register_as',
        'agent_cd',
        'siujk_no',
        'siujk_exp',
        'active_status',
        'abbrv_creditor_acct',
        'audit_user_abbrv',
        'audit_date_abbrv',
    ];

    protected $casts = [
        'bal_amt' => 'decimal:2',
        'claim_amt' => 'decimal:2',
        'retention_amt' => 'decimal:2',
        'ret_release_amt' => 'decimal:2',
        'inv_amt' => 'decimal:2',
        'payment_amt' => 'decimal:2',
        'dn_amt' => 'decimal:2',
        'cn_amt' => 'decimal:2',
        'contra_amt' => 'decimal:2',
        'deposit_amt' => 'decimal:2',
        'advance_amt' => 'decimal:2',
        'tax_amt' => 'decimal:2',
        'forex_amt' => 'decimal:2',
        'cr_limit' => 'decimal:2',
        'markup' => 'decimal:2',
        'tax_on_adv' => 'decimal:2',
        'deduct_on_adv' => 'decimal:2',
        'audit_date' => 'datetime',
        'siujk_exp' => 'datetime',
        'audit_date_abbrv' => 'datetime',
    ];

    // Relationships
    public function creditorType(): BelongsTo
    {
        return $this->belongsTo(ApCreditorType::class, 'type', 'creditor_type');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(PlContract::class, 'contractor_id', 'creditor_acct');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PoOrderhd::class, 'supplier_cd', 'creditor_acct');
    }

    // Accessor for full address
    public function getFullAddressAttribute(): string
    {
        return trim(implode(', ', array_filter([
            $this->address1,
            $this->address2,
            $this->address3,
        ])));
    }

    // Accessor for formatted balance
    public function getFormattedBalAmtAttribute(): string
    {
        return 'Rp ' . number_format($this->bal_amt, 0, ',', '.');
    }

    // Get total projects count
    public function getTotalProjectsAttribute(): int
    {
        return $this->contracts()->distinct('project_no')->count('project_no');
    }

    // Get type description
    public function getTypeDescriptionAttribute(): string
    {
        return $this->creditorType?->descs ?? 'Unknown';
    }
}