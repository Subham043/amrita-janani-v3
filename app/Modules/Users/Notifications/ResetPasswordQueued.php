<?php

namespace App\Modules\Users\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword;

class ResetPasswordQueued extends ResetPassword implements ShouldQueue
{
    use Queueable;
}
