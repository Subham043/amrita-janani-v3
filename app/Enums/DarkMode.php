<?php

namespace App\Enums;

enum DarkMode:int {
    case Yes = 1;
    case No = 0;

    public function value(): int
    {
        return $this->value;
    }
}
