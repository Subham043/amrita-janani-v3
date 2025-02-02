<?php

namespace App\Enums;

enum UserType:int {
    case Admin = 1;
    case User = 2;

    public function value(): int
    {
        return $this->value;
    }
}
