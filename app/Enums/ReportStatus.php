<?php

namespace App\Enums;

enum ReportStatus:int {
    case Pending = 0;
    case InProgress = 1;
    case Completed = 2;

    public function value(): int
    {
        return $this->value;
    }
}
