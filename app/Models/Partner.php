<?php
// app/Models/Partner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_unit_id',
        'name',
        'percentage',
        'lembar_saham'
    ];

    protected $casts = [
        'percentage' => 'integer'
    ];

    public function businessUnit()
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function getFormattedPercentageAttribute()
    {
        return number_format($this->percentage, 0) . '%';
    }

    public function getFormattedLembarSahamAttribute()
    {
        return $this->lembar_saham ? number_format($this->lembar_saham, 0, ',', '.') : '-';
    }
}