<?php

namespace App\Models;

use App\Enums\EventStatus;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'description', 'date', 'start_time', 'end_time', 'location', 'status', 'submitted_by'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => EventStatus::class,
        ];
    }

    public function meetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class)->withPivot('notes')->withTimestamps();
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function emails(): BelongsToMany
    {
        return $this->belongsToMany(Email::class)->withTimestamps();
    }
}
