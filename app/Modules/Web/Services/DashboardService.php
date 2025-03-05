<?php

namespace App\Modules\Web\Services;

use App\Enums\Status;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Images\Models\ImageModel;
use App\Modules\SearchHistories\Models\SearchHistory;
use App\Modules\Videos\Models\VideoModel;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class DashboardService
{
    public function customBuilder(Builder $model): Builder
    {
        $search = request()->query('filter')['search'] ?? NULL;
        $sort = request()->query('sort') ?? 'id';
        $sort_type = str_contains($sort, '-') ? 'desc' : 'asc';
        
        $model->where('status', Status::Active->value());
        $model->orderBy(str_replace('-', '', $sort), $sort_type);
        if($search){
            $model->where('title', 'like', '%' . $search . '%');
        }
        return $model;
    }
    public function imageModel(): Builder
    {
        $query = ImageModel::query()->selectRaw('id, title, uuid, description_unformatted, image as file, created_at, "IMAGE" as type');
        return $this->customBuilder($query);
    }
    
    public function audioModel(): Builder
    {
        $query = AudioModel::query()->selectRaw('id, title, uuid, description_unformatted, audio as file, created_at, "AUDIO" as type');
        return $this->customBuilder($query);
    }
    
    public function documentModel(): Builder
    {
        $query = DocumentModel::query()->selectRaw('id, title, uuid, description_unformatted, document as file, created_at, "DOCUMENT" as type');
        return $this->customBuilder($query);
    }
    
    public function videoModel(): Builder
    {
        $query = VideoModel::query()->selectRaw('id, title, uuid, description_unformatted, video as file, created_at, "VIDEO" as type');
        return $this->customBuilder($query);
    }
    
    public function model(): Builder
    {
        $imageQuery = $this->imageModel();
        $audioQuery = $this->audioModel();
        $documentQuery = $this->documentModel();
        $videoQuery = $this->videoModel();
        $query = $imageQuery->unionAll($audioQuery)->unionAll($documentQuery)->unionAll($videoQuery);
        return $this->customBuilder($query);
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::for($this->model())
            ->allowedFilters([
                AllowedFilter::custom('search', new CommonFilter, null, false),
            ]);
    }

    public function paginate(Int $total = 12): LengthAwarePaginator
	{
		$contents = $this->query()
			->paginate($total)
			->appends(request()->query());
        
        $data = $contents->through(function($content) {
            if($content->type == 'IMAGE'){
                $content->file_link = (!is_null($content->file) && Storage::exists((new ImageModel)->file_path.$content->file)) ? URL::temporarySignedRoute(
                    'content_image_thumbnail_file',
                    now()->addMinutes(5),
                    ['uuid' => $content->uuid, 'compressed' => true]
                ) : null;

                $content->route = route('content_image_view', ['uuid' => $content->uuid]); 
            }
            if($content->type == 'AUDIO'){
                $content->file_link = Vite::asset('resources/images/audio-book.webp');

                $content->route = route('content_audio_view', ['uuid' => $content->uuid]);
            }
            if($content->type == 'DOCUMENT'){
                $content->file_link = Vite::asset('resources/images/pdf.webp');

                $content->route = route('content_document_view', ['uuid' => $content->uuid]);
            }
            if($content->type == 'VIDEO'){
                if(strpos($content->file,'vimeo') !== false){
                    $content->file_link = 'https://vumbnail.com/'.$this->getVideoId($content->file).'.jpg';
                }else{
                    $content->file_link = 'https://i3.ytimg.com/vi/'.$this->getVideoId($content->file).'/maxresdefault.jpg';
                }

                $content->route = route('content_video_view', ['uuid' => $content->uuid]);
            }
            return $content;
        });
        return $data;
	}

    public function getVideoId($link){
        if(strpos($link,'vimeo') !== false){
            if(preg_match("/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/?(showcase\/)*([0-9))([a-z]*\/)*([0-9]{6,11})[?]?.*/", $link, $output_array)) {
                return $output_array[6];
            }
        }else{
            $video_id = explode("/embed/", $link);
            $video_id = $video_id[1];
            return $video_id;
        }
    }
}

class CommonFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        defer(function() use($value){
            SearchHistory::create([
                'search' => $value,
                'screen' => 1,
                'user_id' => Auth::user()->id,
            ]);
        });
        $query->where('title', 'like', '%' . $value . '%');
    }
}