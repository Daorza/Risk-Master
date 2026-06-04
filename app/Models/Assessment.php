<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Assessment extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    // Cast ke Carbon dari eloquent
    protected $casts = [
        'status' => 'string',
        'title' => 'encrypted',
        'description' => 'encrypted',
    ];

    // Default value
    protected $attributes = [
        'status' => self::STATUS_DRAFT,
    ];

    // Relasi
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alternatives(): BelongsToMany
    {
        return $this->belongsToMany(
            Alternative::class,
            'assessment_alternatives',
            'assessment_id',
            'alternative_id',
        );
    }

    public function alternativeValues(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'assessment_id');
    }

    public function edasResults(): HasMany
    {
        return $this->hasMany(EdasResult::class, 'assessment_id');
    }

    public function rankedResults(): HasMany
    {
        return $this->hasMany(EdasResult::class, 'assessment_id')->orderBy('rank');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'record_id')->where('table_name', 'assessments');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted(): bool
    {
        return $this->update(['status' => self::STATUS_COMPLETED]);
    }

    public function isMatrixComplete(): bool
    {
        $alternativeCount = $this->alternatives()->count();
        $criteriaCount = Criteria::count();
        $expectedTotal = $alternativeCount * $criteriaCount;

        if ($expectedTotal === 0) {
            return false;
        }

        $actualTotal = $this->alternativeValues()->count();

        return $actualTotal === $expectedTotal;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_DRAFT => 'Draft',
            default => ucfirst($this->Status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_COMPLETED => 'bg-success/10 text-success border border-success/30',
            self::STATUS_DRAFT => 'bg-warning/10 text-warning border border-warning/30',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithSummary(Builder $query): Builder
    {
        return $query->with([
            'owner:id,name,email',
            'alternatives:id,name',
        ])->withCount([
            'alternatives',
            'alternativeValues',
        ]);
    }

    public function scopeWithFullDetail(Builder $query): Builder
    {
        return $query->with([
            'owner:id,name,email',
            'alternatives',
            'alternativeValues.alternative:id,name',
            'alternativeValues.criteria:id,name,type,weight',
            'alternativeValues.inputBy:id,name',
        ]);
    }

    public function scopeWithResults(Builder $query): Builder
    {
        return $query->with([
            'owner:id,name,email',
            'rankedResults.alternative:id,name,description',
        ]);
    }
}
