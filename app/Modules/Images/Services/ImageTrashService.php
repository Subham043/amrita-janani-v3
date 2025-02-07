<?php

namespace App\Modules\Images\Services;

use App\Modules\Images\Models\ImageModel;
use Illuminate\Database\Eloquent\Builder;

class ImageTrashService extends ImageService
{
    public function model(): Builder
    {
        return ImageModel::withTrashed()->with(['User'])->whereNotNull('deleted_at');
    }
}

