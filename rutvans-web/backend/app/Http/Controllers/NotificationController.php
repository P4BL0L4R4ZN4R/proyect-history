<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

use log;

class NotificationController extends Controller
{
    //



    public function data()
    {
        $notifications = DB::table('notifications')
                            ->orderBy('updated_at', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count(),
            'message' => 'Notificaciones obtenidas correctamente'
        ]);
    }

    public function dataNotRead()
    {
        $notifications = DB::table('notifications')
                            ->where('status', 'notread')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count(),
            'message' => 'Notificaciones no leídas obtenidas correctamente'
        ]);
    }

    public function dataRead($id)
    {
        $updated = DB::table('notifications')
                        ->where('id', $id)
                        ->update(['status' => 'read']);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída correctamente'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar la notificación como leída'
            ], 400);
        }
    }


    public function dataNavBar()
    {
        $notifications = DB::table('notifications')
            ->where('status', 'notread')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $totalCount = DB::table('notifications')
            ->where('status', 'notread')
            ->count();

        $dropdownHtml = "";

        if ($notifications->count() > 0) {

            foreach ($notifications as $n) {

                $categoryTitle = match ($n->category) {
                    'pago'    => 'Nuevo pago registrado',
                    'envio'   => 'Actualización de envío',
                    'horario' => 'Cambio de horario',
                    default   => 'Notificación',
                };

                $message = $n->message ?? '';
                $time = Carbon::parse($n->created_at)->diffForHumans();

                $dropdownHtml .= '
                    <iframe name="hidden-iframe-'.$n->id.'" style="display:none;"></iframe>

                    <form id="read-'.$n->id.'" action="'.route('notifications.markRead', $n->id).'"
                        method="POST" target="hidden-iframe-'.$n->id.'" style="display:none;">
                        '.csrf_field().'
                        <input type="hidden" name="_method" value="PUT">
                    </form>

                    <a href="#" class="dropdown-item"
                    onclick="event.preventDefault(); event.stopPropagation(); document.getElementById(\'read-'.$n->id.'\').submit();">
                        <i class="fas fa-bell text-info mr-2"></i>
                        <strong>'.$categoryTitle.'</strong><br>
                        <small>'.Str::limit($message, 40).'</small>
                        <div><small class="text-muted">'.$time.'</small></div>
                    </a>

                    <div class="dropdown-divider"></div>
                ';

            }

        } else {

            $dropdownHtml .= '
                <a href="#" class="dropdown-item text-success">
                    <i class="fas fa-check mr-2"></i>
                    Sin notificaciones nuevas
                </a>
            ';
        }

        return [
            'label' => $totalCount,
            'label_color' => $totalCount > 0 ? 'danger' : 'success',
            'icon_color' => 'warning',
            'dropdown' => $dropdownHtml
        ];
    }

    public function registrarnotificacion($message, $category)
    {
        DB::table('notifications')->insert([
            'message' => $message,
            'category' => $category,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return "Notificación registrada exitosamente";
    }

}
