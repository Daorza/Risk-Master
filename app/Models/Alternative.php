<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Alternative extends Model
{
    use HasFactory;

    const SOURCE_ADMIN = 'admin';
    const SOURCE_USER = 'user';

    protected $fillable = [
        'name',
        'description',
        'source',
        'created_by',
    ];

    protected $casts = [
        'name' => 'encrypted',
        'description' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::saving(function (Alternative $model) {
            if ($model->isDirty('name') || empty($model->name_hash)) {
                $model->name_hash = hash_hmac(
                    'sha256',
                    mb_strtolower(trim($model->getAttributes()['name'] ?? '')),
                    config('app.key'),
                );
            }
        });
    }

    protected $attributes = [
        'source' => self::SOURCE_ADMIN,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(
            Assessment::class,
            'assessment_alternatives',
            'alternative_id',
            'assessment_id',
        );
    }

    public function values(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'alternative_id');
    }

    public function edasResults(): HasMany
    {
        return $this->hasMany(EdasResult::class, 'alternative_id');
    }

    public function scopeFromAdmin(Builder $query): Builder
    {
        return $query->where('source', self::SOURCE_ADMIN);
    }

    public function scopeFromUser(Builder $query): Builder
    {
        return $query->where('source', self::SOURCE_USER);
    }

    public function scopeCreatedBy(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function scopeSearchByName(Builder $query, string $keyword): Builder
    {
        $hash = hash_hmac('sha256', mb_strtolower(trim($keyword)), config('app.key'));

        return $query->where('name_hash', $hash);
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_ADMIN => 'Template Admin',
            self::SOURCE_USER => 'Input User',
            default => ucfirst($this->source),
        };
    }
}
