<?php

namespace App\Models;

use App\Enums\EmailStatus;
use Database\Factories\EmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['subject', 'content', 'to', 'from', 'cc', 'bcc', 'status', 'date_sent'])]
class Email extends Model
{
    /** @use HasFactory<EmailFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => EmailStatus::class,
            'date_sent' => 'datetime',
        ];
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)->withTimestamps();
    }
}
