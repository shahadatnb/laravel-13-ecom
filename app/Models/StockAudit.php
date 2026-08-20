<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAudit extends Model
{
    use HasFactory;

    protected $table = 'stock_audits';

    protected $fillable = [
        'audit_number',
        'warehouse_id',
        'product_id',
        'system_stock',
        'physical_stock',
        'difference',
        'status',
        'notes',
        'audited_by',
        'verified_by',
        'audited_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'system_stock' => 'integer',
            'physical_stock' => 'integer',
            'difference' => 'integer',
            'audited_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
