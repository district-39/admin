<?php

namespace App\Models;

use App\Enums\Days;
use Database\Factories\GroupMeetingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMeeting extends Model
{
    /** @use HasFactory<GroupMeetingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'day' => Days::class,
        ];
    }
}
