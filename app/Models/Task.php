<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'statement',
        'is_completed',
        'task_date',
        'priority',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
