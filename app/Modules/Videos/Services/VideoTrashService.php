<?php

namespace App\Modules\Videos\Services;

use App\Modules\Videos\Models\VideoModel;
use Illuminate\Database\Eloquent\Builder;

class VideoTrashService extends VideoService
{
    public function model(): Builder
    {
        return VideoModel::withTrashed()->with(['User'])->whereNotNull('deleted_at');
    }
}

