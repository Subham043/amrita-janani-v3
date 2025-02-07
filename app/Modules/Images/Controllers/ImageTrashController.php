<?php

namespace App\Modules\Images\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Images\Models\ImageModel;
use App\Modules\Images\Services\ImageTrashService;
use App\Services\FileService;

class ImageTrashController extends Controller
{
    public function __construct(private ImageTrashService $imageTrashService){}

    public function viewTrash() {
        $data = $this->imageTrashService->paginate($request->total ?? 10);
        return view('pages.admin.image.list_trash')->with('data',$data);
    }

    public function restoreTrash($id){
        $data = $this->imageTrashService->getById($id);
        $data->restore();
        return redirect()->intended(route('image_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function restoreAllTrash(){
        $this->imageTrashService->model()->restore();
        return redirect()->intended(route('image_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function displayTrash($id) {
        $data = $this->imageTrashService->getById($id);
        return view('pages.admin.image.display_trash')->with('data',$data);
    }

    public function deleteTrash($id){
        $data = $this->imageTrashService->getById($id);
        (new FileService)->remove_file($data->image, ('app/private/'.(new ImageModel)->file_path));
        (new FileService)->remove_file('compressed-'.$data->image, ('app/private/'.(new ImageModel)->file_path));
        $data->forceDelete();
        return redirect()->intended(route('image_view_trash'))->with('success_status', 'Data Deleted permanently.');
    }
}
