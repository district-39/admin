<?php

namespace App\Enums;

enum EmailStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Failed = 'failed';
}
