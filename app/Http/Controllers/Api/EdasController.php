<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Assessment;
use App\Models\EdasResult;
use App\Services\EdasService;
use InvalidArgumentException;
use Throwable;


class EdasController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly EdasService $edasService) {}

    public function calculate(Request $request, Assessment $assessment): JsonResponse
    {
        if ($request->user()->role !== 'admin' && $assessment->user_id !== $request->user()->id) {
            return $this->forbidden('Anda tidak memiliki akses ke assessment ini.');
        }

        try {
            $results = $this->edasService->calculate($assessment);

            return $this->success(
                data: [
                    'assessment_id' => $assessment->id,
                    'results' => $results->map(fn($r) => [
                        'rank' => $r->rank,
                        'alternative' => [
                            'id' => $r->alternative->id,
                            'name' => $r->alternative->name,
                            'description' => $r->alternative->description,
                        ],
                        'pda' => $r->pda,
                        'nda' => $r->nda,
                        'sp' => $r->sp,
                        'sn' => $r->sn,
                        'nsp' => $r->nsp,
                        'nsn' => $r->nsn,
                        'as_score' => $r->appraisal_score,
                        'quality_label' => $r->quality_label,
                        'quality_color' => $r->quality_color,
                    ]),
                ],
                message: "Kalkulasi EDAS berhasil. Ditemukan {$results->count()} alternatif."
            );
        } catch (InvalidArgumentException $e) {
            // Error validasi (matrix tidak lengkap, bobot salah, dll)
            return $this->error($e->getMessage(), 422);
        } catch (Throwable $e) {
            // Error tidak terduga — log dan kembalikan pesan generik
            report($e);
            return $this->error('Kalkulasi gagal. Silakan coba lagi.', 500);
        }
    }
    public function results(Request $request, Assessment $assessment): JsonResponse
    {
        if ($request->user()->role !== 'admin' && $assessment->user_id !== $request->user()->id) {
            return $this->forbidden();
        }

        if ($assessment->isDraft()) {
            return $this->error(
                'Assessment ini belum dikalkulasi. Jalankan kalkulasi EDAS terlebih dahulu.',
                409 // Conflict
            );
        }

        $results = $assessment->rankedResults()->with('alternative:id,name,description')->get();

        return $this->success([
            'assessment'  => [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'status' => $assessment->status,
                'status_label' => $assessment->status_label,
            ],
            'results' => $results->map(fn($r) => [
                'rank' => $r->rank,
                'alternative' => $r->alternative,
                'sp' => $r->sp,
                'sn' => $r->sn,
                'nsp' => $r->nsp,
                'nsn' => $r->nsn,
                'as_score' => $r->appraisal_score,
                'as_formatted' => $r->as_score_formatted,
                'quality_label' => $r->quality_label,
                'quality_color' => $r->quality_color,
            ]),
            'top_recommendation' => $results->first()?->alternative?->name,
        ]);
    }
}
