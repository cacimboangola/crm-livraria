<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_count',
        'usage_limit_per_user',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com o usuário que criou o cupom
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com clientes que usaram o cupom
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'coupon_customer')
            ->withPivot(['invoice_id', 'discount_applied', 'used_at'])
            ->withTimestamps();
    }

    /**
     * Relacionamento com faturas onde o cupom foi usado
     */
    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            'coupon_customer',
            'coupon_id',
            'id',
            'id',
            'invoice_id'
        );
    }

    /**
     * Scope para cupons ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para cupons válidos (dentro do período e com usos disponíveis)
     */
    public function scopeValid($query)
    {
        $today = Carbon::today();
        
        return $query->active()
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    /**
     * Verifica se o cupom está válido
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();

        if ($this->start_date && $this->start_date->gt($today)) {
            return false;
        }

        if ($this->end_date && $this->end_date->lt($today)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se o cliente pode usar o cupom
     */
    public function canBeUsedByCustomer(Customer $customer): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->usage_limit_per_user) {
            $usageCount = $this->customers()
                ->where('customer_id', $customer->id)
                ->count();

            if ($usageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcula o desconto para um valor de pedido
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->min_order_value && $orderTotal < $this->min_order_value) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $orderTotal * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        // Aplicar limite máximo de desconto
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        // O desconto não pode ser maior que o total do pedido
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return round($discount, 2);
    }

    /**
     * Registra o uso do cupom
     */
    public function recordUsage(Customer $customer, ?Invoice $invoice = null, float $discountApplied = 0): void
    {
        $this->customers()->attach($customer->id, [
            'invoice_id' => $invoice?->id,
            'discount_applied' => $discountApplied,
            'used_at' => now(),
        ]);

        $this->increment('usage_count');
    }

    /**
     * Retorna o tipo formatado
     */
    public function getTypeFormattedAttribute(): string
    {
        return $this->type === 'percentage' ? 'Percentual' : 'Valor Fixo';
    }

    /**
     * Retorna o valor formatado
     */
    public function getValueFormattedAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return 'Kz ' . number_format($this->value, 2, ',', '.');
    }

    /**
     * Retorna o status formatado
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inativo';
        }

        $today = Carbon::today();

        if ($this->start_date && $this->start_date->gt($today)) {
            return 'Agendado';
        }

        if ($this->end_date && $this->end_date->lt($today)) {
            return 'Expirado';
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return 'Esgotado';
        }

        return 'Ativo';
    }

    /**
     * Retorna a classe CSS do badge de status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Ativo' => 'bg-success',
            'Agendado' => 'bg-info',
            'Expirado' => 'bg-secondary',
            'Esgotado' => 'bg-warning',
            'Inativo' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
