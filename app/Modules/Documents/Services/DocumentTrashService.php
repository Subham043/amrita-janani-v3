<?php

namespace App\Modules\Documents\Services;

use App\Modules\Documents\Models\DocumentModel;
use Illuminate\Database\Eloquent\Builder;

class DocumentTrashService extends DocumentService
{
    public function model(): Builder
    {
        return DocumentModel::withTrashed()->with(['User'])->whereNotNull('deleted_at');
    }
}

