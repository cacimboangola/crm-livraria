<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'link',
        'read',
        'read_at',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Obtém o usuário destinatário da notificação.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o usuário remetente da notificação.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Marca a notificação como lida.
     *
     * @return $this
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->read = true;
            $this->read_at = now();
            $this->save();
        }

        return $this;
    }

    /**
     * Marca a notificação como não lida.
     *
     * @return $this
     */
    public function markAsUnread()
    {
        if ($this->read) {
            $this->read = false;
            $this->read_at = null;
            $this->save();
        }

        return $this;
    }

    /**
     * Escopo para notificações não lidas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Escopo para notificações lidas.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }
}
