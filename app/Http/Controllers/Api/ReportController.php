<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Traits\ApiResponse;
use App\Models\Criteria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EdasResultExport;

class ReportController extends Controller
{
    use ApiResponse;

    public function pdf(Request $request, Assessment $assessment)
    {

        if ($assessment->isDraft()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assessment belum dikalkulasi. Jalankan kalkulasi EDAS terlebih dahulu.',
            ], 409);
        }

        $results = $assessment->rankedResults()
            ->with('alternative:id,name,description')
            ->get();

        $criteria = Criteria::orderBy('id')->get();

        $pdf = Pdf::loadView('reports.edas-pdf', [
            'assessment' => $assessment->load('owner:id,name,email'),
            'results' => $results,
            'criteria' => $criteria,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('hasil-edas-' . $assessment->id . '.pdf');
    }

    public function excel(Request $request, Assessment $assessment)
    {

        if ($assessment->isDraft()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assessment belum dikalkulasi. Jalankan kalkulasi EDAS terlebih dahulu.',
            ], 409);
        }

        return Excel::download(
            new EdasResultExport($assessment),
            'hasil-edas-' . $assessment->id . '.xlsx'
        );
    }
}
