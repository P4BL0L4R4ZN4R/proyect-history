<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use App\Models\Sale;

class StripeController extends Controller
{
    public function __construct()
    {
        // ✅ CONFIGURACIÓN MANUAL - Claves directas
        $stripeSecret = 'sk_test_51SCrD919HZqLwOqTGoe8kZm0tCaX4Q9GNUAGfITpAZLpgIqJ1equsTyQNEvqMWNK5roU1aw6vzhdxxIePIt9D94800hSVxcIQC';
        Stripe::setApiKey($stripeSecret);

        Log::info('[STRIPE] API Key configurada manualmente');
    }

    /**
     * Crear un Payment Intent de Stripe
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            Log::info('[STRIPE] Creando Payment Intent', $request->all());

            $validated = $request->validate([
                'amount' => 'required|integer|min:50', // Mínimo 50 centavos
                'currency' => 'sometimes|string|size:3',
                'metadata' => 'sometimes|array'
            ]);

            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'],
                'currency' => $validated['currency'] ?? 'mxn',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => $validated['metadata'] ?? [],
            ]);

            Log::info('[STRIPE] Payment Intent creado exitosamente', [
                'id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'id' => $paymentIntent->id,
                'status' => $paymentIntent->status
            ]);

        } catch (ApiErrorException $e) {
            Log::error('[STRIPE] Error de API: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('[STRIPE] Error inesperado: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar pago y crear la venta
     */
public function confirmPayment(Request $request)
{
    try {
        \Log::info('📥 [DEBUG] Confirmando pago - DATOS RECIBIDOS:', $request->all());

        // DEBUG: Log detallado de sale_data
        \Log::info('🔍 [DEBUG] sale_data contenido:', $request->input('sale_data', []));

        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
            'sale_data' => 'required|array'
        ]);

        \Log::info('✅ [DEBUG] Datos validados correctamente');

        // Verificar el estado del pago en Stripe
        $paymentIntent = PaymentIntent::retrieve($validated['payment_intent_id']);
        \Log::info('🔍 [DEBUG] Estado de PaymentIntent: ' . $paymentIntent->status);

        if ($paymentIntent->status !== 'succeeded') {
            \Log::error('❌ [DEBUG] PaymentIntent no succeeded: ' . $paymentIntent->status);
            return response()->json([
                'success' => false,
                'message' => 'El pago no fue exitoso. Estado: ' . $paymentIntent->status
            ], 400);
        }

        // DEBUG: Verificar datos antes de crear la venta
        $saleData = $validated['sale_data'];
        \Log::info('🔍 [DEBUG] Datos para crear venta:', $saleData);

        // Validar campos requeridos para la venta
        $requiredFields = ['user_id', 'route_unit_schedule_id', 'rate_id', 'amount', 'payment_id'];
        foreach ($requiredFields as $field) {
            if (!isset($saleData[$field])) {
                \Log::error("❌ [DEBUG] Campo requerido faltante: $field");
                return response()->json([
                    'success' => false,
                    'message' => "Campo requerido faltante: $field"
                ], 400);
            }
        }

        // Crear la venta directamente en lugar de llamar a otro controlador
        $saleData['folio'] = \App\Models\Sale::generateFolio();
        $saleData['payment_intent_id'] = $validated['payment_intent_id'];

        \Log::info('💾 [DEBUG] Creando venta con datos:', $saleData);

        $sale = \App\Models\Sale::create($saleData);

        \Log::info('✅ [DEBUG] Venta creada exitosamente:', ['id' => $sale->id, 'folio' => $sale->folio]);

        return response()->json([
            'success' => true,
            'folio' => $sale->folio,
            'sale_id' => $sale->id,
            'message' => 'Pago confirmado y venta registrada exitosamente'
        ]);

    } catch (\Exception $e) {
        \Log::error('💥 [DEBUG] Error en confirmPayment:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error al confirmar el pago: ' . $e->getMessage()
        ], 500);
    }
}
}
