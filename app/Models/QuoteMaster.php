<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteMaster extends Model
{
    use HasFactory;

    protected $table = "quote_master";

    protected $fillable = [
        'sku',
        'module',
        'kw',
        'module_count',
        'value',
        'taxes',
        'metering_cost',
        'mcb_ppa',
        'payable',
        'subsidy',
        'projected',
        'meta',
    ];

    protected $casts = [
        'kw'             => 'decimal:2',
        'value'          => 'decimal:2',
        'taxes'          => 'decimal:2',
        'metering_cost'  => 'decimal:2',
        'mcb_ppa'        => 'decimal:2',
        'payable'        => 'decimal:2',
        'subsidy'        => 'decimal:2',
        'projected'      => 'decimal:2',
        'meta'           => 'array',
    ];

    /**
     * Auto-generate SKU if missing
     */
    public function getSkuAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }

        try {
            $module = $this->module ? explode(' ', $this->module)[0] : 'MOD';
            $kw     = $this->kw ?? 0;
            $count  = $this->module_count ?? 0;

            return "{$module}-{$kw}-MC-{$count}";
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Accessor: final payable with subsidy applied
     */
    public function getNetPayableAttribute()
    {
        return ($this->payable ?? 0) - ($this->subsidy ?? 0);
    }

    /**
     * Accessor: price formatting
     */
    public function getFormattedValueAttribute()
    {
        return number_format($this->value ?? 0, 2);
    }

    /**
     * Relationship: which quotations used this master?
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'quote_master_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'quote_master_id');
    }
}
