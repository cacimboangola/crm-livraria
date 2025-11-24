<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Cria uma nova notificação para um usuário específico.
     *
     * @param  int  $userId
     * @param  string  $type
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $link
     * @param  int|null  $senderId
     * @return \App\Models\Notification
     */
    public function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?int $senderId = null
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
        ]);
    }

    /**
     * Envia uma notificação para vários usuários.
     *
     * @param  array|Collection  $userIds
     * @param  string  $type
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $link
     * @param  int|null  $senderId
     * @return Collection
     */
    public function notifyMany(
        $userIds,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?int $senderId = null
    ): Collection {
        $notifications = collect();

        foreach ($userIds as $userId) {
            $notifications->push($this->createNotification(
                $userId,
                $type,
                $title,
                $message,
                $link,
                $senderId
            ));
        }

        return $notifications;
    }

    /**
     * Notifica todos os usuários com um papel específico.
     *
     * @param  string  $role
     * @param  string  $type
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $link
     * @param  int|null  $senderId
     * @return Collection
     */
    public function notifyRole(
        string $role,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?int $senderId = null
    ): Collection {
        $userIds = User::where('role', $role)->pluck('id')->toArray();
        
        return $this->notifyMany(
            $userIds,
            $type,
            $title,
            $message,
            $link,
            $senderId
        );
    }

    /**
     * Notifica todos os administradores.
     *
     * @param  string  $type
     * @param  string  $title
     * @param  string  $message
     * @param  string|null  $link
     * @param  int|null  $senderId
     * @return Collection
     */
    public function notifyAdmins(
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?int $senderId = null
    ): Collection {
        return $this->notifyRole('admin', $type, $title, $message, $link, $senderId);
    }

    /**
     * Marca uma notificação como lida.
     *
     * @param  int  $notificationId
     * @return \App\Models\Notification|null
     */
    public function markAsRead(int $notificationId): ?Notification
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        return $notification;
    }

    /**
     * Marca todas as notificações de um usuário como lidas.
     *
     * @param  int  $userId
     * @return int
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Obtém todas as notificações de um usuário.
     *
     * @param  int  $userId
     * @param  int  $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserNotifications(int $userId, int $limit = 15)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Obtém as notificações não lidas de um usuário.
     *
     * @param  int  $userId
     * @param  int  $limit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUnreadNotifications(int $userId, int $limit = 15)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Conta as notificações não lidas de um usuário.
     *
     * @param  int  $userId
     * @return int
     */
    public function countUnreadNotifications(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Exclui uma notificação.
     *
     * @param  int  $notificationId
     * @return bool
     */
    public function deleteNotification(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if ($notification) {
            return $notification->delete();
        }
        
        return false;
    }

    /**
     * Exclui todas as notificações lidas de um usuário.
     *
     * @param  int  $userId
     * @return int
     */
    public function deleteReadNotifications(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', true)
            ->delete();
    }
}
