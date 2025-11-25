<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialOrder extends Model
{
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'user_id',
        'book_title',
        'book_author',
        'book_isbn',
        'book_publisher',
        'quantity',
        'estimated_price',
        'customer_notes',
        'supplier_notes',
        'status',
        'ordered_at',
        'received_at',
        'notified_at',
        'delivered_at',
        'delivery_preference',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'estimated_price' => 'decimal:2',
        'ordered_at' => 'datetime',
        'received_at' => 'datetime',
        'notified_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Status disponíveis para o pedido especial.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_ORDERED = 'ordered';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_NOTIFIED = 'notified';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Obter o cliente associado ao pedido.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Obter o funcionário que criou o pedido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna o status formatado em português.
     */
    public function getStatusFormattedAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Aguardando Encomenda',
            self::STATUS_ORDERED => 'Encomendado ao Fornecedor',
            self::STATUS_RECEIVED => 'Recebido na Loja',
            self::STATUS_NOTIFIED => 'Cliente Notificado',
            self::STATUS_DELIVERED => 'Entregue',
            self::STATUS_CANCELLED => 'Cancelado',
            default => $this->status,
        };
    }

    /**
     * Retorna a classe CSS do badge de status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_ORDERED => 'bg-info text-white',
            self::STATUS_RECEIVED => 'bg-primary text-white',
            self::STATUS_NOTIFIED => 'bg-secondary text-white',
            self::STATUS_DELIVERED => 'bg-success text-white',
            self::STATUS_CANCELLED => 'bg-danger text-white',
            default => 'bg-secondary',
        };
    }

    /**
     * Retorna a preferência de entrega formatada.
     */
    public function getDeliveryPreferenceFormattedAttribute(): string
    {
        return match ($this->delivery_preference) {
            'pickup' => 'Retirada na Loja',
            'delivery' => 'Entrega em Domicílio',
            default => $this->delivery_preference,
        };
    }

    /**
     * Verifica se o pedido pode ser cancelado.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_ORDERED]);
    }

    /**
     * Verifica se o pedido pode avançar para o próximo status.
     */
    public function canAdvanceStatus(): bool
    {
        return !in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    /**
     * Retorna o próximo status possível.
     */
    public function getNextStatusAttribute(): ?string
    {
        return match ($this->status) {
            self::STATUS_PENDING => self::STATUS_ORDERED,
            self::STATUS_ORDERED => self::STATUS_RECEIVED,
            self::STATUS_RECEIVED => self::STATUS_NOTIFIED,
            self::STATUS_NOTIFIED => self::STATUS_DELIVERED,
            default => null,
        };
    }

    /**
     * Scope para pedidos pendentes.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope para pedidos ativos (não entregues/cancelados).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    /**
     * Scope para pedidos que precisam de ação.
     */
    public function scopeNeedsAction($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_RECEIVED]);
    }
}
