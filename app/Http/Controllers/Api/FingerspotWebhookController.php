<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Fingerspot\FingerspotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FingerspotWebhookController extends Controller
{
    public function __construct(
        protected FingerspotService $fingerspotService
    ) {}

    /**
     * Endpoint Webhook Realtime untuk Fingerspot Developer Cloud.
     * Menerima notifikasi scan push dari mesin presensi.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->json()->all();

        if (empty($payload)) {
            $raw = $request->getContent();
            $payload = json_decode($raw, true) ?: [];
        }

        Log::info('[Fingerspot Webhook] Data diterima', ['payload' => $payload]);

        // Fingerspot biasanya mengirim data dalam payload['data'] atau langsung di root
        $data = $payload['data'] ?? $payload;
        $cloudId = $payload['cloud_id'] ?? ($data['cloud_id'] ?? null);

        // Jika data berupa batch array (multiple logs dalam 1 payload)
        if (isset($data[0]) && is_array($data[0])) {
            $processed = 0;
            foreach ($data as $item) {
                if (isset($item['pin'], $item['scan'])) {
                    $this->fingerspotService->processScan($item['pin'], $item['scan'], $cloudId ?: ($item['cloud_id'] ?? null));
                    $processed++;
                }
            }

            return response()->json([
                'status'    => 'success',
                'message'   => "Batch {$processed} scan diproses.",
                'timestamp' => now()->toIso8601String(),
            ], 200);
        }

        // Single scan record
        $pin = $data['pin'] ?? null;
        $scan = $data['scan'] ?? ($data['scan_date'] ?? null);

        if (! $pin || ! $scan) {
            Log::warning('[Fingerspot Webhook] Payload data scan tidak valid', ['payload' => $payload]);

            return response()->json([
                'status'  => 'invalid data',
                'message' => 'Parameter pin dan scan wajib disertakan.',
            ], 400);
        }

        $result = $this->fingerspotService->processScan($pin, $scan, $cloudId);

        return response()->json([
            'status'    => 'success',
            'action'    => $result['action'],
            'message'   => $result['message'],
            'timestamp' => now()->toIso8601String(),
        ], 200);
    }
}
