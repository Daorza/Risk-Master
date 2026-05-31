<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Criteria extends Model
{
    use HasFactory;

    const TYPE_BENEFIT = 'benefit';
    const TYPE_COST = 'cost';

    protected $fillable = [
        'name',
        'description',
        'type',
        'weight',
    ];

    protected $casts = [
        'weight' => 'float',
    ];

    public function alternativeValues(): HasMany
    {
        return $this->hasMany(AlternativeValue::class, 'criteria_id');
    }

    public function scopeBenefits(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BENEFIT);
    }

    public function scopeCosts(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_COST);
    }

    public function isBenefit(): bool
    {
        return $this->type === self::TYPE_BENEFIT;
    }

    public function getWeightPercentAttribute(): string
    {
        return number_format($this->weight * 100, 1) . '%';
    }
}
