<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_data',
        'new_data',
        'ip_address',
    ];

    protected $casts = [
        'old_data' => 'encrypted:array',
        'new_data' => 'encrypted:array',
        'ip_address' => 'encrypted',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $action,
        string $tableName,
        ?int $recordId = null,
        ?array $oldData = null,
        ?array $newData = null,
    ): self {
        $userId = Auth::id();

        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()?->ip(),
        ]);
    }

    public function scopeForTable(Builder $query, string $table): Builder
    {
        return $query->where('table_name', $table);
    }

    public function scopeForRecord(Builder $query, string $table, int $id): Builder
    {
        return $query->where('table_name', $table)->where('record_id', $id);
    }
}
