<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'content',
        'start_date',
        'end_date',
        'status',
        'target_criteria',
        'metrics',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'target_criteria' => 'array',
        'metrics' => 'array',
    ];

    /**
     * Os clientes associados a esta campanha.
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'campaign_customer')
            ->withPivot('sent', 'sent_at', 'opened', 'opened_at', 'clicked', 'clicked_at', 'converted', 'converted_at')
            ->withTimestamps();
    }

    /**
     * Escopo para campanhas ativas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Escopo para campanhas em rascunho.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Escopo para campanhas concluídas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Escopo para campanhas canceladas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Verifica se a campanha está ativa.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->start_date->isPast() && 
               ($this->end_date === null || $this->end_date->isFuture());
    }

    /**
     * Retorna a taxa de abertura da campanha.
     *
     * @return float
     */
    public function getOpenRate()
    {
        $total = $this->customers()->wherePivot('sent', true)->count();
        $opened = $this->customers()->wherePivot('opened', true)->count();
        
        return $total > 0 ? ($opened / $total) * 100 : 0;
    }

    /**
     * Retorna a taxa de clique da campanha.
     *
     * @return float
     */
    public function getClickRate()
    {
        $total = $this->customers()->wherePivot('sent', true)->count();
        $clicked = $this->customers()->wherePivot('clicked', true)->count();
        
        return $total > 0 ? ($clicked / $total) * 100 : 0;
    }

    /**
     * Retorna a taxa de conversão da campanha.
     *
     * @return float
     */
    public function getConversionRate()
    {
        $total = $this->customers()->wherePivot('sent', true)->count();
        $converted = $this->customers()->wherePivot('converted', true)->count();
        
        return $total > 0 ? ($converted / $total) * 100 : 0;
    }
}
