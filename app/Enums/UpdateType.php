<?php

namespace App\Enums;

enum UpdateType: string
{
    case Intergroup = 'intergroup';
    case District = 'district';
    case Area = 'area';
    case General = 'general';
}
