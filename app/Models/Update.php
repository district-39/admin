<?php

namespace App\Models;

use App\Enums\UpdateType;
use Database\Factories\UpdateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Update extends Model
{
    /** @use HasFactory<UpdateFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'update_type' => UpdateType::class,
        ];
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
