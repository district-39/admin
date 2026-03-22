<?php

namespace App\Models;

use Database\Factories\NoteSectionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['district_meeting_id', 'title', 'committee', 'order', 'json', 'text', 'markdown'])]
class NoteSection extends Model
{
    /** @use HasFactory<NoteSectionFactory> */
    use HasFactory;

    public function districtMeeting(): BelongsTo
    {
        return $this->belongsTo(DistrictMeeting::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'json' => 'array',
            'order' => 'integer',
        ];
    }
}
