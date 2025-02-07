<?php

namespace App\Modules\Audios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Audios\Models\AudioModel;
use App\Modules\Audios\Services\AudioTrashService;
use App\Services\FileService;

class AudioTrashController extends Controller
{
    public function __construct(private AudioTrashService $audioTrashService){}

    public function viewTrash() {
        $data = $this->audioTrashService->paginate($request->total ?? 10);
        return view('pages.admin.audio.list_trash')->with('data',$data);
    }

    public function restoreTrash($id){
        $data = $this->audioTrashService->getById($id);
        $data->restore();
        return redirect()->intended(route('audio_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function restoreAllTrash(){
        $this->audioTrashService->model()->restore();
        return redirect()->intended(route('audio_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function displayTrash($id) {
        $data = $this->audioTrashService->getById($id);
        return view('pages.admin.audio.display_trash')->with('data',$data);
    }

    public function deleteTrash($id){
        $data = $this->audioTrashService->getById($id);
        (new FileService)->remove_file($data->audio, ('app/private/'.(new AudioModel)->file_path));
        $data->forceDelete();
        return redirect()->intended(route('audio_view_trash'))->with('success_status', 'Data Deleted permanently.');
    }
}
