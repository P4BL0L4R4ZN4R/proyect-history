<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    private $responses = [
        // Preguntas técnicas
        "hola" => "¡Hola! Soy tu asistente virtual. Puedes hacer preguntas cómo: como agregar una ruta, como generar un reporte, problema con la impresora, como dar permisos a un usuario, como actualizar tarifas, como cerrar caja, error al cobrar, como cambiar datos, como reimprimir un boleto, como respaldar datos. ¿En qué puedo ayudarte?",
        "como agregar una ruta" => "Para agregar una ruta, ve a 'Configuración > Rutas' y haz clic en 'Nueva'. Completa los campos requeridos (origen, destino, tarifa).",
        "como generar un reporte" => "Puedes generar reportes en 'Reportes > Diarios/Mensuales'. Selecciona el período y exporta en PDF o Excel.",
        "problema con la impresora" => "Verifica que la impresora esté conectada y con papel. Si el problema persiste, reinicia el servicio de impresión en 'Sistema > Dispositivos'.",
        "como dar permisos a un usuario" => "Ve a 'Usuarios > Editar', selecciona el rol (Administrador, Cajero) y guarda los cambios.",
        "como actualizar tarifas" => "En 'Tarifas > Editar', modifica los valores y guarda. Los cambios aplicarán inmediatamente.",
        "error al cobrar" => "Revisa la conexión a internet. Si el error es 'Transacción rechazada', verifica los datos de la tarjeta o intenta con otro método de pago.",
        "como cerrar la caja" => "Ve a 'Caja > Cierre', revisa el resumen y confirma. El sistema generará un comprobante automáticamente.",
        "como reimprimir un boleto" => "En 'Ventas > Histórico', busca la transacción y haz clic en 'Reimprimir'. Solo disponible hasta 24 horas después.",
        "como cambiar horario" => "En 'Configuración > Horarios', edita los turnos y guarda. Nota: Esto afectará a los conductores asignados.",
        "como respaldar datos" => "Ve a 'Sistema > Respaldo'. Elige 'Generar respaldo' y descarga el archivo .bak en una ubicación segura.",

        // Redirección a WhatsApp
        "necesito mas ayuda" => "¿Requieres asesoría personalizada? <a href='https://wa.me/5219993010426' class='text-orange-500 font-bold underline' target='_blank'><strong>Contáctanos por WhatsApp</strong></a> Atención al cliente: de lunes a viernes de 8:00 am a 6:00 pm. Sabado y domingo de 9:00 a 3:00 pm",

        // Respuesta por defecto
        "default" => "No entendí tu pregunta. ¿Necesitas ayuda con algo en particular? Puedes preguntar sobre: rutas, reportes, impresora, permisos, etc."
    ];

    public function handle(Request $request)
    {
        $userMessage = strtolower(trim($request->input('message')));

        // Verificar coincidencias exactas primero
        foreach ($this->responses as $question => $answer) {
            if (str_contains($userMessage, $question)) {
                return response()->json(['response' => $answer]);
            }
        }
    }

}