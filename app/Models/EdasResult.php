<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class EdasResult extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'assessment_id',
        'alternative_id',
        'pda',
        'nda',
        'sp',
        'sn',
        'nsp',
        'nsn',
        'appraisal_score',
        'rank',
        'calculated_at',
    ];

    protected $casts = [
        'pda' => 'float',
        'nda' => 'float',
        'sp' => 'float',
        'sn' => 'float',
        'nsp' => 'float',
        'nsn' => 'float',
        'appraisal_score' => 'float',
        'rank' => 'integer',
        'calculated_at' => 'datetime',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function alternative(): BelongsTo
    {
        return $this->belongsTo(Alternative::class);
    }

    public function getAsScoreFormattedAttribute(): string
    {
        return number_format($this->appraisal_score, 4);
    }

    public function getQualityLabelAttribute(): string
    {
        return match (true) {
            $this->appraisal_score >= 0.80 => 'Sangat Direkomendasikan',
            $this->appraisal_score >= 0.60 => 'Direkomendasikan',
            $this->appraisal_score >= 0.40 => 'Cukup',
            $this->appraisal_score >= 0.20 => 'Kurang Direkomendasikan',
            default => 'Tidak Direkomendasikan',
        };
    }

    public function getQualityColorAttribute():string
    {
        return match (true) {
            $this->appraisal_score >= 0.80 => 'bg-success/10 text-success border border-success/30',
            $this->appraisal_score >= 0.60 => 'bg-info/10 text-info border border-info/30',
            $this->appraisal_score >= 0.40 => 'bg-warning/10 text-warning border border-warning/30',
            default => 'bg-danger/10 text-danger border border-danger/30',
        };
    }

    public function scopeRanked(Builder $query): Builder
    {
        return $query->orderBy('rank');
    }

    public function scopeTopRanked(Builder $query): Builder
    {
        return $query->where('rank', 1);
    }
}
