<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use App\Models\Assessment;
use App\Models\Criteria;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Assessment::withSummary();

        if (! $user->isAdmin()) {
            $query->forUser($user->id);
        }

        $recentAssessments = $query->orderBy('created_at', 'desc')->take(5)->get();

        $stats = [
            'total_assessments' => $user->isAdmin()
                ? Assessment::count()
                : Assessment::forUser($user->id)->count(),
            'completed_assessments' => $user->isAdmin()
                ? Assessment::completed()->count()
                : Assessment::completed()->forUser($user->id)->count(),
            'draft_assessments' => $user->isAdmin()
                ? Assessment::draft()->count()
                : Assessment::draft()->forUser($user->id)->count(),
            'total_alternatives' => Alternative::count(),
            'total_criteria' => Criteria::count(),
        ];

        $criteria = Criteria::orderBy('id')->get();
        $totalWeight = $criteria->sum('weight');

        return view('dashboard', compact('recentAssessments', 'stats', 'criteria', 'totalWeight'));
    }
}
