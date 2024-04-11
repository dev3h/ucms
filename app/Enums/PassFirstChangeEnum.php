<?php

declare(strict_types=1);

namespace App\Enums;

enum PassFirstChangeEnum: int
{
    case NOT_CHANGE = 0;
    case CHANGED = 1;
}