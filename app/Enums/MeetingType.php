<?php

namespace App\Enums;

enum MeetingType: string
{
    case District = 'district';
    case Intergroup = 'intergroup';
    case Area = 'area';
    case Group = 'group';
    case Other = 'other';
}
