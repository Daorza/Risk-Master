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

    
}
