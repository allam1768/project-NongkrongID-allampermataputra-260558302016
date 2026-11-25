<?php

namespace App\Http\Controllers;

use App\Services\AiBaristaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiBaristaController extends Controller
{
    public function __construct(
        protected AiBaristaService $aiService
    ) {}

    /**
     * Handle AI chat recommendations from web/mobile clients
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'context' => 'nullable|string|max:50',
            'table_number' => 'nullable|string|max:20',
        ]);

        $response = $this->aiService->chat(
            query: $validated['message'],
            context: $validated['context'] ?? 'order',
            tableNumber: $validated['table_number'] ?? null,
        );

        $aiText = $response['text'] ?? ($response['message'] ?? '');

        if (!empty($aiText)) {
            $tableId = null;
            if (!empty($validated['table_number'])) {
                $tableId = \App\Models\Table::where('table_number', $validated['table_number'])->value('id');
            }

            $recommendedIds = [];
            if (!empty($response['menuId'])) {
                $recommendedIds[] = (int) $response['menuId'];
            } elseif (!empty($response['recommendations']) && is_array($response['recommendations'])) {
                $recommendedIds = collect($response['recommendations'])->pluck('id')->filter()->map(fn($v) => (int) $v)->values()->all();
            }

            try {
                \App\Models\AiConsultation::create([
                    'table_id' => $tableId,
                    'user_prompt' => $validated['message'],
                    'ai_response' => $aiText,
                    'recommended_ids' => !empty($recommendedIds) ? $recommendedIds : null,
                    'is_converted' => false,
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed saving AiConsultation: ' . $e->getMessage());
            }
        }

        return response()->json($response);
    }
}
