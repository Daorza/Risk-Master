<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AuditLog;
use App\Models\Criteria;
use App\Models\EdasResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class EdasService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private const FLOAT_PRECISION = 10;

    public function calculate(Assessment $assessment): Collection
    {
        $this->validateAssessment($assessment);

        return DB::transaction(function () use ($assessment) {
            $criteria = Criteria::orderBy('id')->get();
            $alternatives = $assessment->alternatives()->orderBy('id')->get();

            $valueRows = $assessment->alternativeValues()
                ->with(['alternative', 'criteria'])
                ->get();

            $matrix = $this->buildDecisionMatrix($alternatives, $criteria, $valueRows);

            $av = $this->calculateAverageSolution($matrix, $criteria);

            [$pda, $nda] = $this->calculatedDistances($matrix, $av, $criteria, $alternatives);

            [$sp, $sn] = $this->calculateWeightedSums($pda, $nda, $criteria, $alternatives);

            [$nsp, $nsn] = $this->normalize($sp, $sn, $alternatives);

            $ranked = $this->calculateAppraisalScores($nsp, $nsn, $alternatives);

            $saved = $this->persistResults($assessment, $ranked, $sp, $sn, $nsp, $nsn, $pda, $nda);

            $assessment->markAsCompleted();

            AuditLog::record(
                action: 'calculate_edas',
                tableName: 'assessments',
                recordId: $assessment->id,
                newData: [
                    'alternatives_count' => $alternatives->count(),
                    'criteria_count' => $criteria->count(),
                    'top_alternative_id' => $ranked[0]['alt_id'] ?? null,
                    'top_appraisal_score' => $ranked[0]['appraisal_score'] ?? null,
                ],
            );

            return $saved;
        });
    }

    private function buildDecisionMatrix(
        Collection $alternatives,
        Collection $criteria,
        Collection $valueRows
    ): array {
        $matrix = [];
        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                $matrix[$alt->id][$crit->id] = null;
            }
        }

        foreach ($valueRows as $row) {
            if (
                array_key_exists($row->alternative_id, $matrix) &&
                array_key_exists($row->criteria_id, $matrix[$row->alternative_id])
            ) {
                $matrix[$row->alternative_id][$row->criteria_id] = (float) $row->value;
            }
        }

        foreach ($alternatives as $alt) {
            foreach ($criteria as $crit) {
                if ($matrix[$alt->id][$crit->id] === null) {
                    throw new InvalidArgumentException(
                        "Nilai untuk alternatif '{$alt->name}' pada kriteria '{$crit->name}' belum diisi."
                    );
                }
            }
        }

        return $matrix;
    }

    private function calculateAverageSolution(array $matrix, Collection $criteria): array
    {
        $m = count($matrix);
        $av = [];

        foreach ($criteria as $crit) {
            $sum = 0.0;
            foreach ($matrix as $altValues) {
                $sum += $altValues[$crit->id];
            }
            $av[$crit->id] = $m > 0 ? $sum / $m : 0.0;
        }

        return $av;
    }

    private function calculatedDistances(
        array $matrix,
        array $av,
        Collection $criteria,
        Collection $alternatives
    ): array {
        $pda = [];
        $nda = [];

        foreach ($alternatives as $alt) {
            $pda[$alt->id] = [];
            $nda[$alt->id] = [];

            foreach ($criteria as $crit) {
                $x = $matrix[$alt->id][$crit->id];
                $avg = $av[$crit->id];

                if (abs($avg) < PHP_FLOAT_EPSILON) {
                    $pda[$alt->id][$crit->id] = 0.0;
                    $nda[$alt->id][$crit->id] = 0.0;
                    continue;
                }

                 if ($crit->isBenefit()) {
                    $pda[$alt->id][$crit->id] = max(0.0, ($x - $avg)) / $avg;
                    $nda[$alt->id][$crit->id] = max(0.0, ($avg - $x)) / $avg;
                } else {
                    $pda[$alt->id][$crit->id] = max(0.0, ($avg - $x)) / $avg;
                    $nda[$alt->id][$crit->id] = max(0.0, ($x - $avg)) / $avg;
                }
            }
        }

        return [$pda, $nda];
    }

    private function calculateWeightedSums(
        array $pda,
        array $nda,
        Collection $criteria,
        Collection $alternatives
    ): array {
        $sp = [];
        $sn = [];

        foreach ($alternatives as $alt) {
            $spSum = 0.0;
            $snSum = 0.0;

            foreach ($criteria as $crit) {
                $w = (float) $crit->weight;
                $spSum += $w * $pda[$alt->id][$crit->id];
                $snSum += $w * $nda[$alt->id][$crit->id];
            }

            $sp[$alt->id] = $spSum;
            $sn[$alt->id] = $snSum;
        }

        return [$sp, $sn];
    }

    private function normalize(
        array $sp,
        array $sn,
        Collection $alternatives
    ): array {
        $maxSp = max($sp);
        $maxSn = max($sn);

        $nsp = [];
        $nsn = [];

        foreach ($alternatives as $alt) {
            $nsp[$alt->id] = ($maxSp > PHP_FLOAT_EPSILON)
                ? $sp[$alt->id] / $maxSp
                : 0.0;

            $nsn[$alt->id] = ($maxSn > PHP_FLOAT_EPSILON)
                ? 1.0 - ($sn[$alt->id] / $maxSn)
                : 1.0;
        }

        return [$nsp, $nsn];
    }

    private function calculateAppraisalScores(
        array $nsp,
        array $nsn,
        Collection $alternatives
    ): array {
        $scores = [];

        foreach ($alternatives as $alt) {
            $scores[] = [
                'alt_id' => $alt->id,
                'appraisal_score' => ($nsp[$alt->id] + $nsn[$alt->id]) / 2.0,
            ];
        }

        usort($scores, fn ($a, $b) => $b['appraisal_score'] <=> $a['appraisal_score']);

        $rank = 1;
        $prevScore = null;

        foreach ($scores as $i => &$entry) {
            $currentScore = round($entry['appraisal_score'], self::FLOAT_PRECISION);

            if ($prevScore === null || $currentScore !== $prevScore) {
                $entry['rank'] = $rank;
            } else {
                $entry['rank'] = $scores[$i - 1]['rank'];
            }

            $prevScore = $currentScore;
            $rank++;
        }
        unset($entry);

        return $scores;
    }

    private function persistResults(
        Assessment $assessment,
        array $ranked,
        array $sp,
        array $sn,
        array $nsp,
        array $nsn,
        array $pda,
        array $nda
    ): Collection {
        EdasResult::where('assessment_id', $assessment->id)->delete();

        $pdaAgg = $this->aggregatePdaNda($pda);
        $ndaAgg = $this->aggregatePdaNda($nda);

        $now = now();
        $insert = [];

        foreach ($ranked as $entry) {
            $altId = $entry['alt_id'];
            $insert[] = [
                'assessment_id' => $assessment->id,
                'alternative_id' => $altId,
                'pda' => round($pdaAgg[$altId], 6),
                'nda' => round($ndaAgg[$altId], 6),
                'sp' => round($sp[$altId], 6),
                'sn' => round($sn[$altId], 6),
                'nsp' => round($nsp[$altId], 6),
                'nsn' => round($nsn[$altId], 6),
                'appraisal_score' => round($entry['appraisal_score'], 6),
                'rank' => $entry['rank'],
                'calculated_at' => $now,
            ];
        }

        EdasResult::insert($insert);

        return EdasResult::where('assessment_id', $assessment->id)
            ->with('alternative:id,name,description')
            ->orderBy('rank')
            ->orderBy('appraisal_score', 'desc')
            ->get();
    }

    private function aggregatePdaNda(array $disMatrix): array
    {
        $aggregated = [];

        foreach ($disMatrix as $altId => $critValues) {
            $count = count($critValues);
            $aggregated[$altId] = $count > 0
                ? array_sum($critValues) / $count
                : 0.0;
        }

        return $aggregated;
    }

    private function validateAssessment(Assessment $assessment): void
    {
        if ($assessment->alternatives()->count() < 2) {
            throw new InvalidArgumentException("EDAS butuh minimal 2 alternatif. Tambah alternatif dulu sebelum menghitung EDAS.");
        }

        if (Criteria::count() === 0) {
            throw new InvalidArgumentException("Belum ada kriteria yang terdefinisi. Hubungi admin untuk menambahkan kriteria sebelum menghitung EDAS.");
        }

        if (! $assessment->isMatrixComplete()) {
            $altCount = $assessment->alternatives()->count();
            $critCount = Criteria::count();
            $filled = $assessment->alternativeValues()->count();
            $expected = $altCount * $critCount;

            throw new InvalidArgumentException(
                "Decision matrix belum lengkap. Diisi: {$filled}/{$expected} sel"
                .
                "({$altCount} alternatif x {$critCount} kriteria)."
            );
        }

        $totalWeight = Criteria::sum('weight');
        if (abs($totalWeight - 1.0) > 0.01) {
            throw new InvalidArgumentException(
                sprintf(
                    "Total bobot kriteria harus = 1.0 (saat ini: %.4f)."
                    .
                    "Sesuaikan bobot kriteria melalui admin.",
                    $totalWeight
                )
            );
        }
    }

    public function calculateRaw(
        array $rawMatrix,
        Collection $criteria,
    ): array {
        $altIds = array_keys($rawMatrix);

        $av = [];
        foreach ($criteria as $crit) {
            $sum = 0.0;
            foreach ($rawMatrix as $vals) {
                $sum += $vals[$crit->id] ?? 0.0;
            }
            $av[$crit->id] = count($rawMatrix) > 0 ? $sum / count($rawMatrix) : 0.0;
        }

        $pda = [];
        $nda = [];
        foreach ($altIds as $altId) {
            $pda[$altId] = [];
            $nda[$altId] = [];

            foreach ($criteria as $crit) {
                $x = $rawMatrix[$altId][$crit->id] ?? 0.0;
                $avg = $av[$crit->id];

                if (abs($avg) < PHP_FLOAT_EPSILON) {
                    $pda[$altId][$crit->id] = 0.0;
                    $nda[$altId][$crit->id] = 0.0;
                    continue;
                }

                if ($crit->isBenefit()) {
                    $pda[$altId][$crit->id] = max(0.0, ($x - $avg)) / $avg;
                    $nda[$altId][$crit->id] = max(0.0, ($avg - $x)) / $avg;
                } else {
                    $pda[$altId][$crit->id] = max(0.0, ($avg - $x)) / $avg;
                    $nda[$altId][$crit->id] = max(0.0, ($x - $avg)) / $avg;
                }
            }
        }

        $sp = [];
        $sn = [];
        foreach ($altIds as $altId) {
            $spSum = 0.0;
            $snSum = 0.0;

            foreach ($criteria as $crit) {
                $w = (float) $crit->weight;
                $spSum += $w * $pda[$altId][$crit->id];
                $snSum += $w * $nda[$altId][$crit->id];
            }

            $sp[$altId] = $spSum;
            $sn[$altId] = $snSum;
        }

        $maxSp = !empty($sp) ? max($sp) : 0.0;
        $maxSn = !empty($sn) ? max($sn) : 0.0;

        $nsp = [];
        $nsn = [];
        foreach ($altIds as $altId) {
            $nsp[$altId] = ($maxSp > PHP_FLOAT_EPSILON) ? $sp[$altId] / $maxSp : 0.0;
            $nsn[$altId] = ($maxSn > PHP_FLOAT_EPSILON) ? 1.0 - ($sn[$altId] / $maxSn) : 1.0;
        }

        $scores = [];
        foreach ($altIds as $altId) {
            $scores[$altId] = ($nsp[$altId] + $nsn[$altId]) / 2.0;
        }

        arsort($scores);
        $rank = 1;
        $ranked = [];

        foreach ($scores as $altId => $appraisalScore) {
            $ranked[$altId] = ['appraisal_score' => $appraisalScore, 'rank' => $rank++];
        }

        return compact('av', 'pda', 'nda', 'sp', 'sn', 'nsp', 'nsn', 'ranked');
    }
}
