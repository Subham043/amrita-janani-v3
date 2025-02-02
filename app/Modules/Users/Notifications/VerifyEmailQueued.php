<?php

namespace App\Modules\Users\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;
}
