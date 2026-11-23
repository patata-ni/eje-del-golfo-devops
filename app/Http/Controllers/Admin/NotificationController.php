<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $staff = \App\Models\Staff::where('correo', Auth::user()->email)->first();

        $adminId = $staff ? $staff->id_personal : 1;

        $notificaciones = Notification::where('personal_id', $adminId)
                            ->orderBy('fecha', 'desc')
                            ->get();

        Notification::where('personal_id', $adminId)->update(['leida' => 1]);

        return view('admin.notifications.index', compact('notificaciones'));
    }

    public function clientMessages()
    {
        $staff = \App\Models\Staff::where('correo', Auth::user()->email)->first();
        $adminId = $staff ? $staff->id_personal : 1;

        $mensajes = Notification::where('personal_id', $adminId)
                        ->where('tipo', 'mensaje_cliente')
                        ->orderBy('fecha', 'desc')
                        ->get();

        Notification::where('personal_id', $adminId)
                    ->where('tipo', 'mensaje_cliente')
                    ->update(['leida' => 1]);

        return view('admin.notifications.messages', compact('mensajes'));
    }
}