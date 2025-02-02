<?php

namespace App\Enums;

enum Status:int {
    case Active = 1;
    case Inactive = 0;

    public function value(): int
    {
        return $this->value;
    }
}
