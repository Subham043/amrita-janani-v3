<?php

namespace App\Modules\Audios\Services;

use App\Modules\Audios\Models\AudioModel;
use Illuminate\Database\Eloquent\Builder;

class AudioTrashService extends AudioService
{
    public function model(): Builder
    {
        return AudioModel::withTrashed()->with(['User'])->whereNotNull('deleted_at');
    }
}

