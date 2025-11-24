<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoint extends Model
{
    use SoftDeletes;
    
    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'points',
        'points_spent',
        'points_expired',
        'current_balance',
        'level',
        'level_expires_at',
    ];
    
    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'level_expires_at' => 'date',
    ];
    
    /**
     * Obtém o cliente associado aos pontos de fidelidade.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Obtém as transações de pontos de fidelidade associadas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'customer_id', 'customer_id');
    }
    
    /**
     * Verifica se o cliente tem pontos suficientes para resgatar.
     *
     * @param int $points
     * @return bool
     */
    public function hasEnoughPoints(int $points): bool
    {
        return $this->current_balance >= $points;
    }
    
    /**
     * Atualiza o nível de fidelidade com base no saldo atual.
     *
     * @return void
     */
    public function updateLevel(): void
    {
        $balance = $this->current_balance;
        
        if ($balance >= 5000) {
            $this->level = 'platinum';
        } elseif ($balance >= 2000) {
            $this->level = 'gold';
        } elseif ($balance >= 500) {
            $this->level = 'silver';
        } else {
            $this->level = 'bronze';
        }
        
        // Define a data de expiração do nível para 1 ano a partir de agora
        $this->level_expires_at = now()->addYear();
        $this->save();
    }
}
