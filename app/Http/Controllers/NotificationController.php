<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    /**
     * Construtor.
     *
     * @param  \App\Services\NotificationService  $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Exibe todas as notificações do usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications(Auth::id());
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marca uma notificação como lida.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = $this->notificationService->markAsRead($id);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notificação marcada como lida.');
    }

    /**
     * Marca todas as notificações como lidas.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'count' => $count]);
        }
        
        return redirect()->back()->with('success', $count . ' notificações marcadas como lidas.');
    }

    /**
     * Exclui uma notificação.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success = $this->notificationService->deleteNotification($id);
        
        if (request()->ajax()) {
            return response()->json(['success' => $success]);
        }
        
        if ($success) {
            return redirect()->back()->with('success', 'Notificação excluída com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Não foi possível excluir a notificação.');
        }
    }

    /**
     * Exclui todas as notificações lidas.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyRead()
    {
        $count = $this->notificationService->deleteReadNotifications(Auth::id());
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'count' => $count]);
        }
        
        return redirect()->back()->with('success', $count . ' notificações excluídas com sucesso.');
    }

    /**
     * Retorna as notificações não lidas para o dropdown de notificações.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUnreadNotifications()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id(), 5);
        $count = $this->notificationService->countUnreadNotifications(Auth::id());
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
}
