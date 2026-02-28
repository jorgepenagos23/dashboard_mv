<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch tokens without limit but paginate, we will just pass all tokens for easier grouping or paginate with filter later.
        // Or get them all since FCM has few thousand max. Let's stick to paginate but add direcciones.
        $tokens = FcmToken::with(['cliente' => function($q) {
            $q->select('id', 'cliente', 'razon_social', 'email')->with(['direcciones' => function($qd) {
                // only need these fields to group
                $qd->select('CTECLI_CODIGO_K', 'CTEPFR_CODIGO_K', 'CTEDIR_TIPOFIS', 'CTECLI_RAZONSOCIAL');
            }]);
        }])
        ->orderBy('creado_en', 'desc')
        ->paginate(500); // Increased from 50 to 500 to show more data since we want groups
        
        $hasServiceAccount = file_exists(storage_path('app/firebase-service-account.json'));

        return Inertia::render('Notifications/Index', [
            'tokens' => $tokens,
            'hasServiceAccount' => $hasServiceAccount
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'tokens' => 'required|array|min:1',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500'
        ]);

        $serviceAccountPath = storage_path('app/firebase-service-account.json');

        if (!file_exists($serviceAccountPath)) {
            return back()->with('error', 'Falta el archivo service-account.json en storage/app/firebase-service-account.json');
        }

        $payloadPath = storage_path('app/fcm_payload_' . uniqid() . '.json');
        
        $payload = [
            'serviceAccountPath' => $serviceAccountPath,
            'tokens' => $validated['tokens'],
            'title' => $validated['title'],
            'body' => $validated['body']
        ];

        file_put_contents($payloadPath, json_encode($payload));

        $scriptPath = storage_path('app/send_fcm.mjs');
        
        // Use symfony process
        $process = new Process(['node', $scriptPath, $payloadPath]);
        $process->setTimeout(60); // give it time for 50 requests
        $process->run();

        // Cleanup
        unlink($payloadPath);

        if (!$process->isSuccessful()) {
            return back()->with('error', 'Error al ejecutar Node.js: ' . $process->getErrorOutput());
        }

        $output = $process->getOutput();
        $result = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($result['exitosos'])) {
            return back()->with('error', 'Error interpretando respuesta Node: ' . $output);
        }

        return back()->with('success', "Enviados: {$result['exitosos']} exitosos, {$result['fallidos']} fallidos. " . ($result['error'] ?? ''));
    }
}
