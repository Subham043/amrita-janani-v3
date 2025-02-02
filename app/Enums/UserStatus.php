<?php

namespace App\Enums;

enum UserStatus:int {
    case Active = 1;
    case Blocked = 2;

    public function value(): int
    {
        return $this->value;
    }
}
