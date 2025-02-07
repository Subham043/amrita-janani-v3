<?php

namespace App\Modules\Documents\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Documents\Models\DocumentModel;
use App\Modules\Documents\Services\DocumentTrashService;
use App\Services\FileService;

class DocumentTrashController extends Controller
{
    public function __construct(private DocumentTrashService $documentTrashService){}

    public function viewTrash() {
        $data = $this->documentTrashService->paginate($request->total ?? 10);
        return view('pages.admin.document.list_trash')->with('data',$data);
    }

    public function restoreTrash($id){
        $data = $this->documentTrashService->getById($id);
        $data->restore();
        return redirect()->intended(route('document_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function restoreAllTrash(){
        $this->documentTrashService->model()->restore();
        return redirect()->intended(route('document_view_trash'))->with('success_status', 'Data Restored successfully.');
    }

    public function displayTrash($id) {
        $data = $this->documentTrashService->getById($id);
        return view('pages.admin.document.display_trash')->with('data',$data);
    }

    public function deleteTrash($id){
        $data = $this->documentTrashService->getById($id);
        (new FileService)->remove_file($data->document, ('app/private/'.(new DocumentModel)->file_path));
        $data->forceDelete();
        return redirect()->intended(route('document_view_trash'))->with('success_status', 'Data Deleted permanently.');
    }
}
