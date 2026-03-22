<?php

namespace App\Models;

use Database\Factories\DistrictEmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject', 'content', 'to', 'from', 'cc', 'bcc', 'status', 'date_sent', 'meeting_id', 'district_meeting_id'])]
class DistrictEmail extends Email
{
    /** @use HasFactory<DistrictEmailFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('type', function ($query) {
            $query->where('type', 'district_email');
        });

        static::creating(function (DistrictEmail $email) {
            $email->type = 'district_email';
        });
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function districtMeeting(): BelongsTo
    {
        return $this->belongsTo(DistrictMeeting::class);
    }
}
